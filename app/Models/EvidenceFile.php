<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EvidenceFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'party',
        'filename',
        'original_filename',
        'path',
        'mime_type',
        'size',
        'extracted_text',
        'is_locked',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getFileIconAttribute()
    {
        $extension = pathinfo($this->original_filename, PATHINFO_EXTENSION);
        
        $icons = [
            'pdf' => ['color' => '#DC2626', 'icon' => 'document'],
            'doc' => ['color' => '#2563EB', 'icon' => 'document'],
            'docx' => ['color' => '#2563EB', 'icon' => 'document'],
            'png' => ['color' => '#059669', 'icon' => 'photograph'],
            'jpg' => ['color' => '#059669', 'icon' => 'photograph'],
            'jpeg' => ['color' => '#059669', 'icon' => 'photograph'],
            'mp4' => ['color' => '#7C3AED', 'icon' => 'film'],
        ];

        return $icons[strtolower($extension)] ?? ['color' => '#6B7280', 'icon' => 'document'];
    }

    public function getPartyLabelAttribute()
    {
        return $this->party === 'party_a' ? 'Party A' : 'Party B';
    }
}
