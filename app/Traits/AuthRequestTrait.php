<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * AuthRequestTrait
 * Provides common authentication-related functionality
 */
trait AuthRequestTrait
{
    /**
     * Get the client IP address.
     *
     * @return string
     */
    protected function getClientIp(): string
    {
        return request()->ip();
    }

    /**
     * Get the user agent.
     *
     * @return string
     */
    protected function getUserAgent(): string
    {
        return request()->userAgent() ?? 'Unknown';
    }

    /**
     * Get request timestamp.
     *
     * @return string
     */
    protected function getRequestTimestamp(): string
    {
        return Carbon::now()->toDateTimeString();
    }

    /**
     * Log authentication attempt.
     *
     * @param string $action
     * @param array $data
     * @return void
     */
    protected function logAuthAttempt(string $action, array $data = []): void
    {
        Log::channel('custom_daily')->info("Auth attempt: {$action}", array_merge([
            'action' => $action,
            'ip_address' => $this->getClientIp(),
            'user_agent' => $this->getUserAgent(),
            'timestamp' => $this->getRequestTimestamp(),
        ], $data));
    }

    /**
     * Log failed authentication attempt.
     *
     * @param string $action
     * @param string $reason
     * @param array $data
     * @return void
     */
    protected function logFailedAuthAttempt(string $action, string $reason, array $data = []): void
    {
        Log::channel('custom_daily')->warning("Failed auth attempt: {$action}", array_merge([
            'action' => $action,
            'reason' => $reason,
            'ip_address' => $this->getClientIp(),
            'user_agent' => $this->getUserAgent(),
            'timestamp' => $this->getRequestTimestamp(),
        ], $data));
    }

    /**
     * Get sanitized request data for logging.
     *
     * @param array $excludeFields
     * @return array
     */
    protected function getSanitizedRequestData(array $excludeFields = ['password', 'password_confirmation', 'otp']): array
    {
        $data = request()->all();
        
        foreach ($excludeFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }

        // Truncate email if present
        if (isset($data['email'])) {
            $parts = explode('@', $data['email']);
            if (count($parts) === 2) {
                $data['email'] = substr($parts[0], 0, 3) . '***@' . $parts[1];
            }
        }

        // Truncate phone number if present
        if (isset($data['phone_number'])) {
            $data['phone_number'] = substr($data['phone_number'], 0, 3) . '****' . substr($data['phone_number'], -2);
        }

        return $data;
    }

    /**
     * Validate rate limit header.
     *
     * @return bool
     */
    protected function checkRateLimitHeaders(): bool
    {
        $remaining = request()->header('X-RateLimit-Remaining');
        
        if ($remaining !== null && (int) $remaining <= 0) {
            return false;
        }

        return true;
    }
}
