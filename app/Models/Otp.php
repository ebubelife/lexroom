<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $fillable = [
        'user_id', 'type', 'code', 'expires_at', 'verified_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isVerified()
    {
        return !is_null($this->verified_at);
    }

    public static function generateForUser($userId, $type)
    {
        // Delete any existing OTPs for this user and type
        static::where('user_id', $userId)
            ->where('type', $type)
            ->delete();

        // For phone OTP, always use 111111 for now
        $code = $type === 'phone' ? '111111' : str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);
    }
}
