<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('pending','awaiting_party_b_payment','waiting_for_party_b','active','timer_expired','pause_requested','paused','completed','escalated','expired') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE rooms MODIFY COLUMN status ENUM('pending','waiting_for_party_b','active','completed','escalated','expired') DEFAULT 'pending'");
    }
};
