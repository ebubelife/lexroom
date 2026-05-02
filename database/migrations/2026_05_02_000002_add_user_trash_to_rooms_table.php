<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->timestamp('user_deleted_at')->nullable()->after('archived_at');
            $table->enum('status', [
                'draft',
                'pending',
                'waiting_for_party_b',
                'active',
                'paused',
                'pause_requested',
                'locked',
                'completed',
                'escalated',
                'expired'
            ])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('user_deleted_at');
            $table->enum('status', [
                'pending',
                'waiting_for_party_b',
                'active',
                'paused',
                'pause_requested',
                'locked',
                'completed',
                'escalated',
                'expired'
            ])->default('pending')->change();
        });
    }
};
