<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalculateRateRequest;
use App\Http\Requests\GetRatesRequest;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RateController extends Controller
{
    /**
     * Get all rates or filter by asset type.
     *
     * @param GetRatesRequest $request
     * @return JsonResponse
     */
    public function getRates(GetRatesRequest $request): JsonResponse
    {
        try {
            $query = Rate::active();

            // Filter by asset type if provided
            if ($request->has('asset_type')) {
                $query->ofType($request->asset_type);
            }

            $rates = $query->orderBy('asset_type')->orderBy('asset_name')->get();

            // Group rates by asset type
            $groupedRates = $rates->groupBy('asset_type')->map(function ($items) {
                return $items->map(function ($rate) {
                    return [
                        'asset_code' => $rate->asset_code,
                        'asset_name' => $rate->asset_name,
                        'buy_rate' => $rate->buy_rate,
                        'sell_rate' => $rate->sell_rate,
                        'currency' => $rate->currency,
                        'min_amount' => $rate->min_amount,
                        'max_amount' => $rate->max_amount,
                        'description' => $rate->description,
                    ];
                });
            });

            // Log the request
            Log::channel('custom_daily')->info('Rates retrieved', [
                'action' => 'get_rates',
                'asset_type' => $request->asset_type ?? 'all',
                'ip_address' => $request->ip(),
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rates retrieved successfully.',
                'data' => [
                    'rates' => $groupedRates,
                    'total_count' => $rates->count(),
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::channel('custom_daily')->error('Failed to retrieve rates', [
                'action' => 'get_rates',
                'error' => $e->getMessage(),
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve rates.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Calculate conversion amount based on rate.
     *
     * @param CalculateRateRequest $request
     * @return JsonResponse
     */
    public function calculate(CalculateRateRequest $request): JsonResponse
    {
        try {
            $rate = Rate::getByCode($request->asset_code);

            if (!$rate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rate not found or inactive.',
                ], 404);
            }

            // Check min/max amounts
            if ($rate->min_amount && $request->amount < $rate->min_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Minimum amount is {$rate->min_amount}.",
                ], 422);
            }

            if ($rate->max_amount && $request->amount > $rate->max_amount) {
                return response()->json([
                    'success' => false,
                    'message' => "Maximum amount is {$rate->max_amount}.",
                ], 422);
            }

            // Calculate based on transaction type
            $appliedRate = $request->transaction_type === 'buy' ? $rate->buy_rate : $rate->sell_rate;
            $calculatedAmount = $request->amount * $appliedRate;

            // Log the calculation
            Log::channel('custom_daily')->info('Rate calculation performed', [
                'action' => 'calculate_rate',
                'asset_code' => $request->asset_code,
                'amount' => $request->amount,
                'transaction_type' => $request->transaction_type,
                'calculated_amount' => $calculatedAmount,
                'ip_address' => $request->ip(),
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Calculation successful.',
                'data' => [
                    'asset_code' => $rate->asset_code,
                    'asset_name' => $rate->asset_name,
                    'transaction_type' => $request->transaction_type,
                    'input_amount' => (float) $request->amount,
                    'applied_rate' => (float) $appliedRate,
                    'calculated_amount' => (float) number_format($calculatedAmount, 2, '.', ''),
                    'currency' => $rate->currency,
                ],
            ], 200);

        } catch (\Exception $e) {
            Log::channel('custom_daily')->error('Failed to calculate rate', [
                'action' => 'calculate_rate',
                'error' => $e->getMessage(),
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Calculation failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
