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
        Schema::create('rates', function (Blueprint $table) {
            $table->id();
            $table->string('asset_type', 50)->comment('crypto, gift_card');
            $table->string('asset_name', 100)->comment('Bitcoin, Amazon Gift Card, etc.');
            $table->string('asset_code', 20)->unique()->comment('BTC, ETH, AMAZON_USD, etc.');
            $table->decimal('buy_rate', 18, 8)->comment('Rate for buying from user');
            $table->decimal('sell_rate', 18, 8)->comment('Rate for selling to user');
            $table->string('currency', 10)->default('NGN')->comment('Base currency (NGN, USD, etc.)');
            $table->decimal('min_amount', 18, 8)->nullable()->comment('Minimum transaction amount');
            $table->decimal('max_amount', 18, 8)->nullable()->comment('Maximum transaction amount');
            $table->boolean('is_active')->default(true)->comment('Whether this rate is active');
            $table->text('description')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('asset_type');
            $table->index('asset_code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rates');
    }
};
