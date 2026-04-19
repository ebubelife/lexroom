<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Room;
use Illuminate\Http\Request;
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

    /**
     * Party A checkout — full or split (their half)
     */
    public function checkoutPartyA(Room $room)
    {
        abort_if($room->party_a_id !== auth()->id(), 403);
        abort_if($room->party_a_paid, 400, 'Already paid.');

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
                'room_id' => $room->id,
                'party'   => 'party_a',
                'user_id' => auth()->id(),
            ],
        ]);

        // Update billing record with session ID
        Billing::where('room_id', $room->id)
            ->where('party', 'party_a')
            ->update(['stripe_session_id' => $session->id]);

        return redirect($session->url);
    }

    /**
     * Party B checkout — via payment link token
     */
    public function checkoutPartyB(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();

        // Validate token
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
        $amount = $prices['split'] / 100; // convert cents to dollars

        return view('payment.party-b', compact('room', 'amount'));
    }
}
