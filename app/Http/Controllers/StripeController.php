<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Room;
use App\Models\SessionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller
{
    // Extension pricing in pence (GBP) — admin-editable in future
    const EXTENSION_PRICES = [
        '30' => 2000,  // £20.00
        '60' => 3500,  // £35.00
    ];

    const EXTENSION_WINDOW_HOURS  = 24;
    const EXTENSION_LOCK_MINUTES  = 10;

    /**
     * Party A checkout — full or split (their half)
     */
    public function checkoutPartyA(Room $room)
    {
        abort_if($room->party_a_id != auth()->id(), 403);
        abort_if($room->party_a_paid == true, 400, 'Already paid.');

        $package = SessionPackage::forDuration($room->duration);
        abort_if(!$package, 400, 'Session package not found.');

        $amount = $room->payment_type === 'split' ? $package->split_price_pence : $package->full_price_pence;
        $label  = $room->payment_type === 'split' ? 'Your half' : 'Full session';

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => \App\Models\PlatformSetting::currencyCode(),
                    'unit_amount'  => $amount,
                    'product_data' => [
                        'name' => "FirstMediator — {$package->name} session ({$label})",
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode'        => 'payment',
            'success_url' => route('payment.success', $room->uuid) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('rooms.show', $room->uuid),
            'metadata'    => [
                'room_id' => $room->id,
                'party'   => 'party_a',
                'user_id' => auth()->id(),
                'type'    => 'session',
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
                    'currency'     => \App\Models\PlatformSetting::currencyCode(),
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

        $package = SessionPackage::forDuration($room->duration);
        abort_if(!$package, 400, 'Session package not found.');

        $amount = $package->split_price_pence;

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency'     => \App\Models\PlatformSetting::currencyCode(),
                    'unit_amount'  => $amount,
                    'product_data' => [
                        'name' => "FirstMediator — {$package->name} session (Your half)",
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
                'user_id' => auth()->user()?->id ?? null,
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

        // Check if token is provided
        if (!$request->has('token')) {
            \Log::warning("Party B payment access without token for room {$uuid}");
            abort(403, 'Payment token required.');
        }

        // Check if room has a payment token set
        if (!$room->party_b_payment_token) {
            \Log::error("Room {$uuid} has no party_b_payment_token set");
            abort(500, 'Payment link not configured. Please contact the session creator.');
        }

        // Log for debugging
        \Log::info("Party B payment page access", [
            'room_uuid' => $uuid,
            'provided_token' => $request->token,
            'stored_token' => $room->party_b_payment_token,
            'tokens_match' => $room->party_b_payment_token === $request->token,
        ]);

        abort_if($room->party_b_payment_token !== $request->token, 403, 'Invalid payment link.');

        // Check if token has expired or is null
        if (!$room->party_b_payment_expires_at || $room->party_b_payment_expires_at < now()) {
            return view('payment.expired', compact('room'));
        }

        // If already paid, redirect to room
        if ($room->party_b_paid) {
            return redirect()->route('rooms.show', $uuid);
        }

        $package = \App\Models\SessionPackage::forDuration($room->duration);
        $amount  = $package ? $package->split_price : 0;

        return view('payment.party-b', compact('room', 'amount'));
    }
}
