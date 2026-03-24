<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessLexResponse;
use App\Models\Room;
use App\Models\SessionMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

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

        // Get timer state from Redis
        $timerKey = "room:{$room->id}:timer";
        $remainingSeconds = Redis::get($timerKey) ?? ($room->duration * 60);

        // Get current phase
        $currentPhase = Cache::get("room:{$room->id}:phase", 'opening');

        // Check if Lex is processing
        $lexProcessing = Cache::get("room:{$room->id}:lex_processing", false);

        return response()->json([
            'messages' => $messages,
            'timer' => [
                'remaining_seconds' => (int) $remainingSeconds,
                'total_seconds' => $room->duration * 60,
            ],
            'phase' => $currentPhase,
            'lex_processing' => $lexProcessing,
            'status' => $room->status,
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
        ProcessLexResponse::dispatch($room->id, $message->id);

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

        // Initialize timer in Redis
        $timerKey = "room:{$room->id}:timer";
        Redis::set($timerKey, $room->duration * 60);

        // Set initial phase
        Cache::put("room:{$room->id}:phase", 'opening', 7200);

        // Send Lex welcome message
        $welcomeMessage = SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => "Welcome to FirstMediator. I'm Lex, your AI mediator. I'm here to facilitate a fair and constructive dialogue between both parties. Let's begin with opening statements. Party A, please share your perspective on this dispute.",
            'phase' => 'opening',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Session started',
            'timer' => [
                'remaining_seconds' => $room->duration * 60,
                'total_seconds' => $room->duration * 60,
            ],
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
