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
        Schema::create('session_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // e.g. Starter, Standard, Extended
            $table->integer('duration_minutes');             // 30, 60, 90
            $table->integer('full_price_pence');             // e.g. 4500 = £45.00
            $table->integer('split_price_pence');            // half of full
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_packages');
    }
};
