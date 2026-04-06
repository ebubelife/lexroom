<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'role',
        'google_id',
        'email_verified_at',
        'phone_verified_at',
        'bvn',
        'nin',
        'profile_image',
        'google_avatar',
        'referral_code',
        'referred_by_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->referral_code) {
                $user->referral_code = self::generateUniqueReferralCode();
            }
            if ($user->first_name && $user->last_name) {
                $user->name = $user->first_name . ' ' . $user->last_name;
            }
        });

        static::saving(function ($user) {
            if ($user->isDirty(['first_name', 'last_name'])) {
                $user->name = $user->first_name . ' ' . $user->last_name;
            }
        });
    }

    public static function generateUniqueReferralCode()
    {
        do {
            $code = 'FM-' . strtoupper(Str::random(6));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return HasOne
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function rooms()
    {
        return Room::where('party_a_id', $this->id)->orWhere('party_b_id', $this->id);
    }

    public function getFirstNameAttribute()
    {
        return ($this->attributes['first_name'] ?? null) ?: explode(' ', $this->name ?? '')[0];
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function hasVerifiedPhone()
    {
        return !is_null($this->phone_verified_at);
    }

    public function isFullyVerified()
    {
        return $this->hasVerifiedEmail();
    }

    public function getFormattedPhoneAttribute()
    {
        return \App\Helpers\PhoneHelper::format($this->phone);
    }

    public function getInternationalPhoneAttribute()
    {
        return \App\Helpers\PhoneHelper::toInternational($this->phone);
    }

    public function getInitialsAttribute()
    {
        $firstName = $this->attributes['first_name'] ?? null;
        $lastName = $this->attributes['last_name'] ?? null;

        if ($firstName && $lastName) {
            return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
        }
        
        $names = explode(' ', $this->name ?? '');
        $initials = '';
        foreach ($names as $name) {
            if ($name) $initials .= strtoupper(substr($name, 0, 1));
        }
        return substr($initials, 0, 2) ?: '??';
    }

    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/' . $this->profile_image);
        }
        if ($this->google_avatar) {
            return $this->google_avatar;
        }
        return null;
    }

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }
}
