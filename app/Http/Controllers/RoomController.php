<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\RoomInvitation;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $search = $request->get('q');
        $status = $request->get('status');

        $baseQuery = fn($userId, $col) => Room::with('evidenceFiles')
            ->where($col, $userId)
            ->when($search, fn($q) => $q->where(function ($q) use ($search) {
                $q->where('case_summary', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            }))
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest();

        $myRooms      = $baseQuery($user->id, 'party_a_id')->paginate(9, ['*'], 'my_rooms');
        $invitedRooms = $baseQuery($user->id, 'party_b_id')->paginate(9, ['*'], 'invited_rooms');

        return view('rooms.index', compact('myRooms', 'invitedRooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:tenancy,freelance,business,ecommerce,employment,debt',
            'jurisdiction' => 'required|string|max:255',
            'language' => 'required|in:english,pidgin,yoruba,igbo,hausa',
            'case_summary' => 'required|string|min:50|max:2000',
            'duration' => 'required|in:30,60,90',
            'payment_type' => 'required|in:full,split',
            'party_b_email' => 'required|email|max:255',
        ]);

        // Create room
        $room = Room::create([
            'uuid' => Str::uuid(),
            'party_a_id' => auth()->id(),
            'category' => $validated['category'],
            'jurisdiction' => $validated['jurisdiction'],
            'language' => $validated['language'],
            'case_summary' => $validated['case_summary'],
            'duration' => $validated['duration'],
            'payment_type' => $validated['payment_type'],
            'status' => 'pending',
            'invite_token' => Str::random(64),
        ]);

        // Store Party B email temporarily (we'll create user account later if they sign up)
        $room->update(['party_b_email' => $validated['party_b_email']]);

        // Send invitation email to Party B
        try {
            Mail::to($validated['party_b_email'])->queue(new RoomInvitation($room));
        } catch (\Exception $e) {
            // Continue even if email fails (for demo)
        }

        // For demo: bypass payment and go directly to room
        return response()->json([
            'success' => true,
            'room_id' => $room->id,
            'payment_url' => route('rooms.show', $room->uuid),
            'message' => 'Room created successfully!'
        ]);
    }

    private function initializePaystackPayment($room, $amount)
    {
        // This is a placeholder - implement actual Paystack integration
        // For now, return a mock payment URL
        
        $paystackSecretKey = config('services.paystack.secret_key');
        
        $url = "https://api.paystack.co/transaction/initialize";
        
        $fields = [
            'email' => auth()->user()->email,
            'amount' => $amount * 100, // Paystack expects amount in kobo
            'reference' => 'LEXROOM_' . $room->uuid . '_' . time(),
            'callback_url' => route('payment.callback'),
            'metadata' => [
                'room_id' => $room->id,
                'user_id' => auth()->id(),
                'payment_type' => $room->payment_type,
            ]
        ];

        $fields_string = http_build_query($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$paystackSecretKey}",
            "Cache-Control: no-cache",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $result = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($result, true);

        if ($response['status']) {
            return $response['data'];
        }

        throw new \Exception('Payment initialization failed');
    }

    public function show($uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        $user = auth()->user();
        
        // Check if user is Party A (creator)
        if ($user && $room->party_a_id === $user->id) {
            return view('rooms.show', compact('room'));
        }
        
        // Check if user is Party B (already assigned)
        if ($user && $room->party_b_id === $user->id) {
            return view('rooms.show', compact('room'));
        }
        
        // Check if user's email matches Party B email (invited but not assigned yet)
        if ($user && $room->party_b_email && $user->email === $room->party_b_email) {
            // Auto-assign Party B if not already assigned
            if (!$room->party_b_id) {
                $room->update(['party_b_id' => $user->id]);
            }
            return view('rooms.show', compact('room'));
        }
        
        // Check for valid invite token (for non-logged-in users)
        if (request()->has('token') && request('token') === $room->invite_token) {
            return view('rooms.show', compact('room'));
        }
        
        abort(403, 'Unauthorized access to this room');
    }
}
