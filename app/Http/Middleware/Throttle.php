<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Throttle
{
    protected RateLimiter $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type = 'api'): Response
    {
        $maxAttempts = $this->getMaxAttempts($type);
        $decayMinutes = $this->getDecayMinutes($type);

        $key = $this->resolveRequestSignature($request, $type);

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildTooManyAttemptsResponse($key, $maxAttempts);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts)
        );
    }

    /**
     * Get the maximum number of attempts allowed.
     */
    protected function getMaxAttempts(string $type): int
    {
        return match ($type) {
            'login' => (int) env('THROTTLE_LOGIN_MAX_ATTEMPTS', 5),
            'register' => (int) env('THROTTLE_REGISTER_MAX_ATTEMPTS', 3),
            'otp' => (int) env('THROTTLE_OTP_MAX_ATTEMPTS', 10),
            default => (int) env('THROTTLE_API_MAX_ATTEMPTS', 60),
        };
    }

    /**
     * Get the decay time in minutes.
     */
    protected function getDecayMinutes(string $type): int
    {
        return match ($type) {
            'login' => (int) env('THROTTLE_LOGIN_DECAY_MINUTES', 1),
            'register' => (int) env('THROTTLE_REGISTER_DECAY_MINUTES', 10),
            'otp' => (int) env('THROTTLE_OTP_DECAY_MINUTES', 10),
            default => (int) env('THROTTLE_API_DECAY_MINUTES', 1),
        };
    }

    /**
     * Resolve the request signature.
     */
    protected function resolveRequestSignature(Request $request, string $type): string
    {
        $signature = sha1(
            $type . '|' . $request->ip() . '|' . $request->userAgent()
        );

        return 'throttle:' . $signature;
    }

    /**
     * Calculate the number of remaining attempts.
     */
    protected function calculateRemainingAttempts(string $key, int $maxAttempts): int
    {
        return $this->limiter->retriesLeft($key, $maxAttempts);
    }

    /**
     * Create a 'too many attempts' response.
     */
    protected function buildTooManyAttemptsResponse(string $key, int $maxAttempts): Response
    {
        $retryAfter = $this->limiter->availableIn($key);

        return response()->json([
            'success' => false,
            'message' => 'Too many attempts. Please try again later.',
            'retry_after_seconds' => $retryAfter,
        ], 429)
        ->withHeaders([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
            'Retry-After' => $retryAfter,
            'X-RateLimit-Reset' => now()->addSeconds($retryAfter)->timestamp,
        ]);
    }

    /**
     * Add rate limit headers to response.
     */
    protected function addHeaders(Response $response, int $maxAttempts, int $remainingAttempts): Response
    {
        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => $remainingAttempts,
        ]);

        return $response;
    }
}
