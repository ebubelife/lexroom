<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateReportJob;
use App\Models\Report;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportsController extends Controller
{
    public function index()
    {
        $reports = Report::with('room')
            ->whereHas('room', function ($query) {
                $query->where('party_a_id', auth()->id())
                    ->orWhere('party_b_email', auth()->user()->email);
            })
            ->orderBy('generated_at', 'desc')
            ->paginate(10);

        return view('reports.index', compact('reports'));
    }

    public function show(Report $report)
    {
        // Check authorization
        $room = $report->room;
        if ($room->party_a_id !== auth()->id() && $room->party_b_email !== auth()->user()->email) {
            abort(403);
        }

        return view('reports.show', compact('report'));
    }

    public function download(Report $report)
    {
        // Check authorization
        $room = $report->room;
        if ($room->party_a_id !== auth()->id() && $room->party_b_email !== auth()->user()->email) {
            abort(403);
        }

        if (!$report->pdf_path || !Storage::exists($report->pdf_path)) {
            return back()->with('error', 'Report PDF not found');
        }

        return Storage::download($report->pdf_path, "mediation-report-{$room->uuid}.pdf");
    }

    public function generate(Room $room)
    {
        // Check authorization
        if ($room->party_a_id !== auth()->id()) {
            abort(403);
        }

        // Check if room is completed
        if ($room->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Room must be completed before generating report',
            ], 400);
        }

        // Check if report already exists
        if ($room->report) {
            return response()->json([
                'success' => false,
                'message' => 'Report already generated for this room',
            ], 400);
        }

        // Dispatch report generation job
        GenerateReportJob::dispatch($room->id);

        return response()->json([
            'success' => true,
            'message' => 'Report generation started. You will receive an email when it\'s ready.',
        ]);
    }
}
