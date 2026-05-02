<?php

namespace App\Http\Controllers;

use App\Models\Escalation;
use App\Models\Lawyer;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EscalationController extends Controller
{
    /**
     * Show escalation form — lawyer profile with contact form
     */
    public function show(Lawyer $lawyer)
    {
        abort_if(!$lawyer->active || !$lawyer->verified, 404);

        // Get user's completed rooms for the dropdown
        $rooms = Room::where('party_a_id', auth()->id())
            ->where('status', 'completed')
            ->latest()
            ->get();

        return view('fmrefer.show', compact('lawyer', 'rooms'));
    }

    /**
     * Submit escalation request
     */
    public function store(Request $request, Lawyer $lawyer)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'message' => 'required|string|min:20|max:1000',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Ensure user owns this room
        abort_if($room->party_a_id !== auth()->id(), 403);

        // Check not already escalated to this lawyer
        $exists = Escalation::where('room_id', $room->id)
            ->where('lawyer_id', $lawyer->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already escalated this case to this lawyer.');
        }

        $escalation = Escalation::create([
            'room_id'   => $room->id,
            'user_id'   => auth()->id(),
            'lawyer_id' => $lawyer->id,
            'message'   => $request->message,
            'status'    => 'pending',
        ]);

        // Email the lawyer
        $this->notifyLawyer($lawyer, $room, $request->message);

        return back()->with('success', 'Your case has been sent to ' . $lawyer->name . '. They will contact you within 48–72 hours.');
    }

    protected function notifyLawyer(Lawyer $lawyer, Room $room, string $message): void
    {
        try {
            Mail::send('emails.escalation', [
                'lawyer'  => $lawyer,
                'room'    => $room,
                'user'    => auth()->user(),
                'message' => $message,
            ], function ($m) use ($lawyer, $room) {
                $m->to($lawyer->email)
                  ->subject("New Case Referral — {$room->title} | FirstMediator");
            });
        } catch (\Exception $e) {
            Log::error('Escalation email failed: ' . $e->getMessage());
        }
    }
}
