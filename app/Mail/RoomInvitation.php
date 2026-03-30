<?php

namespace App\Mail;

use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class RoomInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $room;
    public $roomLink;

    public function __construct(Room $room)
    {
        $this->room = $room;
        $this->roomLink = URL::signedRoute('rooms.show', [
            'uuid' => $room->uuid,
            'token' => $room->invite_token
        ], now()->addDays(7));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'ve been invited to a FirstMediator mediation session',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.room-invitation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
