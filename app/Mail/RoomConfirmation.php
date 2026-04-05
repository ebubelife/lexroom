<?php

namespace App\Mail;

use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RoomConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $room;
    public $roomUrl;

    public function __construct(Room $room)
    {
        $this->room   = $room;
        $this->roomUrl = route('rooms.show', $room->uuid);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Mediation Room is Ready — Case #' . $this->room->case_id,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.room-confirmation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
