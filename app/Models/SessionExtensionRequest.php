<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionExtensionRequest extends Model
{
    protected $fillable = [
        'room_id', 'initiator_id', 'payment_type',
        'minutes', 'total_amount', 'status'
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }
}
