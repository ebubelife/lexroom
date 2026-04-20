<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    // Pricing in cents (USD)
    const PRICES = [
        '30' => ['full' => 450,  'split' => 225,  'plan' => 'starter'],
        '60' => ['full' => 750,  'split' => 375,  'plan' => 'standard'],
        '90' => ['full' => 1000, 'split' => 500,  'plan' => 'extended'],
    ];

    // Extension pricing in cents
    const EXTENSION_PRICES = [
        '30' => 250,  // $2.50
        '60' => 450,  // $4.50
    ];

    // Extension window in hours
    const EXTENSION_WINDOW_HOURS = 24;

    // Extension lock timeout in minutes
    const EXTENSION_LOCK_MINUTES = 10;

    /**
     * Party A checkout — full or split (their half)
     */
    public function checkoutPartyA(Room $room)
    {
        abort_if($room->party_a_id != auth()->id(), 403);
        abort_if($room->party_a_paid == true, 400, 'Already paid.');

        Stripe::setApiKey(config('services.stripe.secret'));

        $prices   = self::PRICES[$room->duration];
        $amount   = $room->payment_type === 'split' ? $prices['split'] : $prices['full'];
        $label    = $room->payment_type === 'split' ? 'Your half' : 'Full session';

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'unit_amount'  => $amount,
                    'product_data' => [
                        'name' => "FirstMediator — {$prices['plan']} session ({$label})",
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('payment.success', $room->uuid) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('rooms.show', $room->uuid),
            'metadata'    => [
                'room_id'  => $room->id,
                'party'    => 'party_a',
                'user_id'  => auth()->id(),
                'type'     => 'session',
            ],
        ]);

        Billing::where('room_id', $room->id)
            ->where('party', 'party_a')
            ->update(['stripe_session_id' => $session->id]);

        return redirect($session->url);
    }

    /**
     * Session extension checkout — either party can pay
     */
    public function checkoutExtension(Request $request, Room $room)
    {
        $request->validate(['minutes' => 'required|in:30,60']);

        // Must be party A or party B
        $isPartyA = $room->party_a_id == auth()->id();
        $isPartyB = $room->party_b_id == auth()->id();
        abort_if(!$isPartyA && !$isPartyB, 403);

        // Room must be timer_expired and within extension window
        abort_if($room->status !== 'timer_expired', 400, 'Session is not awaiting extension.');
        abort_if($room->extension_deadline && $room->extension_deadline < now(), 410, 'Extension window has closed.');

        // Race condition lock — use DB transaction
        $locked = DB::transaction(function () use ($room) {
            $fresh = Room::lockForUpdate()->find($room->id);

            // Check if already locked by someone else within 10 minutes
            if (
                $fresh->extension_locked_by &&
                $fresh->extension_locked_by != auth()->id() &&
                $fresh->extension_locked_at &&
                $fresh->extension_locked_at->gt(now()->subMinutes(self::EXTENSION_LOCK_MINUTES))
            ) {
                return false;
            }

            // Acquire lock
            $fresh->update([
                'extension_locked_by' => auth()->id(),
                'extension_locked_at' => now(),
            ]);

            return true;
        });

        if (!$locked) {
            return back()->with('error', 'The other party is currently processing an extension. Please wait a few minutes.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $minutes = $request->minutes;
        $amount  = self::EXTENSION_PRICES[$minutes];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'unit_amount'  => $amount,
                    'product_data' => [
                        'name' => "FirstMediator — Session Extension (+{$minutes} minutes)",
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('payment.success', $room->uuid) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('rooms.show', $room->uuid),
            'metadata'    => [
                'room_id'            => $room->id,
                'party'              => $isPartyA ? 'party_a' : 'party_b',
                'user_id'            => auth()->id(),
                'type'               => 'extension',
                'extension_minutes'  => $minutes,
            ],
        ]);

        // Log billing record
        Billing::create([
            'room_id'           => $room->id,
            'user_id'           => auth()->id(),
            'party'             => $isPartyA ? 'party_a' : 'party_b',
            'amount'            => $amount / 100,
            'plan'              => "extension_{$minutes}min",
            'stripe_session_id' => $session->id,
            'status'            => 'pending',
        ]);

        return redirect($session->url);
    }

    /**
     * Party B checkout — via payment link token
     */
    public function checkoutPartyB(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();

        abort_if($room->party_b_payment_token !== $request->token, 403, 'Invalid payment link.');
        abort_if($room->party_b_payment_expires_at < now(), 410, 'Payment link has expired.');
        abort_if($room->party_b_paid, 400, 'Already paid.');

        Stripe::setApiKey(config('services.stripe.secret'));

        $prices = self::PRICES[$room->duration];
        $amount = $prices['split'];

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'unit_amount'  => $amount,
                    'product_data' => [
                        'name' => "FirstMediator — {$prices['plan']} session (Your half)",
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('payment.party-b.success', $uuid) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('payment.party-b.checkout', ['uuid' => $uuid, 'token' => $request->token]),
            'metadata'    => [
                'room_id' => $room->id,
                'party'   => 'party_b',
                'user_id' => auth()->user()?->id ?? 0,
                'type'    => 'session',
            ],
        ]);

        Billing::where('room_id', $room->id)
            ->where('party', 'party_b')
            ->update(['stripe_session_id' => $session->id]);

        return redirect($session->url);
    }

    /**
     * Party A payment success page
     */
    public function successPartyA(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        return view('payment.success', compact('room'));
    }

    /**
     * Party B payment success page
     */
    public function successPartyB(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        return view('payment.success-party-b', compact('room'));
    }

    /**
     * Party B payment page (shown before Stripe redirect)
     */
    public function partyBPaymentPage(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();

        abort_if($room->party_b_payment_token !== $request->token, 403, 'Invalid payment link.');

        if ($room->party_b_payment_expires_at < now()) {
            return view('payment.expired', compact('room'));
        }

        if ($room->party_b_paid) {
            return redirect()->route('rooms.show', $uuid);
        }

        $prices = self::PRICES[$room->duration];
        $amount = $prices['split'] / 100;

        return view('payment.party-b', compact('room', 'amount'));
    }
}
