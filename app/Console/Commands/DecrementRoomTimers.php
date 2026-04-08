<?php

namespace App\Console\Commands;

use App\Jobs\GenerateReportJob;
use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class DecrementRoomTimers extends Command
{
    protected $signature = 'rooms:decrement-timers';
    protected $description = 'Decrement timers for all active rooms';

    public function handle()
    {
        $activeRooms = Room::where('status', 'active')->get();

        foreach ($activeRooms as $room) {
            // Calculate remaining seconds based on clock (original duration + extensions)
            $remaining = (($room->duration + $room->extended_minutes) * 60) - $room->started_at->diffInSeconds(now()) + (int)$room->total_paused_seconds;
            
            if ($remaining <= 0) {
                $room->update([
                    'status' => 'completed',
                    'ended_at' => now(),
                ]);
                
                // Clear state
                Cache::forget("room:{$room->id}:timer");
                Cache::forget("room:{$room->id}:phase");
                
                $this->info("Room {$room->id} marked as completed.");
                
                // Trigger report generation
                GenerateReportJob::dispatch($room->id);
                $this->info("Report generation queued for room {$room->uuid}");
            }
        }

        return 0;
    }
}
