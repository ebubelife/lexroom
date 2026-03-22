<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'party_a_id', 'party_b_id', 'party_b_email', 'category',
        'jurisdiction', 'language', 'duration', 'status',
        'payment_type', 'case_summary', 'invite_token',
        'started_at', 'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($room) {
            if (!$room->uuid) {
                $room->uuid = Str::uuid();
            }
        });
    }

    public function partyA()
    {
        return $this->belongsTo(User::class, 'party_a_id');
    }

    public function partyB()
    {
        return $this->belongsTo(User::class, 'party_b_id');
    }

    public function billing()
    {
        return $this->hasMany(Billing::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }

    public function messages()
    {
        return $this->hasMany(SessionMessage::class);
    }

    public function evidence()
    {
        return $this->hasMany(EvidenceFile::class);
    }

    public function getCategoryBadgeColorAttribute()
    {
        $colors = [
            'tenancy' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dark_bg' => 'rgba(37,99,235,0.15)', 'dark_text' => '#93C5FD'],
            'freelance' => ['bg' => '#F0FDF4', 'text' => '#15803D', 'dark_bg' => 'rgba(22,163,74,0.15)', 'dark_text' => '#86EFAC'],
            'business' => ['bg' => '#FFF7ED', 'text' => '#C2410C', 'dark_bg' => 'rgba(234,88,12,0.15)', 'dark_text' => '#FDB976'],
            'ecommerce' => ['bg' => '#FDF4FF', 'text' => '#7E22CE', 'dark_bg' => 'rgba(147,51,234,0.15)', 'dark_text' => '#D8B4FE'],
            'employment' => ['bg' => '#FFF1F2', 'text' => '#BE123C', 'dark_bg' => 'rgba(225,29,72,0.15)', 'dark_text' => '#FDA4AF'],
            'debt' => ['bg' => '#F0FDF4', 'text' => '#0F766E', 'dark_bg' => 'rgba(20,184,166,0.15)', 'dark_text' => '#5EEAD4'],
        ];

        return $colors[$this->category] ?? $colors['business'];
    }

    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'active' => ['bg' => '#F0FDF4', 'text' => '#15803D', 'dark_bg' => 'rgba(22,163,74,0.15)', 'dark_text' => '#86EFAC'],
            'pending' => ['bg' => '#FFFBEB', 'text' => '#B45309', 'dark_bg' => 'rgba(245,158,11,0.15)', 'dark_text' => '#FCD34D'],
            'waiting_for_party_b' => ['bg' => '#EFF6FF', 'text' => '#1D4ED8', 'dark_bg' => 'rgba(37,99,235,0.15)', 'dark_text' => '#93C5FD'],
            'completed' => ['bg' => '#F4F4F2', 'text' => '#6B6B68', 'dark_bg' => 'rgba(255,255,255,0.08)', 'dark_text' => '#9BA8B4'],
            'escalated' => ['bg' => '#F5EDD6', 'text' => '#92400E', 'dark_bg' => 'rgba(201,168,76,0.15)', 'dark_text' => '#C9A84C'],
        ];

        return $colors[$this->status] ?? $colors['pending'];
    }
}
