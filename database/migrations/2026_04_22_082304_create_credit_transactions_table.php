<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2); // positive = credit, negative = debit
            $table->enum('type', [
                'subscription_grant',
                'topup',
                'session_deduction',
                'extension_deduction',
                'refund',
                'admin_adjustment',
                'referral_reward',
                'expiry',
            ]);
            $table->string('description');
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('balance_after', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_transactions');
    }
};
