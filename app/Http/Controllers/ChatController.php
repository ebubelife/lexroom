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

        // Record Party B heartbeat
        if ((Auth::check() && Auth::id() == $room->party_b_id) || $request->input('token') === $room->invite_token) {
            Cache::put("room:{$room->id}:party_b_last_seen", now()->timestamp, 30);
        }

        $partyBLastSeen = Cache::get("room:{$room->id}:party_b_last_seen");
        $partyBOnline = $partyBLastSeen && (now()->timestamp - $partyBLastSeen < 10);

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
            $startTime = $startedAt->timestamp;
            $currentTime = now()->timestamp;
            $elapsed = max(0, $currentTime - $startTime);
            $elapsed = $elapsed - (int) $room->total_paused_seconds;
            $remainingSeconds = max(0, $totalSeconds - $elapsed);
            Cache::put($timerKey, (int) $remainingSeconds, 7200);
        } elseif ($startedAt && $room->status === 'paused' && $room->paused_at) {
            // Check for 24-hour expiration
            if (now()->diffInHours($room->paused_at) >= 24) {
                $room->update([
                    'status' => 'expired',
                    'ended_at' => now(),
                ]);
                $remainingSeconds = 0;
                Cache::forget($timerKey);

                // Add FM message for expiration
                SessionMessage::create([
                    'room_id' => $room->id,
                    'sender_type' => 'lex',
                    'content' => '⏰ This session has been paused for over 24 hours and has now automatically expired.',
                    'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
                ]);
                
                // Dispatch report generation
                GenerateReportJob::dispatch($room->id);
            } else {
                $startTime = $startedAt->timestamp;
                $pauseTime = $room->paused_at->timestamp;
                $elapsed = max(0, $pauseTime - $startTime);
                $elapsed = $elapsed - (int) $room->total_paused_seconds;
                $remainingSeconds = max(0, $totalSeconds - $elapsed);
                Cache::put($timerKey, (int) $remainingSeconds, 7200);
            }
        } else {
            // For pending/awaiting status, always show full time and clear any cached values
            $remainingSeconds = $totalSeconds;
            Cache::forget($timerKey);
        }

        // Get current phase
        $currentPhase = Cache::get("room:{$room->id}:phase", 'opening');

        // Check if Lex is processing
        $lexProcessing = Cache::get("room:{$room->id}:lex_processing", false);

        // Check pending extensions
        $pendingExtension = $room->extensionRequests()
            ->whereIn('status', ['pending_party_b', 'pending_top_up'])
            ->latest()
            ->first();

        // Check grace period expiry (2 minutes)
        if ($pendingExtension && $room->status === 'completed' && $room->ended_at) {
            if (now()->diffInSeconds($room->ended_at) > 120) {
                $pendingExtension->update(['status' => 'expired']);
                $pendingExtension = null;
                // Dispatch report if not dispatched yet
                GenerateReportJob::dispatch($room->id);
            }
        }

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
            'pending_extension' => $pendingExtension,
            'party_b_online' => $partyBOnline,
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

        // Trigger Lex response (queued) with a 120s cache to account for the delay
        Cache::put("room:{$room->id}:lex_processing", true, 120);

        if (config('queue.default') === 'sync') {
            set_time_limit(120);
        }

        dispatch(new ProcessLexResponse($room->id, $message->id))->delay(now()->addSeconds(30));

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

        if ($room->hasPendingExtension()) {
            $room->update([
                'status' => 'completed',
                'ended_at' => now(),
            ]);
            SessionMessage::create([
                'room_id' => $room->id,
                'sender_type' => 'lex',
                'content' => '⚠️ The timer has expired, but an extension request is pending. A 2-minute grace period has started.',
                'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
            ]);
            // Dispatch a delayed check for report generation
            GenerateReportJob::dispatch($room->id)->delay(now()->addMinutes(2));
            return response()->json(['success' => true, 'status' => 'grace_period']);
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
            'payment_type' => 'nullable|in:full,split',
        ]);

        $room = Room::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        // Only party A can extend
        if (!$user || $room->party_a_id !== $user->id) {
            return response()->json(['error' => 'Only Party A can extend the session.'], 403);
        }

        if ($room->extensionCount() >= 3) {
            return response()->json(['error' => 'Maximum of 3 extensions per session reached.'], 403);
        }

        // Room must be completed (expired) or active
        if (!in_array($room->status, ['completed', 'active'])) {
            return response()->json(['error' => 'Session cannot be extended in its current state.'], 400);
        }

        if ($room->hasPendingExtension()) {
            return response()->json(['error' => 'There is already a pending extension request.'], 400);
        }

        $minutes = (int) $request->minutes;
        $paymentType = $request->input('payment_type', 'full');
        $blocks = $minutes / 30;
        $totalAmount = $blocks * self::EXTENSION_COST_PER_30_MIN;
        $requiredAmount = $paymentType === 'split' ? $totalAmount / 2 : $totalAmount;

        // Deduct from wallet
        $wallet = Wallet::where('user_id', $user->id)->first();
        if (!$wallet || $wallet->credits_balance < $requiredAmount) {
            return response()->json([
                'require_topup' => true,
                'amount' => $requiredAmount,
                'balance' => $wallet ? (float) $wallet->credits_balance : 0,
            ]);
        }

        if ($paymentType === 'split') {
            \App\Models\SessionExtensionRequest::create([
                'room_id' => $room->id,
                'initiator_id' => $user->id,
                'payment_type' => 'split',
                'minutes' => $minutes,
                'total_amount' => $totalAmount,
                'status' => 'pending_party_b'
            ]);

            SessionMessage::create([
                'room_id' => $room->id,
                'sender_type' => 'lex',
                'content' => "Party A requested a {$minutes}-minute extension and offered to split the cost ($" . number_format($totalAmount/2, 2) . " each). Party B, please accept or decline.",
                'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
            ]);

            return response()->json(['success' => true, 'split_requested' => true]);
        }

        DB::transaction(function () use ($room, $user, $wallet, $minutes, $totalAmount) {
            // Deduct wallet
            $wallet->decrement('credits_balance', $totalAmount);

            // Record extension for admin tracking
            SessionExtension::create([
                'room_id' => $room->id,
                'user_id' => $user->id,
                'minutes_added' => $minutes,
                'amount_paid' => $totalAmount,
                'status' => 'paid',
            ]);

            // Update room
            $wasCompleted = $room->status === 'completed';
            $room->update([
                'extended_minutes' => $room->extended_minutes + $minutes,
                'status' => 'active',
            ]);

            // If room was locked, clear ended_at so it's live again
            if ($wasCompleted) {
                $room->update(['ended_at' => null]);
            }

            // Refresh cache for new total
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
            'amount_paid' => $totalAmount,
            'extended_minutes' => (int) $room->extended_minutes,
            'timer' => [
                'remaining_seconds' => (int) $remainingSeconds,
                'total_seconds' => (int) $totalSeconds,
            ],
            'wallet_balance' => (float) $wallet->fresh()->credits_balance,
        ]);
    }

    public function acceptExtensionSplit(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        
        if (Auth::check() && $room->party_b_id !== Auth::id()) {
            return response()->json(['error' => 'Only Party B can accept this split.'], 403);
        }

        $extensionRequest = $room->extensionRequests()->where('status', 'pending_party_b')->latest()->first();
        if (!$extensionRequest) {
            return response()->json(['error' => 'No active split request found.'], 400);
        }

        $halfAmount = $extensionRequest->total_amount / 2;

        $walletB = Wallet::where('user_id', $room->party_b_id)->first();
        if (!$walletB || $walletB->credits_balance < $halfAmount) {
             return response()->json([
                'require_topup' => true,
                'amount' => $halfAmount,
                'balance' => $walletB ? (float) $walletB->credits_balance : 0,
            ]);
        }

        $walletA = Wallet::where('user_id', $room->party_a_id)->first();
        if (!$walletA || $walletA->credits_balance < $halfAmount) {
            return response()->json(['error' => 'Party A no longer has sufficient funds for this split.'], 400);
        }

        DB::transaction(function () use ($room, $walletA, $walletB, $extensionRequest, $halfAmount) {
            $walletA->decrement('credits_balance', $halfAmount);
            $walletB->decrement('credits_balance', $halfAmount);

            $extensionRequest->update(['status' => 'completed']);

            SessionExtension::create([
                'room_id' => $room->id,
                'user_id' => $room->party_a_id,
                'minutes_added' => $extensionRequest->minutes,
                'amount_paid' => $extensionRequest->total_amount,
                'status' => 'paid',
            ]);

            $wasCompleted = $room->status === 'completed';
            $room->update([
                'extended_minutes' => $room->extended_minutes + $extensionRequest->minutes,
                'status' => 'active',
            ]);

            if ($wasCompleted) {
                $room->update(['ended_at' => null]);
            }

            SessionMessage::create([
                'room_id' => $room->id,
                'sender_type' => 'lex',
                'content' => "⏱️ Party B accepted the split! Session extended by {$extensionRequest->minutes} minutes.",
                'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
            ]);
        });

        return response()->json(['success' => true]);
    }

    public function declineExtensionSplit(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)->firstOrFail();
        
        if (Auth::check() && $room->party_b_id !== Auth::id()) {
            return response()->json(['error' => 'Only Party B can decline this split.'], 403);
        }

        $extensionRequest = $room->extensionRequests()->where('status', 'pending_party_b')->latest()->first();
        if (!$extensionRequest) {
            return response()->json(['error' => 'No active split request found.'], 400);
        }

        $extensionRequest->update(['status' => 'declined']);

        SessionMessage::create([
            'room_id' => $room->id,
            'sender_type' => 'lex',
            'content' => "Party B has declined the extension split request. Party A may still pay in full to extend.",
            'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
        ]);

        return response()->json(['success' => true]);
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
        if (!$user) {
            return response()->json(['error' => 'You must be logged in to pause the session.'], 401);
        }

        if ((int)$room->party_a_id !== (int)$user->id) {
            return response()->json(['error' => 'Only the room creator can request a pause.'], 403);
        }

        if ($room->status !== 'active') {
            return response()->json(['error' => 'Session must be active to pause.'], 400);
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
