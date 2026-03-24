<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'room_id',
        'case_summary',
        'party_a_position',
        'party_b_position',
        'evidence_reviewed',
        'factual_findings',
        'contradictions',
        'legal_framework',
        'resolution_recommendation',
        'confidence_score',
        'next_steps',
        'pdf_path',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'confidence_score' => 'integer',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
