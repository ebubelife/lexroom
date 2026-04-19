<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReportJob;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with('room.partyA')->latest('generated_at');

        if ($search = $request->input('search')) {
            $query->whereHas('room', fn($r) => $r->where('case_id', 'like', "%{$search}%"));
        }

        if ($status = $request->input('status')) {
            match ($status) {
                'generated' => $query->whereNotNull('pdf_path'),
                'pending'   => $query->whereNull('pdf_path'),
                default     => null,
            };
        }

        $reports = $query->paginate(25)->withQueryString();

        $stats = [
            'total'     => Report::count(),
            'generated' => Report::whereNotNull('pdf_path')->count(),
            'pending'   => Report::whereNull('pdf_path')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats'));
    }

    public function download(Report $report)
    {
        if (!$report->pdf_path || !Storage::exists($report->pdf_path)) {
            return back()->with('error', 'PDF not found on disk.');
        }

        auth('admin')->user()->log('downloaded_report', 'Report', $report->id, [
            'case_id' => $report->room?->case_id,
        ]);

        return Storage::download($report->pdf_path, "report-{$report->room?->case_id}.pdf");
    }

    public function regenerate(Report $report)
    {
        $room = $report->room;

        if (!$room) {
            return back()->with('error', 'Room not found.');
        }

        // Reset pdf_path so the job regenerates it
        $report->update(['pdf_path' => null, 'generated_at' => null]);

        GenerateReportJob::dispatch($room->id);

        auth('admin')->user()->log('regenerated_report', 'Report', $report->id, [
            'case_id' => $room->case_id,
        ]);

        return back()->with('success', "Report regeneration queued for {$room->case_id}.");
    }

    public function destroy(Report $report)
    {
        $caseId = $report->room?->case_id;

        if ($report->pdf_path && Storage::exists($report->pdf_path)) {
            Storage::delete($report->pdf_path);
        }

        auth('admin')->user()->log('deleted_report', 'Report', $report->id, [
            'case_id' => $caseId,
        ]);

        $report->delete();

        return back()->with('success', "Report for {$caseId} deleted.");
    }
}
