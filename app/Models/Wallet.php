<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'credits_balance', 'referral_credits', 'escrow_balance',
        'referral_minutes', 'referral_minutes_expires_at',
    ];

    protected $casts = [
        'credits_balance'             => 'decimal:2',
        'referral_credits'            => 'decimal:2',
        'escrow_balance'              => 'decimal:2',
        'referral_minutes'            => 'integer',
        'referral_minutes_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Total spendable balance (credits + referral credits) ──────────
    public function totalBalance(): float
    {
        return (float) $this->credits_balance + (float) $this->referral_credits;
    }

    public function hasEnoughCredits(float $amount): bool
    {
        return $this->totalBalance() >= $amount;
    }

    /**
     * Deduct from referral_credits first, then credits_balance.
     */
    public function deductCredits(float $amount): void
    {
        $referralDeduction = min((float) $this->referral_credits, $amount);
        $mainDeduction     = $amount - $referralDeduction;

        if ($referralDeduction > 0) {
            $this->decrement('referral_credits', $referralDeduction);
        }
        if ($mainDeduction > 0) {
            $this->decrement('credits_balance', $mainDeduction);
        }
    }

    public function addCredits(float $amount): void
    {
        $this->increment('credits_balance', $amount);
    }

    public function addReferralCredits(float $amount): void
    {
        $this->increment('referral_credits', $amount);
    }

    // ── Legacy referral minutes (kept for backward compat) ───────────
    public function addReferralMinutes(int $minutes): void
    {
        $this->increment('referral_minutes', $minutes);
        $currentExpiry = $this->referral_minutes_expires_at;
        $newExpiry = ($currentExpiry && $currentExpiry->isFuture())
            ? $currentExpiry->addYear()
            : now()->addYear();
        $this->update(['referral_minutes_expires_at' => $newExpiry]);
    }

    public function deductReferralMinutes(int $minutes): bool
    {
        if ($this->referral_minutes < $minutes) return false;
        $this->decrement('referral_minutes', $minutes);
        return true;
    }

    public function hasReferralMinutes(): bool
    {
        return $this->referral_minutes > 0
            && $this->referral_minutes_expires_at
            && $this->referral_minutes_expires_at->isFuture();
    }

    // ── Escrow ───────────────────────────────────────────────────────
    public function moveEscrowToCredits(float $amount): void
    {
        $this->decrement('escrow_balance', $amount);
        $this->increment('credits_balance', $amount);
    }

    public function holdInEscrow(float $amount): void
    {
        $this->increment('escrow_balance', $amount);
    }

    public function releaseEscrow(float $amount): void
    {
        $this->decrement('escrow_balance', $amount);
    }

    public function getFormattedBalanceAttribute(): string
    {
        return '£' . number_format($this->totalBalance(), 2);
    }
}
