<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvidenceFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    public function index(Request $request)
    {
        $query = EvidenceFile::with(['room'])
            ->latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('original_filename', 'like', "%{$search}%")
                  ->orWhereHas('room', fn($r) => $r->where('case_id', 'like', "%{$search}%"));
            });
        }

        if ($party = $request->input('party')) {
            $query->where('party', $party);
        }

        if ($type = $request->input('type')) {
            $query->where('mime_type', 'like', "%{$type}%");
        }

        $files = $query->paginate(30)->withQueryString();

        // Storage overview
        $storage = [
            'total_files' => EvidenceFile::count(),
            'total_size'  => EvidenceFile::sum('size'),
            'by_type'     => EvidenceFile::select('mime_type', DB::raw('count(*) as count'), DB::raw('sum(size) as total_size'))
                                ->groupBy('mime_type')
                                ->orderByDesc('count')
                                ->limit(6)
                                ->get(),
        ];

        return view('admin.files.index', compact('files', 'storage'));
    }

    public function download(EvidenceFile $file)
    {
        if (!Storage::disk('local')->exists($file->path)) {
            return back()->with('error', 'File not found on disk.');
        }

        auth('admin')->user()->log('downloaded_evidence_file', 'EvidenceFile', $file->id, [
            'filename' => $file->original_filename,
            'room_id'  => $file->room_id,
        ]);

        return Storage::disk('local')->download($file->path, $file->original_filename);
    }

    public function destroy(EvidenceFile $file)
    {
        $filename = $file->original_filename;
        $roomId   = $file->room_id;

        // Delete from disk
        if (Storage::disk('local')->exists($file->path)) {
            Storage::disk('local')->delete($file->path);
        }

        auth('admin')->user()->log('deleted_evidence_file', 'EvidenceFile', $file->id, [
            'filename' => $filename,
            'room_id'  => $roomId,
        ]);

        $file->delete();

        return back()->with('success', "\"{$filename}\" deleted.");
    }
}
