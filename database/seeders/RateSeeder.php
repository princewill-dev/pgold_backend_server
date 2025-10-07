<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rate;

class RateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
            // Cryptocurrencies
            [
                'asset_type' => 'crypto',
                'asset_name' => 'Bitcoin',
                'asset_code' => 'BTC',
                'buy_rate' => 50000000.00, // 50M NGN per BTC
                'sell_rate' => 49500000.00, // 49.5M NGN per BTC
                'currency' => 'NGN',
                'min_amount' => 0.001,
                'max_amount' => 10.0,
                'is_active' => true,
                'description' => 'Bitcoin (BTC) to Nigerian Naira conversion',
            ],
            [
                'asset_type' => 'crypto',
                'asset_name' => 'Ethereum',
                'asset_code' => 'ETH',
                'buy_rate' => 3500000.00, // 3.5M NGN per ETH
                'sell_rate' => 3450000.00, // 3.45M NGN per ETH
                'currency' => 'NGN',
                'min_amount' => 0.01,
                'max_amount' => 100.0,
                'is_active' => true,
                'description' => 'Ethereum (ETH) to Nigerian Naira conversion',
            ],
            [
                'asset_type' => 'crypto',
                'asset_name' => 'Tether USDT',
                'asset_code' => 'USDT',
                'buy_rate' => 1580.00, // 1,580 NGN per USDT
                'sell_rate' => 1550.00, // 1,550 NGN per USDT
                'currency' => 'NGN',
                'min_amount' => 10.0,
                'max_amount' => 100000.0,
                'is_active' => true,
                'description' => 'Tether USDT to Nigerian Naira conversion',
            ],
            [
                'asset_type' => 'crypto',
                'asset_name' => 'USD Coin',
                'asset_code' => 'USDC',
                'buy_rate' => 1580.00, // 1,580 NGN per USDC
                'sell_rate' => 1550.00, // 1,550 NGN per USDC
                'currency' => 'NGN',
                'min_amount' => 10.0,
                'max_amount' => 100000.0,
                'is_active' => true,
                'description' => 'USD Coin (USDC) to Nigerian Naira conversion',
            ],
            [
                'asset_type' => 'crypto',
                'asset_name' => 'Binance Coin',
                'asset_code' => 'BNB',
                'buy_rate' => 450000.00, // 450K NGN per BNB
                'sell_rate' => 445000.00, // 445K NGN per BNB
                'currency' => 'NGN',
                'min_amount' => 0.1,
                'max_amount' => 500.0,
                'is_active' => true,
                'description' => 'Binance Coin (BNB) to Nigerian Naira conversion',
            ],

            // Gift Cards
            [
                'asset_type' => 'gift_card',
                'asset_name' => 'Amazon Gift Card (USD)',
                'asset_code' => 'AMAZON_USD',
                'buy_rate' => 1400.00, // 1,400 NGN per USD
                'sell_rate' => 1350.00, // 1,350 NGN per USD
                'currency' => 'NGN',
                'min_amount' => 10.0,
                'max_amount' => 1000.0,
                'is_active' => true,
                'description' => 'Amazon Gift Card USD to Nigerian Naira conversion',
            ],
            [
                'asset_type' => 'gift_card',
                'asset_name' => 'iTunes Gift Card (USD)',
                'asset_code' => 'ITUNES_USD',
                'buy_rate' => 1350.00, // 1,350 NGN per USD
                'sell_rate' => 1300.00, // 1,300 NGN per USD
                'currency' => 'NGN',
                'min_amount' => 10.0,
                'max_amount' => 500.0,
                'is_active' => true,
                'description' => 'iTunes Gift Card USD to Nigerian Naira conversion',
            ],
            [
                'asset_type' => 'gift_card',
                'asset_name' => 'Google Play Gift Card (USD)',
                'asset_code' => 'GOOGLEPLAY_USD',
                'buy_rate' => 1300.00, // 1,300 NGN per USD
                'sell_rate' => 1250.00, // 1,250 NGN per USD
                'currency' => 'NGN',
                'min_amount' => 10.0,
                'max_amount' => 500.0,
                'is_active' => true,
                'description' => 'Google Play Gift Card USD to Nigerian Naira conversion',
            ],
            [
                'asset_type' => 'gift_card',
                'asset_name' => 'Steam Wallet Gift Card (USD)',
                'asset_code' => 'STEAM_USD',
                'buy_rate' => 1380.00, // 1,380 NGN per USD
                'sell_rate' => 1330.00, // 1,330 NGN per USD
                'currency' => 'NGN',
                'min_amount' => 10.0,
                'max_amount' => 500.0,
                'is_active' => true,
                'description' => 'Steam Wallet Gift Card USD to Nigerian Naira conversion',
            ],
            [
                'asset_type' => 'gift_card',
                'asset_name' => 'Visa Gift Card (USD)',
                'asset_code' => 'VISA_USD',
                'buy_rate' => 1450.00, // 1,450 NGN per USD
                'sell_rate' => 1400.00, // 1,400 NGN per USD
                'currency' => 'NGN',
                'min_amount' => 25.0,
                'max_amount' => 2000.0,
                'is_active' => true,
                'description' => 'Visa Gift Card USD to Nigerian Naira conversion',
            ],
            [
                'asset_type' => 'gift_card',
                'asset_name' => 'eBay Gift Card (USD)',
                'asset_code' => 'EBAY_USD',
                'buy_rate' => 1320.00, // 1,320 NGN per USD
                'sell_rate' => 1270.00, // 1,270 NGN per USD
                'currency' => 'NGN',
                'min_amount' => 10.0,
                'max_amount' => 1000.0,
                'is_active' => true,
                'description' => 'eBay Gift Card USD to Nigerian Naira conversion',
            ],
        ];

        foreach ($rates as $rate) {
            Rate::updateOrCreate(
                ['asset_code' => $rate['asset_code']],
                $rate
            );
        }

        $this->command->info('Rates seeded successfully!');
    }
}
