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
        Schema::create('lawyers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('jurisdiction'); // Nigeria, UK, South Africa, Ghana
            $table->string('speciality'); // Tenancy, Freelance, Business, etc.
            $table->text('bio')->nullable();
            $table->string('bar_number')->nullable();
            $table->integer('years_experience')->default(0);
            $table->decimal('commission_rate', 5, 2)->default(20.00); // 20%
            $table->boolean('verified')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['jurisdiction', 'speciality', 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lawyers');
    }
};
