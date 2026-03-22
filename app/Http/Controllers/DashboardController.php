<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Rooms where user is Party A
        $myRooms = Room::where('party_a_id', $user->id)
            ->with(['partyB', 'billing'])
            ->latest()
            ->paginate(10);

        // Rooms where user is Party B
        $invitedRooms = Room::where('party_b_id', $user->id)
            ->with(['partyA', 'billing'])
            ->latest()
            ->paginate(10);

        // Active/pending sessions needing attention
        $activeSessions = Room::where(function($q) use ($user) {
                $q->where('party_a_id', $user->id)
                  ->orWhere('party_b_id', $user->id);
            })
            ->whereIn('status', ['active', 'pending', 'waiting_for_party_b'])
            ->with(['partyA', 'partyB'])
            ->latest()
            ->take(3)
            ->get();

        // Stats
        $stats = [
            'total'    => Room::where('party_a_id', $user->id)->orWhere('party_b_id', $user->id)->count(),
            'active'   => Room::where(function($q) use ($user) {
                              $q->where('party_a_id', $user->id)->orWhere('party_b_id', $user->id);
                          })->whereIn('status', ['active', 'pending'])->count(),
            'resolved' => Room::where(function($q) use ($user) {
                              $q->where('party_a_id', $user->id)->orWhere('party_b_id', $user->id);
                          })->where('status', 'completed')->count(),
            'credits'  => $user->wallet?->credits_balance ?? 0,
        ];

        return view('dashboard.index', compact('myRooms', 'invitedRooms', 'activeSessions', 'stats'));
    }
}