<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lawyer extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'jurisdiction',
        'speciality',
        'bio',
        'bar_number',
        'years_experience',
        'commission_rate',
        'verified',
        'active',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'years_experience' => 'integer',
    ];

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }
}
