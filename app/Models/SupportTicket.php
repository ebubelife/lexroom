<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    protected $fillable = [
        'uuid', 'user_id', 'name', 'email', 'subject',
        'type', 'status', 'priority', 'last_reply_at',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
    ];

    const TYPES = [
        'general'   => 'General Inquiry',
        'technical' => 'Technical Issue',
        'billing'   => 'Billing & Payments',
        'account'   => 'Account & Settings',
        'room'      => 'Mediation Room',
        'other'     => 'Other',
    ];

    const STATUSES = [
        'open'        => 'Open',
        'in_progress' => 'In Progress',
        'waiting'     => 'Waiting on You',
        'resolved'    => 'Resolved',
        'closed'      => 'Closed',
    ];

    const PRIORITIES = [
        'low'    => 'Low',
        'normal' => 'Normal',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            $ticket->uuid ??= Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id');
    }

    public function latestMessage()
    {
        return $this->hasOne(SupportMessage::class, 'ticket_id')->latestOfMany();
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? ucfirst($this->priority);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, ['open', 'in_progress', 'waiting']);
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'open'        => 'text-yellow-400; background: rgba(234,179,8,0.12); border-color: rgba(234,179,8,0.2)',
            'in_progress' => 'text-blue-400; background: rgba(59,130,246,0.12); border-color: rgba(59,130,246,0.2)',
            'waiting'     => 'text-purple-400; background: rgba(168,85,247,0.12); border-color: rgba(168,85,247,0.2)',
            'resolved'    => 'text-green-400; background: rgba(34,197,94,0.12); border-color: rgba(34,197,94,0.2)',
            'closed'      => 'text-gray-400; background: rgba(107,114,128,0.12); border-color: rgba(107,114,128,0.2)',
            default       => 'text-gray-400; background: rgba(107,114,128,0.12); border-color: rgba(107,114,128,0.2)',
        };
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'low'    => 'color:#9ca3af',
            'normal' => 'color:#60a5fa',
            'high'   => 'color:#fb923c',
            'urgent' => 'color:#f87171',
            default  => 'color:#9ca3af',
        };
    }
}
