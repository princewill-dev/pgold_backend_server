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
        Schema::create('referral_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who referred (the referrer)');
            $table->foreignId('referred_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('User who was referred (the referee)');
            $table->string('referral_code_used', 50)
                ->comment('The actual referral code that was used');
            $table->timestamp('referred_at')
                ->useCurrent()
                ->comment('When the referral happened');
            $table->timestamps();

            // Indexes
            $table->index('referrer_id');
            $table->index('referred_id');
            $table->index('referral_code_used');
            
            // Ensure a user can only be referred once
            $table->unique('referred_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_relationships');
    }
};
