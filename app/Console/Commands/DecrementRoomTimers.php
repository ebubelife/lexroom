<?php

namespace App\Console\Commands;

use App\Jobs\AwardReferralMinutes;
use App\Jobs\GenerateReportJob;
use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DecrementRoomTimers extends Command
{
    protected $signature   = 'rooms:decrement-timers';
    protected $description = 'Decrement timers for active rooms and handle expiry/extension windows';

    public function handle()
    {
        // 1. Decrement active room timers
        $activeRooms = Room::where('status', 'active')->get();

        foreach ($activeRooms as $room) {
            $remaining = (($room->duration + $room->extension_minutes) * 60)
                - $room->started_at->diffInSeconds(now())
                + (int) $room->total_paused_seconds;

            if ($remaining <= 0) {
                // Timer hit 0 — enter extension window
                $room->update([
                    'status'             => 'timer_expired',
                    'timer_expired_at'   => now(),
                    'extension_deadline' => now()->addHours(24),
                ]);

                Cache::forget("room:{$room->id}:timer");
                Cache::forget("room:{$room->id}:phase");

                $this->info("Room {$room->uuid} timer expired — 24hr extension window started");
            }
        }

        // 2. Check timer_expired rooms — has 24hr window passed?
        $expiredRooms = Room::where('status', 'timer_expired')
            ->where('extension_deadline', '<', now())
            ->get();

        foreach ($expiredRooms as $room) {
            $room->update([
                'status'   => 'completed',
                'ended_at' => $room->timer_expired_at ?? now(),
            ]);

            GenerateReportJob::dispatch($room->id);
            AwardReferralMinutes::dispatch($room->id);

            $this->info("Room {$room->uuid} completed — extension window expired");
        }

        // 3. Release stale extension locks (abandoned payments after 10 mins)
        Room::where('status', 'timer_expired')
            ->whereNotNull('extension_locked_by')
            ->where('extension_locked_at', '<', now()->subMinutes(10))
            ->update([
                'extension_locked_by' => null,
                'extension_locked_at' => null,
            ]);

        return 0;
    }
}
