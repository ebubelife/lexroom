<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Room;
use App\Services\ClaudeService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $reports = Report::with('room.partyA')
            ->whereHas('room', function ($q) use ($user) {
                $q->where('party_a_id', $user->id)
                  ->orWhere('party_b_email', $user->email);
            })
            ->latest('generated_at')
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    /**
     * Party A — generate inline and download immediately.
     * If PDF already exists, just stream it.
     */
    public function download(Report $report)
    {
        $room = $report->room;
        $user = auth()->user();

        if ($room->party_a_id !== $user->id && $room->party_b_email !== $user->email) {
            abort(403);
        }

        return $this->streamOrGenerate($report, $room);
    }

    /**
     * Generate report for a locked room (Party A only).
     * Creates the Report record if it doesn't exist, then generates PDF inline.
     */
    public function generate(Room $room, ClaudeService $claude)
    {
        if ($room->party_a_id !== auth()->id()) {
            abort(403);
        }

        if (!in_array($room->status, ['locked', 'completed', 'expired'])) {
            return back()->with('error', 'Session must be ended before generating a report.');
        }

        // If report already exists just download it
        if ($room->report) {
            return $this->streamOrGenerate($room->report, $room);
        }

        $report = $this->buildReport($room, $claude);

        if (!$report) {
            return back()->with('error', 'Report generation failed. Please try again.');
        }

        return $this->streamOrGenerate($report, $room);
    }

    /**
     * Party B public download — validated by invite token, no login required.
     */
    public function partyBDownload(Request $request, $uuid)
    {
        $room = Room::where('uuid', $uuid)
            ->whereIn('status', ['locked', 'completed', 'expired'])
            ->firstOrFail();

        // Validate token
        if ($request->input('token') !== $room->invite_token) {
            abort(403, 'Invalid access token.');
        }

        if (!$room->report) {
            return back()->with('error', 'Report not yet available for this session.');
        }

        return $this->streamOrGenerate($room->report, $room);
    }

    // ─────────────────────────────────────────────
    // Internals
    // ─────────────────────────────────────────────

    private function streamOrGenerate(Report $report, Room $room)
    {
        // PDF already on disk — stream it
        if ($report->pdf_path && Storage::exists($report->pdf_path)) {
            return Storage::download(
                $report->pdf_path,
                "firstmediator-report-{$room->case_id}.pdf"
            );
        }

        // Regenerate PDF from existing report data
        $pdf = $this->renderPdf($report, $room);
        $path = "reports/firstmediator-report-{$room->uuid}.pdf";
        Storage::put($path, $pdf->output());
        $report->update(['pdf_path' => $path]);

        return response($pdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"firstmediator-report-{$room->case_id}.pdf\"",
        ]);
    }

    private function buildReport(Room $room, ClaudeService $claude): ?Report
    {
        $room->load(['partyA', 'partyB', 'messages', 'evidenceFiles']);

        $messages = $room->messages()
            ->orderBy('created_at')
            ->get()
            ->map(fn($m) => [
                'sender'  => $m->sender_type,
                'content' => $m->content,
                'phase'   => $m->phase,
            ])->toArray();

        $evidence = $room->evidenceFiles->map(fn($f) => [
            'filename'       => $f->original_filename,
            'party'          => $f->party,
            'extracted_text' => $f->extracted_text,
        ])->toArray();

        $context = [
            'category'       => $room->category,
            'jurisdiction'   => $room->jurisdiction,
            'language'       => $room->language,
            'case_summary_a' => $room->case_summary ?? '',
            'case_summary_b' => '',
        ];

        $result = $claude->generateMediationReport($messages, $evidence, $context);

        if (!$result['success']) {
            return null;
        }

        $parsed = $this->parseReportContent($result['message']);

        $report = Report::create([
            'room_id'                  => $room->id,
            'case_summary'             => $parsed['case_summary']             ?? 'N/A',
            'party_a_position'         => $parsed['party_a_position']         ?? 'N/A',
            'party_b_position'         => $parsed['party_b_position']         ?? 'N/A',
            'evidence_reviewed'        => $parsed['evidence_reviewed']        ?? 'N/A',
            'factual_findings'         => $parsed['factual_findings']         ?? 'N/A',
            'contradictions'           => $parsed['contradictions']           ?? 'None identified',
            'legal_framework'          => $parsed['legal_framework']          ?? 'N/A',
            'resolution_recommendation'=> $parsed['resolution_recommendation']?? 'N/A',
            'confidence_score'         => $parsed['confidence_score']         ?? 0,
            'next_steps'               => $parsed['next_steps']               ?? 'N/A',
            'generated_at'             => now(),
        ]);

        // Generate and save PDF
        $pdf  = $this->renderPdf($report, $room);
        $path = "reports/firstmediator-report-{$room->uuid}.pdf";
        Storage::put($path, $pdf->output());
        $report->update(['pdf_path' => $path]);

        return $report;
    }

    private function renderPdf(Report $report, Room $room): \Barryvdh\DomPDF\PDF
    {
        $room->loadMissing(['partyA', 'partyB', 'messages', 'evidenceFiles']);

        // Group messages by phase for the transcript summary
        $transcript = $room->messages()
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($m) => $m->phase ?: 'opening');

        $evidence = $room->evidenceFiles;

        return Pdf::loadView('reports.template', compact('room', 'report', 'transcript', 'evidence'))
            ->setPaper('a4', 'portrait');
    }

    private function parseReportContent(string $content): array
    {
        $sections = [];

        $patterns = [
            'case_summary'              => '/Case Summary[:\s]+(.*?)(?=Party A|$)/is',
            'party_a_position'          => '/Party A\'s Position[:\s]+(.*?)(?=Party B|$)/is',
            'party_b_position'          => '/Party B\'s Position[:\s]+(.*?)(?=Evidence Reviewed|$)/is',
            'evidence_reviewed'         => '/Evidence Reviewed[:\s]+(.*?)(?=Factual Findings|$)/is',
            'factual_findings'          => '/Factual Findings[:\s]+(.*?)(?=Contradictions|$)/is',
            'contradictions'            => '/Contradictions[:\s]+(.*?)(?=Legal Framework|$)/is',
            'legal_framework'           => '/Legal Framework[:\s]+(.*?)(?=Resolution Recommendation|$)/is',
            'resolution_recommendation' => '/Resolution Recommendation[:\s]+(.*?)(?=Confidence Score|$)/is',
            'next_steps'                => '/Next Steps[:\s]+(.*?)$/is',
        ];

        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $content, $matches)) {
                $sections[$key] = trim($matches[1]);
            }
        }

        if (preg_match('/Confidence Score[:\s]+(\d+)%?/i', $content, $matches)) {
            $sections['confidence_score'] = (int) $matches[1];
        }

        return $sections;
    }
}
