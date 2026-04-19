<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'case_id', 'party_a_id', 'party_b_id', 'party_b_email', 'category', 'title',
        'jurisdiction', 'language', 'duration', 'extended_minutes', 'status',
        'payment_type', 'case_summary', 'invite_token',
        'started_at', 'ended_at', 'party_b_clocked_in_at',
        'pause_requested_at', 'paused_at', 'total_paused_seconds'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'party_b_clocked_in_at' => 'datetime',
        'pause_requested_at' => 'datetime',
        'paused_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($room) {
            if (!$room->uuid) {
                $room->uuid = (string) Str::uuid();
            }
            
            if (!$room->case_id) {
                $room->case_id = $room->generateCaseId();
            }
        });
    }

    /**
     * Generate a structured Case ID: [CC]-[CAT]-[YY]-[SEQ]
     * e.g., GB-EMP-26-00001
     */
    public function generateCaseId()
    {
        // 1. Country Code (CC)
        $jurisdiction = strtolower($this->jurisdiction);
        $countryCode = 'INT'; // Default International
        
        $mapping = [
            'united kingdom' => 'GB',
            'uk'             => 'GB',
            'england'        => 'GB',
            'wales'          => 'GB',
            'scotland'       => 'GB',
            'nigeria'        => 'NG',
            'united states'  => 'US',
            'usa'            => 'US',
            'america'        => 'US',
            'germany'        => 'DE',
            'deutschland'    => 'DE',
            'france'         => 'FR',
            'canada'         => 'CA',
            'australia'      => 'AU',
        ];

        foreach ($mapping as $key => $code) {
            if (str_contains($jurisdiction, $key)) {
                $countryCode = $code;
                break;
            }
        }

        // 2. Category Code (CAT)
        $catMapping = [
            'tenancy'    => 'TEN',
            'freelance'  => 'FRE',
            'business'   => 'BUS',
            'ecommerce'  => 'ECO',
            'employment' => 'EMP',
            'debt'       => 'DBT',
        ];
        $categoryCode = $catMapping[$this->category] ?? 'GEN';

        // 3. Year (YY)
        $year = date('y');

        // 4. Sequence (SEQ)
        // Get the count of rooms created this year + 1
        $count = static::whereYear('created_at', date('Y'))->count() + 1;
        $sequence = str_pad($count, 5, '0', STR_PAD_LEFT);

        return "{$countryCode}-{$categoryCode}-{$year}-{$sequence}";
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

    public function evidenceFiles()
    {
        return $this->hasMany(EvidenceFile::class);
    }

    public function extensions()
    {
        return $this->hasMany(\App\Models\SessionExtension::class);
    }

    /**
     * Total duration including extensions (in minutes)
     */
    public function getTotalDurationAttribute(): int
    {
        return (int)($this->duration + $this->extended_minutes);
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
            'marriage' => ['bg' => '#FDF2F8', 'text' => '#DB2777', 'dark_bg' => 'rgba(219,39,119,0.15)', 'dark_text' => '#F9A8D4'],
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
            'pause_requested' => ['bg' => '#FEF2F2', 'text' => '#B91C1C', 'dark_bg' => 'rgba(185,28,28,0.15)', 'dark_text' => '#FCA5A5'],
            'paused' => ['bg' => '#F3F4F6', 'text' => '#4B5563', 'dark_bg' => 'rgba(75,85,99,0.15)', 'dark_text' => '#9CA3AF'],
        ];

        return $colors[$this->status] ?? $colors['pending'];
    }
}
