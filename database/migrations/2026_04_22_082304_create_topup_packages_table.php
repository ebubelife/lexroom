<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topup_packages', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->decimal('credits', 8, 2);
            $table->decimal('price', 8, 2);
            $table->decimal('bonus_credits', 8, 2)->default(0);
            $table->string('stripe_price_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topup_packages');
    }
};
