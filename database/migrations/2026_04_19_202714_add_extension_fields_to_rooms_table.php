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
            $table->enum('status', [
                'pending',
                'awaiting_party_b_payment',
                'active',
                'timer_expired',
                'completed',
                'expired',
            ])->default('pending')->change();

            $table->timestamp('timer_expired_at')->nullable()->after('ended_at');       // when timer hit 0
            $table->timestamp('extension_deadline')->nullable()->after('timer_expired_at'); // 24hr window
            $table->foreignId('extension_locked_by')->nullable()->constrained('users')->nullOnDelete()->after('extension_deadline');
            $table->timestamp('extension_locked_at')->nullable()->after('extension_locked_by');
            $table->integer('extension_minutes')->default(0)->after('extension_locked_at'); // total minutes added
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'timer_expired_at',
                'extension_deadline',
                'extension_locked_by',
                'extension_locked_at',
                'extension_minutes',
            ]);
        });
    }
};
