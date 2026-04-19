<?php

namespace App\Http\Controllers;

use App\Models\ReferralReward;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $rewards = ReferralReward::with('referredUser')
            ->where('referrer_id', $user->id)
            ->latest()
            ->get();

        $stats = [
            'total'     => $rewards->count(),
            'completed' => $rewards->where('status', 'completed')->count(),
            'pending'   => $rewards->where('status', 'pending')->count(),
            'minutes'   => $rewards->where('status', 'completed')->sum('minutes_awarded'),
        ];

        $wallet          = $user->wallet;
        $minutesBalance  = $wallet?->referral_minutes ?? 0;
        $minutesExpiry   = $wallet?->referral_minutes_expires_at;
        $referralLink    = url('/register?ref=' . $user->referral_code);
        $minutesPerRefer = config('referral.minutes_per_referral', 10);

        // Leaderboard — top 10 referrers this month
        $leaderboard = ReferralReward::with('referrer')
            ->where('status', 'completed')
            ->whereMonth('awarded_at', now()->month)
            ->selectRaw('referrer_id, SUM(minutes_awarded) as total_minutes, COUNT(*) as total_referrals')
            ->groupBy('referrer_id')
            ->orderByDesc('total_referrals')
            ->limit(10)
            ->get();

        return view('referrals.index', compact(
            'rewards', 'stats', 'minutesBalance', 'minutesExpiry',
            'referralLink', 'minutesPerRefer', 'leaderboard', 'user'
        ));
    }
}
