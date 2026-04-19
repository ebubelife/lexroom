<?php

namespace App\Jobs;

use App\Models\ReferralReward;
use App\Models\Room;
use App\Models\User;
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

    public function handle(): void
    {
        $room = Room::find($this->roomId);

        if (!$room) return;

        $partyA = User::find($room->party_a_id);

        if (!$partyA || !$partyA->referred_by_id) return;

        // Check this is their first completed paid session
        $completedSessions = Room::where('party_a_id', $partyA->id)
            ->where('status', 'completed')
            ->count();

        if ($completedSessions !== 1) return; // Only reward on exactly first completion

        // Check referral record exists and is still pending
        $referralRecord = ReferralReward::where('referred_user_id', $partyA->id)
            ->where('status', 'pending')
            ->first();

        if (!$referralRecord) return;

        // Get reward amount from config (admin-editable later)
        $minutesToAward = config('referral.minutes_per_referral', 10);

        // Award minutes to referrer's wallet
        $referrer = User::find($partyA->referred_by_id);

        if (!$referrer) return;

        $wallet = $referrer->wallet;

        if (!$wallet) return;

        $wallet->addReferralMinutes($minutesToAward);

        // Mark referral as completed
        $referralRecord->update([
            'status' => 'completed',
            'minutes_awarded' => $minutesToAward,
            'awarded_at' => now(),
            'expires_at' => now()->addYear(),
        ]);

        // Notify referrer
        $this->notifyReferrer($referrer, $partyA, $minutesToAward);

        Log::info("Referral reward awarded", [
            'referrer_id' => $referrer->id,
            'referred_user_id' => $partyA->id,
            'minutes' => $minutesToAward,
        ]);
    }

    protected function notifyReferrer(User $referrer, User $referredUser, int $minutes): void
    {
        try {
            Mail::send('emails.referral-reward', [
                'referrer' => $referrer,
                'referredUser' => $referredUser,
                'minutes' => $minutes,
            ], function ($message) use ($referrer, $minutes) {
                $message->to($referrer->email)
                    ->subject("🎉 You earned {$minutes} free minutes on FirstMediator!");
            });
        } catch (\Exception $e) {
            Log::error('Referral reward email failed', ['error' => $e->getMessage()]);
        }
    }
}
