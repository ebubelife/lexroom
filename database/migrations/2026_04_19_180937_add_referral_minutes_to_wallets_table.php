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
        Schema::table('wallets', function (Blueprint $table) {
            $table->integer('referral_minutes')->default(0)->after('credits_balance');
            $table->timestamp('referral_minutes_expires_at')->nullable()->after('referral_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn(['referral_minutes', 'referral_minutes_expires_at']);
        });
    }
};
