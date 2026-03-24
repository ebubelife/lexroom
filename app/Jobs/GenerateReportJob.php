<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\Room;
use App\Models\SessionMessage;
use App\Services\ClaudeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $roomId;

    public function __construct($roomId)
    {
        $this->roomId = $roomId;
    }

    public function handle(ClaudeService $claudeService): void
    {
        $room = Room::with(['partyA', 'partyB', 'messages', 'evidence'])->find($this->roomId);
        
        if (!$room) {
            Log::error("Room not found for report generation: {$this->roomId}");
            return;
        }

        // Get conversation history
        $messages = $room->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'sender' => $msg->sender_type,
                    'content' => $msg->content,
                    'phase' => $msg->phase,
                ];
            })
            ->toArray();

        // Get evidence list
        $evidence = $room->evidence->map(function ($file) {
            return [
                'filename' => $file->filename,
                'party' => $file->party,
                'extracted_text' => $file->extracted_text,
            ];
        })->toArray();

        // Build context
        $context = [
            'category' => $room->category,
            'jurisdiction' => $room->jurisdiction,
            'language' => $room->language,
            'case_summary_a' => $room->case_summary ?? '',
            'case_summary_b' => '',
        ];

        // Generate report using Claude
        $reportData = $claudeService->generateMediationReport($messages, $evidence, $context);

        if (!$reportData['success']) {
            Log::error('Report generation failed', [
                'room_id' => $room->id,
                'error' => $reportData['error'] ?? 'Unknown error',
            ]);
            return;
        }

        // Parse Claude's response to extract sections
        $reportContent = $reportData['message'];
        $parsedReport = $this->parseReportContent($reportContent);

        // Create report record
        $report = Report::create([
            'room_id' => $room->id,
            'case_summary' => $parsedReport['case_summary'] ?? 'N/A',
            'party_a_position' => $parsedReport['party_a_position'] ?? 'N/A',
            'party_b_position' => $parsedReport['party_b_position'] ?? 'N/A',
            'evidence_reviewed' => $parsedReport['evidence_reviewed'] ?? 'N/A',
            'factual_findings' => $parsedReport['factual_findings'] ?? 'N/A',
            'contradictions' => $parsedReport['contradictions'] ?? 'None identified',
            'legal_framework' => $parsedReport['legal_framework'] ?? 'N/A',
            'resolution_recommendation' => $parsedReport['resolution_recommendation'] ?? 'N/A',
            'confidence_score' => $parsedReport['confidence_score'] ?? 0,
            'next_steps' => $parsedReport['next_steps'] ?? 'N/A',
            'generated_at' => now(),
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('reports.template', [
            'room' => $room,
            'report' => $report,
            'messages' => $messages,
            'evidence' => $evidence,
        ]);

        // Save PDF
        $filename = "mediation-report-{$room->uuid}.pdf";
        $path = "reports/{$filename}";
        Storage::put($path, $pdf->output());

        // Update report with PDF path
        $report->update(['pdf_path' => $path]);

        // Email PDF to both parties
        $this->emailReport($room, $report, $path);

        Log::info("Report generated successfully for room {$room->uuid}");
    }

    protected function parseReportContent(string $content): array
    {
        $sections = [];
        
        // Simple parsing - extract sections based on headers
        $patterns = [
            'case_summary' => '/Case Summary[:\s]+(.*?)(?=Party A\'s Position|$)/is',
            'party_a_position' => '/Party A\'s Position[:\s]+(.*?)(?=Party B\'s Position|$)/is',
            'party_b_position' => '/Party B\'s Position[:\s]+(.*?)(?=Evidence Reviewed|$)/is',
            'evidence_reviewed' => '/Evidence Reviewed[:\s]+(.*?)(?=Factual Findings|$)/is',
            'factual_findings' => '/Factual Findings[:\s]+(.*?)(?=Contradictions|$)/is',
            'contradictions' => '/Contradictions[:\s]+(.*?)(?=Legal Framework|$)/is',
            'legal_framework' => '/Legal Framework[:\s]+(.*?)(?=Resolution Recommendation|$)/is',
            'resolution_recommendation' => '/Resolution Recommendation[:\s]+(.*?)(?=Confidence Score|$)/is',
            'next_steps' => '/Next Steps[:\s]+(.*?)$/is',
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $sections[$key] = trim($matches[1]);
            }
        }

        // Extract confidence score
        if (preg_match('/Confidence Score[:\s]+(\d+)%?/i', $content, $matches)) {
            $sections['confidence_score'] = (int) $matches[1];
        }

        return $sections;
    }

    protected function emailReport(Room $room, Report $report, string $path): void
    {
        $pdfContent = Storage::get($path);
        
        // Email to Party A
        if ($room->partyA && $room->partyA->email) {
            Mail::send('emails.report', ['room' => $room, 'report' => $report], function ($message) use ($room, $pdfContent) {
                $message->to($room->partyA->email)
                    ->subject('FirstMediator - Mediation Report')
                    ->attachData($pdfContent, "mediation-report-{$room->uuid}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });
        }

        // Email to Party B
        if ($room->party_b_email) {
            Mail::send('emails.report', ['room' => $room, 'report' => $report], function ($message) use ($room, $pdfContent) {
                $message->to($room->party_b_email)
                    ->subject('FirstMediator - Mediation Report')
                    ->attachData($pdfContent, "mediation-report-{$room->uuid}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });
        }
    }
}
