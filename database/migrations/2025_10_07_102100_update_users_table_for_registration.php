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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->string('full_name')->after('username');
            $table->string('phone_number')->unique()->after('email');
            $table->string('referral_code')->nullable()->after('phone_number');
            $table->renameColumn('name', 'name_backup');
        });

        // Remove the old name column after data migration if needed
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name_backup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropColumn(['username', 'full_name', 'phone_number', 'referral_code']);
        });
    }
};
