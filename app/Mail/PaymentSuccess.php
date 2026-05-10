<?php

namespace App\Mail;

use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccess extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Room $room,
        public string $party, // 'party_a' or 'party_b'
        public float $amount
    ) {}

    public function build()
    {
        $partyLabel = $this->party === 'party_a' ? 'Party A' : 'Party B';
        $subject = "Payment Confirmed — {$this->room->case_id} Mediation Session";

        return $this->subject($subject)
                    ->view('emails.payment-success')
                    ->with([
                        'room' => $this->room,
                        'party' => $this->party,
                        'partyLabel' => $partyLabel,
                        'amount' => $this->amount,
                    ]);
    }
}