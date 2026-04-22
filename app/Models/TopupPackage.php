<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupPackage extends Model
{
    protected $fillable = [
        'label', 'credits', 'price', 'bonus_credits', 'stripe_price_id', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'credits'       => 'decimal:2',
        'price'         => 'decimal:2',
        'bonus_credits' => 'decimal:2',
        'is_active'     => 'boolean',
    ];

    public function totalCredits(): float
    {
        return (float) $this->credits + (float) $this->bonus_credits;
    }

    public function bonusPercent(): int
    {
        if ($this->credits <= 0) return 0;
        return (int) round(($this->bonus_credits / $this->credits) * 100);
    }
}
