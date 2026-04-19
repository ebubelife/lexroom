<?php

namespace App\Jobs;

use App\Models\Billing;
use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ExpireUnpaidSplitPayments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Find split rooms where Party B hasn't paid and link has expired
        $expiredRooms = Room::where('payment_type', 'split')
            ->where('party_a_paid', true)
            ->where('party_b_paid', false)
            ->where('status', 'awaiting_party_b_payment')
            ->where('party_b_payment_expires_at', '<', now())
            ->with('partyA.wallet')
            ->get();

        foreach ($expiredRooms as $room) {
            // Move Party A's payment to escrow
            $billing = Billing::where('room_id', $room->id)
                ->where('party', 'party_a')
                ->where('status', 'paid')
                ->first();

            if ($billing && $room->partyA?->wallet) {
                $room->partyA->wallet->holdInEscrow($billing->amount);
                Log::info("Moved ₦{$billing->amount} to escrow for room {$room->uuid}");
            }

            // Update room status
            $room->update(['status' => 'expired']);

            // Notify Party A
            $this->notifyPartyA($room);

            Log::info("Split payment expired for room {$room->uuid}");
        }
    }

    protected function notifyPartyA(Room $room): void
    {
        try {
            Mail::send('emails.split-payment-expired', ['room' => $room], function ($m) use ($room) {
                $m->to($room->partyA->email)
                  ->subject('Your mediation session has expired — funds held in Escrow');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send split expiry email: ' . $e->getMessage());
        }
    }
}
