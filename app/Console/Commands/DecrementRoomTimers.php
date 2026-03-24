<?php

namespace App\Console\Commands;

use App\Jobs\GenerateReportJob;
use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class DecrementRoomTimers extends Command
{
    protected $signature = 'rooms:decrement-timers';
    protected $description = 'Decrement timers for all active rooms';

    public function handle()
    {
        $activeRooms = Room::where('status', 'active')->get();

        foreach ($activeRooms as $room) {
            $timerKey = "room:{$room->id}:timer";
            $remaining = Redis::get($timerKey);

            if ($remaining !== null && $remaining > 0) {
                Redis::decr($timerKey);
                
                // Check if time is up
                if ($remaining <= 1) {
                    $room->update(['status' => 'completed', 'ended_at' => now()]);
                    $this->info("Room {$room->uuid} completed - time expired");
                    
                    // Trigger report generation
                    GenerateReportJob::dispatch($room->id);
                    $this->info("Report generation queued for room {$room->uuid}");
                }
            }
        }

        return 0;
    }
}
