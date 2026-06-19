<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename legacy generic 'admin' role to 'platform_admin'
        DB::table('admins')->where('role', 'admin')->update(['role' => 'platform_admin']);

        Schema::table('admins', function (Blueprint $table) {
            $table->string('role')
                ->default('platform_admin')
                ->comment('super_admin|platform_admin|billing_admin|case_manager|document_reviewer|lawyer_manager|support_agent|auditor')
                ->change();
        });
    }

    public function down(): void
    {
        DB::table('admins')->where('role', 'platform_admin')->update(['role' => 'admin']);

        Schema::table('admins', function (Blueprint $table) {
            $table->string('role')->default('admin')->change();
        });
    }
};
