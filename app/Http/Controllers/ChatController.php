<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessLexResponse;
use App\Models\Room;
use App\Models\SessionMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatController extends Controller
{
    /**
     * Poll for new messages and room state
     */
    public function poll(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        $since = $request->input('since', 0);

        // Get new messages since last poll
        $messages = SessionMessage::where('room_id', $room->id)
            ->where('id', '>', $since)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_type' => $message->sender_type,
                    'content' => $message->content,
                    'phase' => $message->phase,
                    'created_at' => $message->created_at->toISOString(),
                ];
            });

        // Get timer state from Cache
        $timerKey = "room:{$room->id}:timer";
        $startedAtKey = "room:{$room->id}:started_at";
        
        $totalSeconds = $room->duration * 60;
        $startedAt = Cache::get($startedAtKey);
        
        if ($startedAt) {
            $elapsed = now()->diffInSeconds($startedAt);
            $remainingSeconds = max(0, $totalSeconds - $elapsed);
            Cache::put($timerKey, $remainingSeconds, 7200);
        } else {
            $remainingSeconds = Cache::get($timerKey) ?? $totalSeconds;
        }

        // Get current phase
        $currentPhase = Cache::get("room:{$room->id}:phase", 'opening');

        // Check if Lex is processing
        $lexProcessing = Cache::get("room:{$room->id}:lex_processing", false);

        return response()->json([
            'messages' => $messages,
            'timer' => [
                'remaining_seconds' => (int) $remainingSeconds,
                'total_seconds' => $totalSeconds,
            ],
            'phase' => $currentPhase,
            'lex_processing' => $lexProcessing,
            'status' => $room->status,
            'party_b_clocked_in_at' => $room->party_b_clocked_in_at,
        ]);
    }

    /**
     * Send a message from a party
     */
    public function sendMessage(Request $request, $uuid)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'sender_type' => 'required|in:party_a,party_b',
        ]);

        $room = Room::where('uuid', $uuid)->firstOrFail();

        // Verify room is active
        if ($room->status !== 'active') {
            return response()->json(['error' => 'Room is not active'], 403);
        }

        // Get current phase
        $currentPhase = Cache::get("room:{$room->id}:phase", 'opening');

        // Save message
        $message = SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => $request->sender_type,
            'content' => $request->content,
            'phase' => $currentPhase,
        ]);

        // Trigger Lex response (queued)
        Cache::put("room:{$room->id}:lex_processing", true, 60);
        // Send message to FM for processing
        if (config('queue.default') === 'sync') {
            // If using sync, give the server more time to wait for the AI
            set_time_limit(120); 
        }
        
        \App\Jobs\ProcessLexResponse::dispatch($room->id, $message->id);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_type' => $message->sender_type,
                'content' => $message->content,
                'phase' => $message->phase,
                'created_at' => $message->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Start the session timer
     */
    public function startSession($uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();

        if ($room->status !== 'pending') {
            return response()->json(['error' => 'Session already started'], 400);
        }

        // Update room status
        $room->update([
            'status' => 'active',
            'started_at' => now(),
        ]);

        // Initialize state in Cache
        Cache::put("room:{$room->id}:phase", 'opening', 7200);
        Cache::put("room:{$room->id}:lex_processing", false, 7200);

        // Set initial phase
        Cache::put("room:{$room->id}:phase", 'opening', 7200);

        // Send FM welcome message
        $welcomeMessage = SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => "Welcome to FirstMediator. I'm FM, your AI advisor. I'm here to facilitate a fair and constructive dialogue between both parties. Let's begin with opening statements. Party A, please share your perspective on this dispute.",
            'phase' => 'opening',
        ]);

        $remainingSeconds = $room->duration * 60;
        if ($room->status === 'active' && $room->started_at) {
            $remainingSeconds = max(0, ($room->duration * 60) - $room->started_at->diffInSeconds(now()));
        }

        return response()->json([
            'success' => true,
            'message' => 'Session started',
            'timer' => [
                'remaining_seconds' => (int) $remainingSeconds,
                'total_seconds' => (int) ($room->duration * 60),
            ],
        ]);
    }

    /**
     * Party B Clock-In
     */
    public function clockIn(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        
        // Guest verification if not logged in
        if (!auth()->check()) {
            $token = $request->input('token');
            if (!$token || $token !== $room->invite_token) {
                return response()->json(['error' => 'Invalid invitation token'], 403);
            }
        }

        if ($room->party_b_clocked_in_at) {
            return response()->json(['success' => true, 'message' => 'Already clocked in']);
        }

        $room->update([
            'party_b_clocked_in_at' => now(),
            'status' => 'pending' // Ensure it's pending so Party A can start
        ]);

        // Send FM greeting for Party B arrival
        SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => "Party B has officially joined the mediation room. FM is now aware of both parties' presence. Party A, you may now click the 'Start Session' button to begin our formal dialogue.",
            'phase' => 'opening',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Clock-in successful',
            'party_b_clocked_in_at' => $room->party_b_clocked_in_at->toISOString(),
        ]);
    }

    /**
     * Change session phase
     */
    public function changePhase(Request $request, $uuid)
    {
        $request->validate([
            'phase' => 'required|in:opening,evidence,cross_examination,analysis,resolution',
        ]);

        $room = Room::where('uuid', $uuid)->firstOrFail();
        Cache::put("room:{$room->id}:phase", $request->phase, 7200);

        // Send phase change notification
        SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => "We are now moving to the {$request->phase} phase.",
            'phase' => $request->phase,
        ]);

        return response()->json(['success' => true]);
    }
}
