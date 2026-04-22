<?php

namespace App\Jobs;

use App\Models\ReferralReward;
use App\Models\Room;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AwardReferralMinutes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $roomId) {}

    public function handle(SubscriptionService $service): void
    {
        $room = Room::find($this->roomId);
        if (!$room) return;

        $partyA = User::find($room->party_a_id);
        if (!$partyA || !$partyA->referred_by_id) return;

        // Only reward on exactly first completed paid session
        $completedSessions = Room::where('party_a_id', $partyA->id)
            ->where('status', 'completed')
            ->count();

        if ($completedSessions !== 1) return;

        $referralRecord = ReferralReward::where('referred_user_id', $partyA->id)
            ->where('status', 'pending')
            ->first();

        if (!$referralRecord) return;

        $referrer = User::find($partyA->referred_by_id);
        if (!$referrer || !$referrer->wallet) return;

        // Get reward amount from credit settings (admin-editable)
        $rewardCredits = (float) \App\Models\CreditSetting::get('referral_reward_credits', '2.00');

        // Award credits to referrer
        $service->grantReferralCredits($referrer, $rewardCredits);

        // Mark referral completed
        $referralRecord->update([
            'status'          => 'completed',
            'minutes_awarded' => 0, // legacy field, not used
            'awarded_at'      => now(),
            'expires_at'      => now()->addYear(),
        ]);

        $this->notifyReferrer($referrer, $partyA, $rewardCredits);

        Log::info("Referral credit reward awarded", [
            'referrer_id'      => $referrer->id,
            'referred_user_id' => $partyA->id,
            'credits'          => $rewardCredits,
        ]);
    }

    protected function notifyReferrer(User $referrer, User $referredUser, float $credits): void
    {
        try {
            Mail::send('emails.referral-reward', [
                'referrer'     => $referrer,
                'referredUser' => $referredUser,
                'credits'      => $credits,
            ], function ($m) use ($referrer, $credits) {
                $m->to($referrer->email)
                  ->subject("🎉 You earned £{$credits} free credits on FirstMediator!");
            });
        } catch (\Exception $e) {
            Log::error('Referral reward email failed', ['error' => $e->getMessage()]);
        }
    }
}
