<?php

namespace App\Http\Controllers;

use App\Models\EvidenceFile;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvidenceController extends Controller
{
    /**
     * Upload evidence file
     */
    public function upload(Request $request, $roomUuid)
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();

        // Block uploads if session is finalized
        if ($room->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot upload evidence after the session has ended',
            ], 403);
        }

        // Validate file
        $request->validate([
            'file' => 'required|file|max:20480|mimes:pdf,txt,csv,md,doc,docx,png,jpg,jpeg,mp4', // 20MB max
        ]);

        // Check file count limit (20 files per session)
        $fileCount = EvidenceFile::where('room_id', $room->id)->count();
        if ($fileCount >= 20) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum file limit reached (20 files per session)',
            ], 422);
        }

        // Determine party
        $party = 'party_a';
        if (auth()->check()) {
            if ($room->party_a_id === auth()->id()) {
                $party = 'party_a';
            } elseif ($room->party_b_id === auth()->id()) {
                $party = 'party_b';
            }
        } else {
            // Guest user (Party B)
            if ($request->has('token') && $request->token === $room->invite_token) {
                $party = 'party_b';
            }
        }

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        
        // Store file in storage/app/evidence/{room_uuid}/
        $path = $file->storeAs("evidence/{$room->uuid}", $filename, 'local');

        // Create evidence record
        $evidence = EvidenceFile::create([
            'room_id' => $room->id,
            'party' => $party,
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        // Dispatch text extraction for pdf/txt
        \App\Jobs\ProcessEvidenceFile::dispatch($evidence->id);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'file' => [
                'id' => $evidence->id,
                'filename' => $evidence->original_filename,
                'size' => $evidence->formatted_size,
                'party' => $evidence->party_label,
                'icon' => $evidence->file_icon,
                'uploaded_at' => $evidence->created_at->format('M j, Y g:i A'),
            ],
        ]);
    }

    /**
     * Download evidence file
     */
    public function download($roomUuid, $fileId)
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $evidence = EvidenceFile::where('room_id', $room->id)
            ->where('id', $fileId)
            ->firstOrFail();

        // Check access
        if (!$this->canAccessRoom($room)) {
            abort(403, 'Unauthorized access');
        }

        return Storage::download($evidence->path, $evidence->original_filename);
    }

    /**
     * Delete evidence file (only before session ends)
     */
    public function delete($roomUuid, $fileId)
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();
        $evidence = EvidenceFile::where('room_id', $room->id)
            ->where('id', $fileId)
            ->firstOrFail();

        // Check if file is locked or session ended
        if ($evidence->is_locked || $room->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete evidence after session has ended',
            ], 403);
        }

        // Check if user owns this file
        $party = $this->getUserParty($room);
        if ($evidence->party !== $party) {
            return response()->json([
                'success' => false,
                'message' => 'You can only delete your own files',
            ], 403);
        }

        // Delete file from storage
        Storage::delete($evidence->path);

        // Delete record
        $evidence->delete();

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully',
        ]);
    }

    /**
     * Get all evidence files for a room
     */
    public function index($roomUuid)
    {
        $room = Room::where('uuid', $roomUuid)->firstOrFail();

        if (!$this->canAccessRoom($room)) {
            abort(403, 'Unauthorized access');
        }

        $files = EvidenceFile::where('room_id', $room->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($file) {
                return [
                    'id' => $file->id,
                    'filename' => $file->original_filename,
                    'size' => $file->formatted_size,
                    'party' => $file->party_label,
                    'icon' => $file->file_icon,
                    'uploaded_at' => $file->created_at->format('M j, Y g:i A'),
                    'is_locked' => $file->is_locked,
                ];
            });

        return response()->json([
            'success' => true,
            'files' => $files,
            'count' => $files->count(),
            'limit' => 20,
        ]);
    }

    /**
     * Check if user can access room
     */
    protected function canAccessRoom($room): bool
    {
        if (auth()->check()) {
            return $room->party_a_id === auth()->id() || $room->party_b_id === auth()->id();
        }

        // Check for valid invite token
        return request()->has('token') && request('token') === $room->invite_token;
    }

    /**
     * Get user's party in the room
     */
    protected function getUserParty($room): string
    {
        if (auth()->check()) {
            if ($room->party_a_id === auth()->id()) {
                return 'party_a';
            } elseif ($room->party_b_id === auth()->id()) {
                return 'party_b';
            }
        }

        // Guest user (Party B)
        if (request()->has('token') && request('token') === $room->invite_token) {
            return 'party_b';
        }

        return 'party_a'; // Default
    }

    /**
     * Extract text from PDF/DOCX (placeholder for future implementation)
     */
    protected function extractText($evidence)
    {
        // TODO: Implement PDF/DOCX text extraction
        // Can use packages like smalot/pdfparser or spatie/pdf-to-text
    }
}
