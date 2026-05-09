<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Room;
use App\Models\TopupPackage;
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(private SubscriptionService $subscriptionService) {}

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

        match ($event->type) {
            'checkout.session.completed'      => $this->handleCheckoutCompleted($event->data->object),
            'invoice.payment_succeeded'       => $this->handleSubscriptionRenewal($event->data->object),
            'customer.subscription.deleted'   => $this->handleSubscriptionCancelled($event->data->object),
            'customer.subscription.updated'   => $this->handleSubscriptionUpdated($event->data->object),
            default                           => null,
        };

        return response()->json(['status' => 'ok']);
    }

    // ─────────────────────────────────────────────────────────────────
    // checkout.session.completed
    // ─────────────────────────────────────────────────────────────────
    protected function handleCheckoutCompleted($session): void
    {
        $type = $session->metadata->type ?? 'session';

        match ($type) {
            'subscription' => $this->handleSubscriptionCheckout($session),
            'topup'        => $this->handleTopupCheckout($session),
            'extension'    => $this->handleExtensionCheckout($session),
            default        => $this->handleSessionCheckout($session),
        };
    }

    protected function handleSubscriptionCheckout($session): void
    {
        $userId = $session->metadata->user_id ?? null;
        $planId = $session->metadata->plan_id ?? null;
        $cycle  = $session->metadata->billing_cycle ?? 'monthly';

        $user = User::find($userId);
        $plan = \App\Models\SubscriptionPlan::find($planId);

        if (!$user || !$plan) return;

        // Create subscription record
        $sub = UserSubscription::create([
            'user_id'               => $user->id,
            'plan_id'               => $plan->id,
            'stripe_subscription_id'=> $session->subscription,
            'stripe_customer_id'    => $session->customer,
            'status'                => 'active',
            'billing_cycle'         => $cycle,
            'current_period_start'  => now(),
            'current_period_end'    => match ($cycle) {
                'quarterly' => now()->addMonths(3),
                'yearly'    => now()->addYear(),
                default     => now()->addMonth(),
            },
        ]);

        // Grant initial credits
        $this->subscriptionService->grantCredits(
            $user,
            (float) $plan->credits_per_cycle,
            'subscription_grant',
            "{$plan->name} Plan — initial grant (£{$plan->credits_per_cycle})"
        );

        Log::info("Subscription created for user {$user->id}, plan {$plan->name}");
    }

    protected function handleTopupCheckout($session): void
    {
        $userId    = $session->metadata->user_id ?? null;
        $packageId = $session->metadata->package_id ?? null;

        $user    = User::find($userId);
        $package = TopupPackage::find($packageId);

        if (!$user || !$package) return;

        $total = $package->totalCredits();

        $this->subscriptionService->grantCredits(
            $user,
            $total,
            'topup',
            "{$package->label} Top-up — £{$package->price} (+£{$package->bonus_credits} bonus)"
        );

        Log::info("Top-up completed for user {$user->id}, package {$package->label}, credits {$total}");
    }

    protected function handleExtensionCheckout($session): void
    {
        $roomId  = $session->metadata->room_id;
        $minutes = (int) ($session->metadata->extension_minutes ?? 30);

        $room = Room::find($roomId);
        if (!$room) return;

        Billing::where('room_id', $roomId)
            ->where('stripe_session_id', $session->id)
            ->update([
                'status'                   => 'paid',
                'stripe_payment_intent_id' => $session->payment_intent,
                'paid_at'                  => now(),
            ]);

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
    }

    protected function handleSessionCheckout($session): void
    {
        $roomId = $session->metadata->room_id;
        $party  = $session->metadata->party;
        $userId = $session->metadata->user_id;

        $room = Room::find($roomId);
        if (!$room) return;

        Billing::where('room_id', $roomId)
            ->where('party', $party)
            ->whereNull('paid_at')
            ->update([
                'status'                   => 'paid',
                'stripe_session_id'        => $session->id,
                'stripe_payment_intent_id' => $session->payment_intent,
                'paid_at'                  => now(),
                'user_id'                  => $userId ?: ($party === 'party_a' ? $room->party_a_id : 0),
            ]);

        if ($party === 'party_a') {
            $room->update(['party_a_paid' => true]);
            
            Log::info("Party A payment completed for room {$room->uuid}", [
                'payment_type' => $room->payment_type,
                'party_b_email' => $room->party_b_email,
            ]);

            if ($room->payment_type === 'full') {
                // Full payment - Party B can join immediately
                $room->update(['status' => 'pending', 'party_b_paid' => true]);
                Log::info("Sending Party B invite for full payment room {$room->uuid}");
                $this->sendPartyBInvite($room);
            } else {
                // Split payment - Party B needs to pay
                // Ensure payment token and expiry are set
                if (!$room->party_b_payment_token) {
                    $room->update([
                        'party_b_payment_token' => \Illuminate\Support\Str::random(64),
                        'party_b_payment_expires_at' => now()->addDays(7),
                    ]);
                    $room->refresh();
                }
                
                $room->update(['status' => 'awaiting_party_b_payment']);
                Log::info("Sending Party B payment link for split payment room {$room->uuid}", [
                    'has_token' => !empty($room->party_b_payment_token),
                    'has_expiry' => !empty($room->party_b_payment_expires_at),
                ]);
                $this->sendPartyBPaymentLink($room);
            }
        }

        if ($party === 'party_b') {
            $updates = ['party_b_paid' => true, 'status' => 'pending'];
            
            // If Party B is logged in and not yet assigned, assign them
            if ($userId && !$room->party_b_id) {
                $updates['party_b_id'] = $userId;
            }
            
            $room->update($updates);
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // invoice.payment_succeeded — subscription renewal
    // ─────────────────────────────────────────────────────────────────
    protected function handleSubscriptionRenewal($invoice): void
    {
        if ($invoice->billing_reason !== 'subscription_cycle') return;

        $sub = UserSubscription::where('stripe_subscription_id', $invoice->subscription)->first();
        if (!$sub) return;

        // Update period dates from Stripe
        try {
            Stripe::setApiKey(config('services.stripe.secret'));
            $stripeSub = \Stripe\Subscription::retrieve($invoice->subscription);
            $sub->update([
                'status'               => 'active',
                'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSub->current_period_start),
                'current_period_end'   => \Carbon\Carbon::createFromTimestamp($stripeSub->current_period_end),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve Stripe subscription: ' . $e->getMessage());
        }

        $this->subscriptionService->handleRenewal($sub);
    }

    // ─────────────────────────────────────────────────────────────────
    // customer.subscription.deleted — cancellation
    // ─────────────────────────────────────────────────────────────────
    protected function handleSubscriptionCancelled($stripeSub): void
    {
        $sub = UserSubscription::where('stripe_subscription_id', $stripeSub->id)->first();
        if (!$sub) return;

        $sub->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        Log::info("Subscription cancelled for user {$sub->user_id}");
    }

    // ─────────────────────────────────────────────────────────────────
    // customer.subscription.updated — upgrade/downgrade
    // ─────────────────────────────────────────────────────────────────
    protected function handleSubscriptionUpdated($stripeSub): void
    {
        $sub = UserSubscription::where('stripe_subscription_id', $stripeSub->id)->first();
        if (!$sub) return;

        $sub->update(['status' => $stripeSub->status]);
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
            Log::info("Successfully sent Party B payment link to {$room->party_b_email}");
        } catch (\Exception $e) {
            Log::error('Failed to send Party B payment link: ' . $e->getMessage());
        }
    }
}
