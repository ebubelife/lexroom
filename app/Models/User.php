<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
        'email',
        'phone',
        'password',
        'role',
        'google_id',
    ];

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
     * @return array<string, string>
     */
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function rooms()
    {
        return Room::where('party_a_id', $this->id)->orWhere('party_b_id', $this->id);
    }

    public function getFirstNameAttribute()
    {
        return explode(' ', $this->name)[0];
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
        return $this->hasVerifiedEmail() && $this->hasVerifiedPhone();
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
        $names = explode(' ', $this->name);
        $initials = '';
        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }
        return substr($initials, 0, 2);
    }

    public function otps()
    {
        return $this->hasMany(Otp::class);
    }
}
