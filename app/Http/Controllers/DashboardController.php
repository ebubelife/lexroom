<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Rooms where user is Party A (exclude trashed)
        $myRooms = Room::where('party_a_id', $user->id)
            ->whereNull('user_deleted_at')
            ->with(['partyB'])
            ->latest()
            ->paginate(10);

        // Rooms where user is Party B (exclude trashed) - includes both party_b_id and party_b_user_id
        $invitedRooms = Room::where(function($q) use ($user) {
                $q->where('party_b_id', $user->id)
                  ->orWhere('party_b_user_id', $user->id);
            })
            ->whereNull('user_deleted_at')
            ->with(['partyA'])
            ->latest()
            ->paginate(10);

        // Active/pending sessions needing attention (exclude trashed)
        $activeSessions = Room::where(function($q) use ($user) {
                $q->where('party_a_id', $user->id)
                  ->orWhere('party_b_id', $user->id)
                  ->orWhere('party_b_user_id', $user->id);
            })
            ->whereNull('user_deleted_at')
            ->whereIn('status', ['active', 'pending', 'waiting_for_party_b'])
            ->with(['partyA', 'partyB'])
            ->latest()
            ->take(3)
            ->get();

        // Real Stats (exclude trashed)
        $stats = [
            'total'    => Room::where(function($q) use ($user) {
                              $q->where('party_a_id', $user->id)
                                ->orWhere('party_b_id', $user->id)
                                ->orWhere('party_b_user_id', $user->id);
                          })->whereNull('user_deleted_at')->count(),
            'active'   => Room::where(function($q) use ($user) {
                              $q->where('party_a_id', $user->id)
                                ->orWhere('party_b_id', $user->id)
                                ->orWhere('party_b_user_id', $user->id);
                          })->whereNull('user_deleted_at')->whereIn('status', ['active', 'pending'])->count(),
            'resolved' => Room::where(function($q) use ($user) {
                              $q->where('party_a_id', $user->id)
                                ->orWhere('party_b_id', $user->id)
                                ->orWhere('party_b_user_id', $user->id);
                          })->whereNull('user_deleted_at')->where('status', 'completed')->count(),
            'credits'  => $user->wallet?->credits_balance ?? 0,
        ];

        return view('dashboard.index', compact('myRooms', 'invitedRooms', 'activeSessions', 'stats'));
    }
}