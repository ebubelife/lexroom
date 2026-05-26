<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReportJob;
use App\Jobs\ProcessLexResponse;
use App\Models\Room;
use App\Models\SessionExtension;
use App\Models\SessionMessage;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // Cost per 30-minute extension block in USD
    const EXTENSION_COST_PER_30_MIN = 50;

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

        // Total seconds = (original duration + extensions) * 60
        $totalSeconds = ($room->duration + $room->extended_minutes) * 60;
        $timerKey = "room:{$room->id}:timer";
        $startedAt = $room->started_at;

        if ($room->status === 'completed') {
            $remainingSeconds = 0;
        } elseif ($startedAt && in_array($room->status, ['active', 'pause_requested'])) {
            $elapsed = (int) now()->diffInSeconds($startedAt);
            $elapsed = $elapsed - (int) $room->total_paused_seconds;
            $remainingSeconds = max(0, $totalSeconds - $elapsed);
            Cache::put($timerKey, (int) $remainingSeconds, 7200);
        } elseif ($startedAt && $room->status === 'paused' && $room->paused_at) {
            $elapsed = (int) $room->paused_at->diffInSeconds($startedAt);
            $elapsed = $elapsed - (int) $room->total_paused_seconds;
            $remainingSeconds = max(0, $totalSeconds - $elapsed);
            Cache::put($timerKey, (int) $remainingSeconds, 7200);
        } else {
            // For pending/awaiting status, always show full time and clear any cached values
            $remainingSeconds = $totalSeconds;
            Cache::forget($timerKey);
        }

        // Get current phase
        $currentPhase = Cache::get("room:{$room->id}:phase", 'opening');

        // Check if Lex is processing
        $lexProcessing = Cache::get("room:{$room->id}:lex_processing", false);

        return response()->json([
            'messages' => $messages,
            'timer' => [
                'remaining_seconds' => (int) $remainingSeconds,
                'total_seconds' => (int) $totalSeconds,
            ],
            'phase' => $currentPhase,
            'lex_processing' => $lexProcessing,
            'status' => $room->status,
            'party_b_clocked_in_at' => $room->party_b_clocked_in_at,
            'extended_minutes' => (int) $room->extended_minutes,
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

        // Enforce role based on authentication
        $senderType = $request->input('sender_type');
        if (Auth::check()) {
            if (Auth::id() == $room->party_a_id) {
                $senderType = 'party_a';
            } elseif (Auth::id() == $room->party_b_id) {
                $senderType = 'party_b';
            }
        } elseif (request('token') === $room->invite_token) {
            // Unauthenticated guest with token must be Party B
            $senderType = 'party_b';
        }

        // Verify room is active
        if ($room->status !== 'active') {
            return response()->json(['error' => 'Room is not active'], 403);
        }

        // Get current phase
        $currentPhase = Cache::get("room:{$room->id}:phase", 'opening');

        // Save message
        $message = SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => $senderType,
            'content' => $request->input('content'),
            'phase' => $currentPhase,
        ]);

        // Trigger Lex response (queued)
        Cache::put("room:{$room->id}:lex_processing", true, 60);

        if (config('queue.default') === 'sync') {
            set_time_limit(120);
        }

        dispatch(new ProcessLexResponse($room->id, $message->id));

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
     * Lock session when timer expires (called from frontend)
     */
    public function lockSession(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();

        if (!in_array($room->status, ['active', 'pause_requested'])) {
            return response()->json(['success' => true, 'status' => $room->status]);
        }

        $room->update([
            'status' => 'completed',
            'ended_at' => now(),
        ]);

        // Clean up cache
        Cache::forget("room:{$room->id}:timer");
        Cache::forget("room:{$room->id}:lex_processing");

        // Save FM closing message
        SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => '⏰ Session time has expired. This mediation room is now locked. Party A may extend the session to continue.',
            'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
        ]);

        // Trigger report generation
        GenerateReportJob::dispatch($room->id);

        return response()->json(['success' => true, 'status' => 'completed']);
    }

    /**
     * Extend session — Party A buys more time
     */
    public function extendSession(Request $request, $uuid)
    {
        $request->validate([
            'minutes' => 'required|integer|in:30,60,90,120',
        ]);

        $room = Room::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Only party A can extend
        if (!$user || $room->party_a_id !== $user->id) {
            return response()->json(['error' => 'Only Party A can extend the session.'], 403);
        }

        // Room must be completed (expired) or active
        if (!in_array($room->status, ['completed', 'active'])) {
            return response()->json(['error' => 'Session cannot be extended in its current state.'], 400);
        }

        $minutes = (int) $request->minutes;
        $blocks = $minutes / 30;
        $amount = $blocks * self::EXTENSION_COST_PER_30_MIN;

        // Deduct from wallet
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet || $wallet->credits_balance < $amount) {
            return response()->json([
                'error' => "Insufficient wallet balance. You need $" . number_format($amount) . " to extend by {$minutes} minutes.",
                'required' => $amount,
                'balance' => $wallet ? (float) $wallet->credits_balance : 0,
            ], 402);
        }

        DB::transaction(function () use ($room, $user, $wallet, $minutes, $amount) {
            // Deduct wallet
            $wallet->decrement('credits_balance', $amount);

            // Record extension for admin tracking
            SessionExtension::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'minutes_added' => $minutes,
                'amount_paid' => $amount,
                'status' => 'paid',
            ]);

            // Update room
            $wasCompleted = $room->status === 'completed';
            $room->update([
                'extended_minutes' => $room->extended_minutes + $minutes,
                'status' => 'active',
                // If room was ended, adjust started_at to account for extension properly
                // We keep original started_at. Timer calc will use new totalSeconds.
            ]);

            // If room was locked, clear ended_at so it's live again
            if ($wasCompleted) {
                $room->update(['ended_at' => null]);
            }

            // Refresh cache for new total
            $newTotal = ($room->duration + $room->extended_minutes + $minutes) * 60;
            Cache::put("room:{$room->id}:phase", Cache::get("room:{$room->id}:phase", 'opening'), 7200);

            // FM announcement
            SessionMessage::create([
                'room_id' => $room->id,
                'sender_type' => 'lex',
                'content' => "⏱️ Session extended by {$minutes} minutes. The mediation continues. Please use this additional time productively.",
                'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
            ]);
        });

        // Re-fetch room with fresh data
        $room->refresh();
        $totalSeconds = ($room->duration + $room->extended_minutes) * 60;
        $elapsed = (int) now()->diffInSeconds($room->started_at) - (int) $room->total_paused_seconds;
        $remainingSeconds = max(0, $totalSeconds - $elapsed);

        return response()->json([
            'success' => true,
            'minutes_added' => $minutes,
            'amount_paid' => $amount,
            'extended_minutes' => (int) $room->extended_minutes,
            'timer' => [
                'remaining_seconds' => (int) $remainingSeconds,
                'total_seconds' => (int) $totalSeconds,
            ],
            'wallet_balance' => (float) $wallet->fresh()->credits_balance,
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

        // Send FM welcome message
        SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => "Welcome to FirstMediator. I'm FM, your AI advisor. I'm here to facilitate a fair and constructive dialogue between both parties. Let's begin with opening statements. Party A, please share your perspective on this dispute.",
            'phase' => 'opening',
        ]);

        $totalSeconds = ($room->duration + $room->extended_minutes) * 60;
        $remainingSeconds = $totalSeconds;

        return response()->json([
            'success' => true,
            'message' => 'Session started',
            'timer' => [
                'remaining_seconds' => (int) $remainingSeconds,
                'total_seconds' => (int) $totalSeconds,
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
        if (!Auth::check()) {
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
            'status' => 'pending'
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

    public function requestPause(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Only Party A can request pause
        if ($user && $room->party_a_id !== $user->id) {
            return response()->json(['error' => 'Only the room creator can request a pause.'], 403);
        }

        if ($room->status !== 'active') {
            return response()->json(['error' => 'Session must be active to pause.'], 400);
        }

        $room->update([
            'status' => 'pause_requested',
            'pause_requested_at' => now(),
        ]);

        SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => "Party A has requested to pause the session. Party B, please accept to pause.",
            'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
        ]);

        return response()->json(['success' => true, 'message' => 'Pause requested.']);
    }

    public function acceptPause(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();

        // Ensure user is Party B (could be guest with token or auth)
        if (Auth::check() && $room->party_b_id !== Auth::id()) {
            return response()->json(['error' => 'Only Party B can accept the pause.'], 403);
        }

        if ($room->status !== 'pause_requested') {
            return response()->json(['error' => 'No active pause request.'], 400);
        }

        $room->update([
            'status' => 'paused',
            'paused_at' => now(),
        ]);

        SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => "Session is now paused. It can be resumed by Party A within 24 hours.",
            'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
        ]);

        return response()->json(['success' => true, 'message' => 'Session paused.']);
    }

    public function resumeSession(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Only Party A can resume
        if ($user && $room->party_a_id !== $user->id) {
            return response()->json(['error' => 'Only the room creator can resume the session.'], 403);
        }

        if ($room->status !== 'paused' || !$room->paused_at) {
            return response()->json(['error' => 'Session is not paused.'], 400);
        }

        $pausedDuration = now()->diffInSeconds($room->paused_at);
        $totalPaused = $room->total_paused_seconds + $pausedDuration;

        $room->update([
            'status' => 'active',
            'paused_at' => null,
            'pause_requested_at' => null,
            'total_paused_seconds' => $totalPaused,
        ]);

        SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => "Session has been resumed.",
            'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
        ]);

        return response()->json(['success' => true, 'message' => 'Session resumed.']);
    }
}
