<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAction;
use App\Models\Billing;
use App\Models\EvidenceFile;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'        => User::count(),
            'new_users_7d'       => User::where('created_at', '>=', now()->subDays(7))->count(),
            'new_users_30d'      => User::where('created_at', '>=', now()->subDays(30))->count(),

            'total_rooms'        => Room::count(),
            'active_rooms'       => Room::where('status', 'active')->count(),
            'pending_rooms'      => Room::whereIn('status', ['pending_payment', 'waiting_for_party_b'])->count(),
            'resolved_rooms'     => Room::where('status', 'locked')->count(),

            'total_revenue'      => Billing::where('status', 'paid')->sum('amount'),
            'revenue_7d'         => Billing::where('status', 'paid')->where('paid_at', '>=', now()->subDays(7))->sum('amount'),
            'pending_payments'   => Billing::where('status', 'pending')->count(),

            'total_files'        => EvidenceFile::count(),
        ];

        // Revenue by day for last 14 days
        $revenueChart = Billing::where('status', 'paid')
            ->where('paid_at', '>=', now()->subDays(14))
            ->select(DB::raw('DATE(paid_at) as date'), DB::raw('SUM(amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Signups by day for last 14 days
        $signupsChart = User::where('created_at', '>=', now()->subDays(14))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date');

        // Recent admin actions
        $recentActions = AdminAction::with('admin')
            ->latest()
            ->limit(10)
            ->get();

        // Recent rooms
        $recentRooms = Room::with(['partyA', 'partyB'])
            ->latest()
            ->limit(5)
            ->get();

        // Recent users
        $recentUsers = User::latest()->limit(5)->get();

        return view('admin.dashboard.index', compact(
            'stats', 'revenueChart', 'signupsChart', 'recentActions', 'recentRooms', 'recentUsers'
        ));
    }
}
