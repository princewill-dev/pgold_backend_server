<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $expectedApiKey = env('BACKEND_SERVER_API_KEY');

        // Check if API key is configured
        if (empty($expectedApiKey)) {
            Log::error('BACKEND_SERVER_API_KEY is not configured in environment');
            return response()->json([
                'success' => false,
                'message' => 'API key validation is not properly configured.',
            ], 500);
        }

        // Check if API key is provided
        if (empty($apiKey)) {
            Log::warning('API request without API key', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'API key is required. Please provide X-API-Key header.',
            ], 401);
        }

        // Validate API key
        if ($apiKey !== $expectedApiKey) {
            Log::warning('Invalid API key attempt', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'provided_key' => substr($apiKey, 0, 8) . '...',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid API key.',
            ], 401);
        }

        // Log successful API key validation (optional, can be disabled in production)
        if (config('app.debug')) {
            Log::debug('Valid API key provided', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
        }

        return $next($request);
    }
}
