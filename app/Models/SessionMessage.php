<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionMessage extends Model
{
    protected $fillable = [
        'room_id',
        'sender_type',
        'content',
        'phase',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
