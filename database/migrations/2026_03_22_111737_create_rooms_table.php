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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('party_a_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('party_b_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('category', ['tenancy','freelance','business','ecommerce','employment','debt']);
            $table->string('jurisdiction');
            $table->string('language')->default('english');
            $table->enum('duration', ['30','60','90']);
            $table->enum('status', ['pending','waiting_for_party_b','active','completed','escalated','expired'])->default('pending');
            $table->enum('payment_type', ['full','split'])->default('full');
            $table->text('case_summary')->nullable();
            $table->string('invite_token')->nullable()->unique();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
