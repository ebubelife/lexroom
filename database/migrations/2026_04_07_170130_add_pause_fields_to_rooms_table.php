<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->timestamp('pause_requested_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->integer('total_paused_seconds')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['pause_requested_at', 'paused_at', 'total_paused_seconds']);
        });
    }
};
