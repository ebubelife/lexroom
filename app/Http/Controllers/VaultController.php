<?php

namespace App\Http\Controllers;

use App\Models\EvidenceFile;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VaultController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // All room IDs the user is part of (creator OR party B)
        $roomIds = Room::where('party_a_id', $user->id)
            ->orWhere('party_b_id', $user->id)
            ->pluck('id');

        $query = EvidenceFile::with('room')
            ->whereIn('room_id', $roomIds)
            ->latest();

        // Search by filename
        if ($search = $request->get('q')) {
            $query->where('original_filename', 'like', "%{$search}%");
        }

        // Filter by file type
        if ($type = $request->get('type')) {
            $mimeMap = [
                'pdf'   => ['application/pdf'],
                'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                'doc'   => ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
                'video' => ['video/mp4', 'video/quicktime', 'video/webm'],
            ];
            if (isset($mimeMap[$type])) {
                $query->whereIn('mime_type', $mimeMap[$type]);
            }
        }

        // Filter by case/room
        if ($roomId = $request->get('room_id')) {
            $query->where('room_id', $roomId);
        }

        $files = $query->paginate(18);

        // Stats
        $totalFiles = EvidenceFile::whereIn('room_id', $roomIds)->count();
        $totalSize  = EvidenceFile::whereIn('room_id', $roomIds)->sum('size');

        // Rooms for the filter dropdown
        $rooms = Room::whereIn('id', $roomIds)->latest()->get();

        return view('vault.index', compact('files', 'totalFiles', 'totalSize', 'rooms'));
    }

    public function download(EvidenceFile $file)
    {
        $user = auth()->user();

        // Ensure the user owns or is party of the room
        $room = $file->room;
        if ($room->party_a_id !== $user->id && $room->party_b_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        if (!Storage::exists($file->path)) {
            abort(404, 'File not found');
        }

        return Storage::download($file->path, $file->original_filename);
    }
}
