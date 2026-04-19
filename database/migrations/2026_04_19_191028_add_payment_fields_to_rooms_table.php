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
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('party_b_payment_token')->nullable()->after('invite_token');
            $table->timestamp('party_b_payment_expires_at')->nullable()->after('party_b_payment_token');
            $table->boolean('party_a_paid')->default(false)->after('party_b_payment_expires_at');
            $table->boolean('party_b_paid')->default(false)->after('party_a_paid');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['party_b_payment_token', 'party_b_payment_expires_at', 'party_a_paid', 'party_b_paid']);
        });
    }
};
