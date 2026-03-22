<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Demo data: Create sample rooms if none exist
        $totalRooms = Room::where('party_a_id', $user->id)->orWhere('party_b_id', $user->id)->count();
        
        if ($totalRooms === 0) {
            // Create demo rooms
            $demoRooms = [
                [
                    'category' => 'tenancy',
                    'jurisdiction' => 'Lagos',
                    'language' => 'english',
                    'case_summary' => 'Landlord refusing to return security deposit after lease ended. Property was left in good condition but landlord claims damages.',
                    'duration' => '60',
                    'payment_type' => 'split',
                    'status' => 'completed',
                    'party_b_email' => 'landlord@example.com',
                ],
                [
                    'category' => 'freelance',
                    'jurisdiction' => 'FCT',
                    'language' => 'english',
                    'case_summary' => 'Client has not paid for completed web development project. All deliverables were submitted on time and approved.',
                    'duration' => '90',
                    'payment_type' => 'full',
                    'status' => 'active',
                    'party_b_email' => 'client@example.com',
                ],
                [
                    'category' => 'ecommerce',
                    'jurisdiction' => 'Lagos',
                    'language' => 'english',
                    'case_summary' => 'Ordered electronics online but received damaged goods. Seller refusing to process refund or replacement.',
                    'duration' => '30',
                    'payment_type' => 'split',
                    'status' => 'pending',
                    'party_b_email' => 'seller@example.com',
                ],
            ];

            foreach ($demoRooms as $index => $roomData) {
                Room::create([
                    'uuid' => \Str::uuid(),
                    'party_a_id' => $user->id,
                    'category' => $roomData['category'],
                    'jurisdiction' => $roomData['jurisdiction'],
                    'language' => $roomData['language'],
                    'case_summary' => $roomData['case_summary'],
                    'duration' => $roomData['duration'],
                    'payment_type' => $roomData['payment_type'],
                    'status' => $roomData['status'],
                    'party_b_email' => $roomData['party_b_email'],
                    'invite_token' => \Str::random(64),
                    'created_at' => now()->subDays(10 - ($index * 3)),
                ]);
            }
        }

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

        // Stats with demo data
        $stats = [
            'total'    => Room::where('party_a_id', $user->id)->orWhere('party_b_id', $user->id)->count(),
            'active'   => Room::where(function($q) use ($user) {
                              $q->where('party_a_id', $user->id)->orWhere('party_b_id', $user->id);
                          })->whereIn('status', ['active', 'pending'])->count(),
            'resolved' => Room::where(function($q) use ($user) {
                              $q->where('party_a_id', $user->id)->orWhere('party_b_id', $user->id);
                          })->where('status', 'completed')->count(),
            'credits'  => 15000, // Demo credits
        ];

        return view('dashboard.index', compact('myRooms', 'invitedRooms', 'activeSessions', 'stats'));
    }
}