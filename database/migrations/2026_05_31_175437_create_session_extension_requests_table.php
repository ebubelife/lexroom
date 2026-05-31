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
        Schema::create('session_extension_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('initiator_id')->constrained('users')->onDelete('cascade');
            $table->enum('payment_type', ['full', 'split']);
            $table->integer('minutes');
            $table->decimal('total_amount', 8, 2);
            $table->enum('status', ['pending_party_b', 'pending_top_up', 'completed', 'expired', 'declined']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_extension_requests');
    }
};
