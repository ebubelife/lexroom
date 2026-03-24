<?php

namespace App\Http\Controllers;

use App\Models\Lawyer;
use Illuminate\Http\Request;

class FmReferController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Mock data for the Refer & Earn dashboard
        $stats = [
            'total' => 0,
            'successful' => 0,
            'earned' => 0,
        ];

        $referralLink = route('register') . '?ref=' . ($user->invite_token ?? 'fm_12345');
        $referrals = [];

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
