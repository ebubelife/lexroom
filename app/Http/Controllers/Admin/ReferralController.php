<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralReward;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $query = ReferralReward::with(['referrer', 'referredUser'])->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('referrer', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                       ->orWhere('email', 'like', "%{$search}%"))
                  ->orWhereHas('referredUser', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                            ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $rewards = $query->paginate(30)->withQueryString();

        $stats = [
            'total'           => ReferralReward::count(),
            'completed'       => ReferralReward::where('status', 'completed')->count(),
            'pending'         => ReferralReward::where('status', 'pending')->count(),
            'total_minutes'   => ReferralReward::where('status', 'completed')->sum('minutes_awarded'),
        ];

        return view('admin.referrals.index', compact('rewards', 'stats'));
    }

    public function revoke(ReferralReward $reward)
    {
        if ($reward->status !== 'completed') {
            return back()->with('error', 'Only completed rewards can be revoked.');
        }

        // Deduct the minutes from the referrer's wallet
        $wallet = $reward->referrer?->wallet;
        if ($wallet && $wallet->referral_minutes >= $reward->minutes_awarded) {
            $wallet->decrement('referral_minutes', $reward->minutes_awarded);
        }

        $reward->update(['status' => 'revoked']);

        auth('admin')->user()->log('revoked_referral_reward', 'ReferralReward', $reward->id, [
            'referrer_id'     => $reward->referrer_id,
            'minutes_revoked' => $reward->minutes_awarded,
        ]);

        return back()->with('success', "Referral reward revoked and {$reward->minutes_awarded} minutes deducted.");
    }
}
