<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FmReferController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Calculate real stats
        $stats = [
            'total' => $user->referrals()->count(),
            'successful' => $user->referrals()->whereExists(function($query) {
                $query->select(\DB::raw(1))
                    ->from('rooms')
                    ->whereColumn('rooms.party_a_id', 'users.id')
                    ->orWhereColumn('rooms.party_b_id', 'users.id')
                    ->where('rooms.status', 'completed');
            })->count(),
            'earned' => $user->referrals()->whereExists(function($query) {
                $query->select(\DB::raw(1))
                    ->from('rooms')
                    ->whereColumn('rooms.party_a_id', 'users.id')
                    ->orWhereColumn('rooms.party_b_id', 'users.id')
                    ->where('rooms.status', 'completed');
            })->count() * 1000,
        ];

        $referralLink = route('register') . '?ref=' . $user->referral_code;
        
        $referrals = $user->referrals()->latest()->get()->map(function($referral) {
            $hasCompleted = \App\Models\Room::where(function($q) use ($referral) {
                $q->where('party_a_id', $referral->id)
                  ->orWhere('party_b_id', $referral->id);
            })->where('status', 'completed')->exists();
            return [
                'name' => $referral->name,
                'date' => $referral->created_at->format('M d, Y'),
                'status' => $hasCompleted ? 'Successful' : 'Pending',
                'status_color' => $hasCompleted ? '#16A34A' : '#D97706',
                'reward' => $hasCompleted ? '$1,000' : '$0',
            ];
        });

        return view('fmrefer.index', compact('stats', 'referralLink', 'referrals'));
    }

    public function show(Lawyer $lawyer)
    {
        if (!$lawyer->active || !$lawyer->verified) {
            abort(404);
        }

        return view('fmrefer.show', compact('lawyer'));
    }

    public function contact(Request $request, Lawyer $lawyer)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'message' => 'required|string|max:1000',
        ]);

        // Send notification to lawyer
        // TODO: Implement email notification

        return response()->json([
            'success' => true,
            'message' => 'Your request has been sent to the lawyer. They will contact you within 48-72 hours.',
        ]);
    }
}
