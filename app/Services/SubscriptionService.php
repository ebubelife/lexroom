<?php

namespace App\Services;

use App\Models\CreditSetting;
use App\Models\CreditTransaction;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    public function getActiveSub(User $user): ?UserSubscription
    {
        return $user->activeSubscription()->with('plan')->first();
    }

    public function hasActiveSubscription(User $user): bool
    {
        return $this->getActiveSub($user) !== null;
    }

    public function hasEnoughCredits(User $user, float $amount): bool
    {
        $wallet = $user->wallet;
        return $wallet && $wallet->hasEnoughCredits($amount);
    }

    public function deductCredits(
        User $user,
        float $amount,
        string $type,
        string $description,
        ?int $roomId = null
    ): bool {
        $wallet = $user->wallet;

        if (!$wallet || !$wallet->hasEnoughCredits($amount)) {
            return false;
        }

        $wallet->deductCredits($amount);
        $wallet->refresh();

        CreditTransaction::create([
            'user_id'      => $user->id,
            'amount'       => -$amount,
            'type'         => $type,
            'description'  => $description,
            'room_id'      => $roomId,
            'balance_after'=> $wallet->totalBalance(),
        ]);

        return true;
    }

    public function grantCredits(
        User $user,
        float $amount,
        string $type,
        string $description,
        ?int $roomId = null
    ): void {
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = $user->wallet()->create([
                'credits_balance'  => 0,
                'referral_credits' => 0,
                'escrow_balance'   => 0,
            ]);
        }

        $wallet->addCredits($amount);
        $wallet->refresh();

        CreditTransaction::create([
            'user_id'      => $user->id,
            'amount'       => $amount,
            'type'         => $type,
            'description'  => $description,
            'room_id'      => $roomId,
            'balance_after'=> $wallet->totalBalance(),
        ]);
    }

    public function grantReferralCredits(User $user, float $amount): void
    {
        $wallet = $user->wallet;
        if (!$wallet) return;

        $wallet->addReferralCredits($amount);
        $wallet->refresh();

        CreditTransaction::create([
            'user_id'      => $user->id,
            'amount'       => $amount,
            'type'         => 'referral_reward',
            'description'  => "Referral reward — £{$amount} credits",
            'balance_after'=> $wallet->totalBalance(),
        ]);
    }

    public function handleRenewal(UserSubscription $sub): void
    {
        $user   = $sub->user;
        $plan   = $sub->plan;
        $expire = CreditSetting::get('credits_expire_on_renewal', 'true') === 'true';

        $wallet = $user->wallet;
        if (!$wallet) return;

        if ($expire) {
            // Reset to exactly plan credits
            $old = $wallet->credits_balance;
            $wallet->update(['credits_balance' => $plan->credits_per_cycle]);

            CreditTransaction::create([
                'user_id'      => $user->id,
                'amount'       => (float) $plan->credits_per_cycle - (float) $old,
                'type'         => 'subscription_grant',
                'description'  => "{$plan->name} Plan — renewal (credits reset to £{$plan->credits_per_cycle})",
                'balance_after'=> $wallet->totalBalance(),
            ]);
        } else {
            // Add on top
            $this->grantCredits(
                $user,
                (float) $plan->credits_per_cycle,
                'subscription_grant',
                "{$plan->name} Plan — renewal (+£{$plan->credits_per_cycle})"
            );
        }

        Log::info("Subscription renewal processed for user {$user->id}, plan {$plan->name}");
    }

    public function cancelSubscription(User $user): bool
    {
        $sub = $this->getActiveSub($user);
        if (!$sub) return false;

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            \Stripe\Subscription::update($sub->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $sub->update(['cancelled_at' => now()]);
            return true;
        } catch (\Exception $e) {
            Log::error('Subscription cancel failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Session prices in GBP
     */
    public static function sessionPrice(string $duration, string $paymentType = 'full'): float
    {
        $prices = ['30' => 3.50, '60' => 6.00, '90' => 8.00];
        $price  = $prices[$duration] ?? 6.00;
        return $paymentType === 'split' ? round($price / 2, 2) : $price;
    }

    public static function extensionPrice(string $minutes): float
    {
        return match ($minutes) {
            '30'    => 2.00,
            '60'    => 3.50,
            default => 2.00,
        };
    }
}
