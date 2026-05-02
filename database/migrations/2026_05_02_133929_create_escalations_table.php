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
        Schema::create('escalations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');   // who escalated
            $table->foreignId('lawyer_id')->constrained()->onDelete('cascade');
            $table->text('message');                                             // user's message to lawyer
            $table->enum('status', ['pending', 'accepted', 'completed', 'declined'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['room_id', 'lawyer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escalations');
    }
};
