<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionPackage extends Model
{
    protected $fillable = [
        'name',
        'duration_minutes',
        'full_price_pence',
        'split_price_pence',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Helpers — return price in pounds (for display)
    public function getFullPriceAttribute(): float
    {
        return $this->full_price_pence / 100;
    }

    public function getSplitPriceAttribute(): float
    {
        return $this->split_price_pence / 100;
    }

    public static function forDuration(int $minutes): ?self
    {
        return static::where('duration_minutes', $minutes)
            ->where('is_active', true)
            ->first();
    }
}
