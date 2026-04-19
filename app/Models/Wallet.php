<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'credits_balance', 'referral_minutes', 'referral_minutes_expires_at'];

    protected $casts = [
        'credits_balance' => 'decimal:2',
        'referral_minutes' => 'integer',
        'referral_minutes_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedBalanceAttribute()
    {
        return '₦' . number_format($this->credits_balance, 0);
    }

    public function addReferralMinutes(int $minutes): void
    {
        $this->increment('referral_minutes', $minutes);
        // Extend expiry by 12 months from now (or from current expiry if not yet expired)
        $currentExpiry = $this->referral_minutes_expires_at;
        $newExpiry = ($currentExpiry && $currentExpiry->isFuture())
            ? $currentExpiry->addYear()
            : now()->addYear();
        $this->update(['referral_minutes_expires_at' => $newExpiry]);
    }

    public function deductReferralMinutes(int $minutes): bool
    {
        if ($this->referral_minutes < $minutes) {
            return false;
        }
        $this->decrement('referral_minutes', $minutes);
        return true;
    }

    public function hasReferralMinutes(): bool
    {
        return $this->referral_minutes > 0
            && $this->referral_minutes_expires_at
            && $this->referral_minutes_expires_at->isFuture();
    }
}
