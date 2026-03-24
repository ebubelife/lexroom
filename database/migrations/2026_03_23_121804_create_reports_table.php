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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->text('case_summary');
            $table->text('party_a_position');
            $table->text('party_b_position');
            $table->text('evidence_reviewed')->nullable();
            $table->text('factual_findings');
            $table->text('contradictions')->nullable();
            $table->text('legal_framework')->nullable();
            $table->text('resolution_recommendation');
            $table->integer('confidence_score')->default(0); // 0-100
            $table->text('next_steps')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            
            $table->index('room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
