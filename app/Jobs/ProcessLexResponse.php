<?php

namespace App\Jobs;

use App\Models\Room;
use App\Models\SessionMessage;
use App\Contracts\AiProviderInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessLexResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $roomId;
    public $triggeredByMessageId;

    public function __construct($roomId, $triggeredByMessageId)
    {
        $this->roomId = $roomId;
        $this->triggeredByMessageId = $triggeredByMessageId;
    }

    public function handle(AiProviderInterface $claudeService): void
    {
        $room = Room::find($this->roomId);
        
        if (!$room) {
            Log::error("Room not found: {$this->roomId}");
            return;
        }

        try {
            // Get conversation history
            $messages = SessionMessage::where('room_id', $room->id)
                ->orderBy('created_at', 'asc')
                ->get();
                
            $claudeMessages = [];
            foreach ($messages as $m) {
                $claudeMessages[] = [
                    'sender_type' => $m->sender_type,
                    'content' => $m->content,
                ];
            }

            // Get parsed evidence texts
            $evidenceRecords = \App\Models\EvidenceFile::where('room_id', $room->id)
                ->whereNotNull('extracted_text')
                ->get();
            
            $evidenceTexts = [];
            foreach ($evidenceRecords as $e) {
                $party = $e->party === 'party_a' ? 'Party A' : 'Party B';
                $evidenceTexts[] = "Document Name: {$e->original_filename}\nUploaded by: {$party}\nContent:\n{$e->extracted_text}";
            }

            // Build context
            $context = [
                'category' => $room->category,
                'jurisdiction' => $room->jurisdiction,
                'language' => $room->language,
                'case_summary_a' => $room->case_summary ?? '',
                'case_summary_b' => '', // TODO: Get from Party B if provided
                'evidence_texts' => empty($evidenceTexts) ? '' : implode("\n\n---\n\n", $evidenceTexts),
            ];

            // Get Lex response
            $response = $claudeService->generateResponse($claudeMessages, $context);

            if ($response['success']) {
                // Save Lex response
                $currentPhase = Cache::get("room:{$room->id}:phase", 'opening');
                
                SessionMessage::create([
                    'room_id' => $room->id,
                    'sender_type' => 'lex',
                    'content' => $response['message'],
                    'phase' => $currentPhase,
                ]);
            } else {
                Log::error('Lex response failed', [
                    'room_id' => $room->id,
                    'error' => $response['error'] ?? 'Unknown error',
                ]);

                // Send error message so the user sees something
                SessionMessage::create([
                    'room_id' => $room->id,
                    'sender_type' => 'lex',
                    'content' => 'I apologize, but I encountered a technical issue. Please continue your discussion, and I will rejoin shortly.',
                    'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('ProcessLexResponse job threw an exception', [
                'room_id' => $room->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Always drop a visible error message so the chat doesn't appear frozen
            try {
                SessionMessage::create([
                    'room_id' => $room->id,
                    'sender_type' => 'lex',
                    'content' => 'I apologize, but I encountered a technical issue. Please continue your discussion, and I will rejoin shortly.',
                    'phase' => Cache::get("room:{$room->id}:phase", 'opening'),
                ]);
            } catch (\Throwable $inner) {
                Log::error('Could not save FM error message', ['error' => $inner->getMessage()]);
            }
        } finally {
            // Always clear the processing flag — no matter what happens above
            Cache::forget("room:{$room->id}:lex_processing");
        }
    }
}
