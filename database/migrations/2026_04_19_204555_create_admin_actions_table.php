<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->string('action');           // e.g. "suspended_user", "issued_refund"
            $table->string('target_type')->nullable(); // e.g. "User", "Room", "Billing"
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('meta')->nullable();   // extra context (reason, amount, etc.)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_actions');
    }
};
