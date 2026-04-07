<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckPausedSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:paused-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for sessions paused for more than 24 hours and concludes them automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoff = now()->subHours(24);

        $expiredRooms = \App\Models\Room::where('status', 'paused')
            ->whereNotNull('paused_at')
            ->where('paused_at', '<', $cutoff)
            ->get();

        foreach ($expiredRooms as $room) {
            $room->update([
                'status' => 'completed',
                'ended_at' => now(),
            ]);

            \App\Models\SessionMessage::create([
                'room_id' => $room->id,
                'sender_type' => 'lex',
                'content' => "Session concluded on inconclusive grounds due to exceeding the maximum 24-hour pause duration.",
                'phase' => \Illuminate\Support\Facades\Cache::get("room:{$room->id}:phase", 'opening'),
            ]);

            $this->info("Room {$room->case_id} automatically concluded due to 24-hour pause limit.");
        }
    }
}
