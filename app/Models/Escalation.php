<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escalation extends Model
{
    protected $fillable = [
        'room_id',
        'user_id',
        'lawyer_id',
        'message',
        'status',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function room()    { return $this->belongsTo(Room::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function lawyer()  { return $this->belongsTo(Lawyer::class); }
}
