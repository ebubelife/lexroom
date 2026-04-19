<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\RoomInvitation;
use App\Mail\RoomConfirmation;

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
            'category' => 'required|in:tenancy,freelance,business,ecommerce,employment,debt,marriage',
            'title' => 'required|string|max:255',
            'jurisdiction' => 'required|string|max:255',
            'language' => 'required|in:english',
            'case_summary' => 'required|string|min:50|max:2000',
            'duration' => 'required|in:30,60,90',
            'payment_type' => 'required|in:full,split',
            'party_b_email' => 'required|email|max:255',
        ]);

        // Create room
        $room = Room::create([
            'uuid'          => Str::uuid(),
            'party_a_id'    => auth()->id(),
            'category'      => $validated['category'],
            'title'         => $validated['title'],
            'jurisdiction'  => $validated['jurisdiction'],
            'language'      => $validated['language'],
            'case_summary'  => $validated['case_summary'],
            'duration'      => $validated['duration'],
            'payment_type'  => $validated['payment_type'],
            'party_b_email' => $validated['party_b_email'],
            'status'        => 'pending',
            'invite_token'  => Str::random(64),
        ]);

        // Create pending billing record for Party A
        $prices = ['30' => 4.50, '60' => 7.50, '90' => 10.00];
        $plans  = ['30' => 'starter', '60' => 'standard', '90' => 'extended'];
        $fullAmount = $prices[$validated['duration']];
        $partyAAmount = $validated['payment_type'] === 'split' ? $fullAmount / 2 : $fullAmount;

        Billing::create([
            'room_id' => $room->id,
            'user_id' => auth()->id(),
            'party'   => 'party_a',
            'amount'  => $partyAAmount,
            'plan'    => $plans[$validated['duration']],
            'status'  => 'pending',
        ]);

        // If split, create pending billing record for Party B and generate payment token
        if ($validated['payment_type'] === 'split') {
            Billing::create([
                'room_id' => $room->id,
                'user_id' => 0, // unknown until Party B pays
                'party'   => 'party_b',
                'amount'  => $fullAmount / 2,
                'plan'    => $plans[$validated['duration']],
                'status'  => 'pending',
            ]);

            $room->update([
                'party_b_payment_token'      => Str::random(64),
                'party_b_payment_expires_at' => now()->addDays(7),
            ]);
        }

        // Send invitation email to Party B
        try {
            Mail::to($validated['party_b_email'])->send(new RoomInvitation($room));
            \Log::info("Successfully sent invitation email to Party B: " . $validated['party_b_email']);
        } catch (\Exception $e) {
            \Log::error('Failed to send Room Invitation to Party B: ' . $e->getMessage());
        }

        // Send confirmation email to Party A
        try {
            Mail::to(auth()->user()->email)->send(new RoomConfirmation($room));
            \Log::info("Successfully sent confirmation email to Party A: " . auth()->user()->email);
        } catch (\Exception $e) {
            \Log::error('Failed to send Room Confirmation to Party A: ' . $e->getMessage());
        }

        // Redirect Party A to Stripe checkout
        return response()->json([
            'success'     => true,
            'room_id'     => $room->id,
            'payment_url' => route('payment.checkout', $room->id),
            'message'     => 'Room created successfully!'
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
            'reference' => 'FIRSTMEDIATOR_' . $room->uuid . '_' . time(),
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
        if ($user && $room->party_a_id == $user->id) {
            return view('rooms.show', compact('room'));
        }
        
        // Check if user is Party B (already assigned)
        if ($user && $room->party_b_id == $user->id) {
            return view('rooms.show', compact('room'));
        }
        
        // Check if user's email matches Party B email (invited but not assigned yet)
        // BUT only if they are not already Party A!
        if ($user && $room->party_b_email && $user->email === $room->party_b_email && $user->id !== $room->party_a_id) {
            // Auto-assign Party B if not already assigned
            if (!$room->party_b_id) {
                $room->update(['party_b_id' => $user->id]);
            }
            return view('rooms.show', compact('room'));
        }
        
        // Check for valid signed invite link (for non-logged-in users)
        if (request()->hasValidSignature() && request('token') === $room->invite_token) {
            return view('rooms.show', compact('room'));
        } elseif (request()->has('signature') && !request()->hasValidSignature()) {
            return redirect()->route('login')->with('error', 'Your invitation link is invalid or has expired.');
        }
        
        return redirect()->route('login')->with('error', 'Please log in to access this room.');
    }

    public function destroy(Room $room)
    {
        $user = auth()->user();
        
        // Ensure only Party A (the creator) can delete it
        if ($user && $room->party_a_id !== $user->id) {
            return redirect()->route('rooms.index')->with('error', 'Unauthorized action.');
        }

        // Proceed to delete the room
        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }

    public function resendInvite(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();

        // Ensure user is Party A
        if (auth()->id() !== $room->party_a_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'email' => 'required|email|max:255'
        ]);

        $email = $request->email;

        // If email changed, update it and regenerate token
        if ($email !== $room->party_b_email) {
            $room->update([
                'party_b_email' => $email,
                'invite_token' => Str::random(64)
            ]);
        }

        // Send email
        try {
            Mail::to($email)->send(new RoomInvitation($room));
            Log::info("Successfully resent invitation email to Party B: " . $email);
            return response()->json(['success' => true, 'message' => 'Invitation sent successfully.']);
        } catch (\Exception $e) {
            Log::error('Failed to resend Room Invitation to Party B: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send invitation. Please try again later.'], 500);
        }
    }
}
