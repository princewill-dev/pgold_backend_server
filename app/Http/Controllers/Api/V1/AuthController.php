<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResendOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ValidateUsernameRequest;
use App\Jobs\SendOtpEmailJob;
use App\Jobs\SendEmailVerifiedJob;
use App\Jobs\SendLoginNoticeJob;
use App\Models\HearAboutUs;
use App\Models\Otp;
use App\Models\User;
use App\Models\ReferralRelationship;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Traits\AuthRequestTrait;

class AuthController extends Controller
{
    use AuthRequestTrait;
    /**
     * Sanitize user data for logging (truncate sensitive information).
     *
     * @param User $user
     * @return array
     */
    private function sanitizeUserForLogging(User $user): array
    {
        return [
            'user_uuid' => substr($user->user_uuid, 0, 8) . '***',
            'username' => $user->username,
            'email' => substr($user->email, 0, 3) . '***@' . explode('@', $user->email)[1],
            'phone_number' => substr($user->phone_number, 0, 3) . '****' . substr($user->phone_number, -2),
        ];
    }
    /**
     * Register a new user.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Create the user (email not verified yet)
            $user = User::create([
                'username' => $request->username,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'referral_code' => $request->referral_code,
                'password' => $request->password,
            ]);

            // Store "how did you hear about us" in separate table
            HearAboutUs::create([
                'user_id' => $user->id,
                'source' => $request->how_did_you_hear_about_us,
            ]);

            // Create referral relationship if referral code was provided
            if (!empty($request->referral_code)) {
                $referrer = User::where('referral_code', $request->referral_code)->first();
                
                if ($referrer) {
                    ReferralRelationship::create([
                        'referrer_id' => $referrer->id,
                        'referred_id' => $user->id,
                        'referral_code_used' => $request->referral_code,
                        'referred_at' => Carbon::now(),
                    ]);
                }
            }

            // Generate and save OTP
            $otpCode = Otp::generateOtp();
            $otp = Otp::create([
                'email' => $user->email,
                'otp' => $otpCode,
                'reason' => 'registration',
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Dispatch job to send OTP email
            SendOtpEmailJob::dispatch($user->email, $otpCode, 'registration');

            DB::commit();

            // Log successful registration
            Log::channel('custom_daily')->info('User registered successfully', [
                'action' => 'register',
                'user' => $this->sanitizeUserForLogging($user),
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please check your email for OTP verification.',
                'data' => [
                    'user' => [
                        'user_uuid' => $user->user_uuid,
                        'username' => $user->username,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                    ],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Verify OTP and mark email as verified.
     *
     * @param VerifyOtpRequest $request
     * @return JsonResponse
     */
    public function verify(VerifyOtpRequest $request): JsonResponse
    {
        try {
            // Find the latest valid OTP for this email and reason
            $otpRecord = Otp::where('email', $request->email)
                ->where('otp', $request->otp)
                ->where('reason', $request->reason)
                ->where('is_used', false)
                ->where('expires_at', '>', Carbon::now())
                ->latest()
                ->first();

            if (!$otpRecord) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP.',
                ], 400);
            }

            // Mark OTP as used
            $otpRecord->markAsUsed();

            // Find the user and verify their email
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Verify email
            $user->email_verified_at = Carbon::now();
            $user->save();

            // Send email verification success notification
            SendEmailVerifiedJob::dispatch($user);

            // Create Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Log successful verification
            Log::channel('custom_daily')->info('Email verified successfully', [
                'action' => 'verify_email',
                'user' => $this->sanitizeUserForLogging($user),
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email verified successfully.',
                'data' => [
                    'user' => [
                        'user_uuid' => $user->user_uuid,
                        'username' => $user->username,
                        'full_name' => $user->full_name,
                        'email' => $user->email,
                        'phone_number' => $user->phone_number,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Resend OTP to user's email.
     *
     * @param ResendOtpRequest $request
     * @return JsonResponse
     */
    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        try {
            // Check if user exists
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Check if email is already verified
            if ($user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email is already verified.',
                ], 400);
            }

            // Invalidate previous OTPs for this email and reason
            Otp::where('email', $request->email)
                ->where('reason', $request->reason)
                ->where('is_used', false)
                ->update(['is_used' => true]);

            // Generate new OTP
            $otpCode = Otp::generateOtp();
            Otp::create([
                'email' => $request->email,
                'otp' => $otpCode,
                'reason' => $request->reason,
                'expires_at' => Carbon::now()->addMinutes(10),
            ]);

            // Dispatch job to send OTP email
            SendOtpEmailJob::dispatch($request->email, $otpCode, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'OTP has been resent to your email.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Login user.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            // Find user by email
            $user = User::where('email', $request->email)->first();

            // Check if user exists and password is correct
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials.',
                ], 401);
            }

            // Check if email is verified
            if (!$user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your email before logging in.',
                ], 403);
            }

            // Create Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Get request details for login notice
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent() ?? 'Unknown';
            $loginTime = Carbon::now()->format('F d, Y \a\t h:i A');

            // Send login notice email
            SendLoginNoticeJob::dispatch($user, $ipAddress, $userAgent, $loginTime);

            // Log successful login
            Log::channel('custom_daily')->info('User logged in successfully', [
                'action' => 'login',
                'user' => $this->sanitizeUserForLogging($user),
                'ip_address' => $ipAddress,
                'timestamp' => Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message' => 'Logged in successfully',
                'data' => [
                    'access_token' => $token,
                    'email_verified_at' => $user->email_verified_at->format('Y-m-d H:i:s'),
                    'user_uuid' => $user->user_uuid,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Validate username availability.
     *
     * @param ValidateUsernameRequest $request
     * @return JsonResponse
     */
    public function validateUsername(ValidateUsernameRequest $request): JsonResponse
    {
        $username = $request->username;
        
        $exists = User::where('username', $username)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Username is already taken.',
                'available' => false,
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'Username is available.',
            'available' => true,
        ], 200);
    }
}
