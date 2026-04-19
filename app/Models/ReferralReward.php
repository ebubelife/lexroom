<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralReward extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_user_id',
        'status',
        'minutes_awarded',
        'awarded_at',
        'expires_at',
    ];

    protected $casts = [
        'awarded_at' => 'datetime',
        'expires_at' => 'datetime',
        'minutes_awarded' => 'integer',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_id');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }
}
