<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.webhook_secret')
            );
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $this->handleCheckoutCompleted($event->data->object);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function handleCheckoutCompleted($session): void
    {
        $roomId = $session->metadata->room_id;
        $party  = $session->metadata->party;
        $userId = $session->metadata->user_id;
        $type   = $session->metadata->type ?? 'session';

        $room = Room::find($roomId);
        if (!$room) return;

        // --- Handle extension payment ---
        if ($type === 'extension') {
            $minutes = (int) ($session->metadata->extension_minutes ?? 30);

            // Mark billing paid
            Billing::where('room_id', $roomId)
                ->where('stripe_session_id', $session->id)
                ->update([
                    'status'                   => 'paid',
                    'stripe_payment_intent_id' => $session->payment_intent,
                    'paid_at'                  => now(),
                ]);

            // Add minutes to Redis timer and reactivate room
            $timerKey = "room:{$room->id}:timer";
            \Illuminate\Support\Facades\Redis::incrby($timerKey, $minutes * 60);

            $room->update([
                'status'              => 'active',
                'extension_minutes'   => $room->extension_minutes + $minutes,
                'extension_locked_by' => null,
                'extension_locked_at' => null,
                'timer_expired_at'    => null,
                'extension_deadline'  => null,
            ]);

            Log::info("Room {$room->uuid} extended by {$minutes} minutes");
            return;
        }

        // --- Handle regular session payment ---
        Billing::where('room_id', $roomId)
            ->where('party', $party)
            ->whereNull('paid_at')
            ->update([
                'status'                    => 'paid',
                'stripe_session_id'         => $session->id,
                'stripe_payment_intent_id'  => $session->payment_intent,
                'paid_at'                   => now(),
                'user_id'                   => $userId ?: ($party === 'party_a' ? $room->party_a_id : 0),
            ]);

        if ($party === 'party_a') {
            $room->update(['party_a_paid' => true]);

            if ($room->payment_type === 'full') {
                $room->update(['status' => 'pending']);
                $this->sendPartyBInvite($room);
                Log::info("Room {$room->uuid} activated — full payment by Party A");
            } else {
                $room->update(['status' => 'awaiting_party_b_payment']);
                $this->sendPartyBPaymentLink($room);
                Log::info("Room {$room->uuid} awaiting Party B payment");
            }
        }

        if ($party === 'party_b') {
            $room->update([
                'party_b_paid' => true,
                'status'       => 'pending',
            ]);
            Log::info("Room {$room->uuid} activated — Party B paid");
        }
    }

    protected function sendPartyBInvite(Room $room): void
    {
        try {
            Mail::to($room->party_b_email)->send(new \App\Mail\RoomInvitation($room));
        } catch (\Exception $e) {
            Log::error('Failed to send Party B invite: ' . $e->getMessage());
        }
    }

    protected function sendPartyBPaymentLink(Room $room): void
    {
        $paymentUrl = route('payment.party-b.checkout', [
            'uuid'  => $room->uuid,
            'token' => $room->party_b_payment_token,
        ]);

        try {
            Mail::send('emails.party-b-payment', compact('room', 'paymentUrl'), function ($m) use ($room) {
                $m->to($room->party_b_email)
                  ->subject('Action Required — Complete your payment to join the mediation session');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send Party B payment link: ' . $e->getMessage());
        }
    }
}
