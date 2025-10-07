<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\RateController;

Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::prefix('accounts')->group(function () {
        Route::post('register', [AuthController::class, 'register'])
            ->middleware('throttle.env:register');
        
        Route::post('verify', [AuthController::class, 'verify'])
            ->middleware('throttle.env:otp');
        
        Route::post('resend-otp', [AuthController::class, 'resendOtp'])
            ->middleware('throttle.env:otp');
        
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle.env:login');
        
        Route::post('validate-username', [AuthController::class, 'validateUsername'])
            ->middleware('throttle.env:api');
    });

    Route::prefix('rates')->group(function () {
        Route::get('/', [RateController::class, 'getRates'])
            ->middleware('throttle.env:api');
        
        Route::post('calculate', [RateController::class, 'calculate'])
            ->middleware('throttle.env:api');
    });
});
