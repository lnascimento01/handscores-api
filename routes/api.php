<?php

use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\MfaController;

Route::get('/ping', fn() => response()->json(['pong' => true]));
Route::prefix('v1/auth')->group(function () {
    // ---------- PUBLIC ----------
    Route::middleware('throttle:auth')->group(function () {
        Route::post('/sign-up',   [AuthController::class, 'signUp']);   // registro
        Route::post('/sign-in',   [AuthController::class, 'signIn']);   // login
    });

    // ---------- PROTECTED ----------
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/sign-out',  [AuthController::class, 'signOut']);

        // Trusted device (opcional)
        Route::post('/trusted-device', [AuthController::class, 'trustedDevice']);

        // Refresh token (opcional, se for JWT rotativo)
        Route::post('/refresh', [AuthController::class, 'refresh']);

        // MFA
        Route::prefix('mfa')->group(function () {
            Route::post('/enroll/totp',        [MfaController::class, 'enrollTotp']);
            Route::post('/enroll/confirm',     [MfaController::class, 'confirmTotp']); // code TOTP
            Route::post('/challenge',          [MfaController::class, 'challenge']);   // totp|sms|email|whatsapp
            Route::middleware('throttle:mfa')->post('/verify', [MfaController::class, 'verify']); // code ou recovery
            Route::post('/recovery/regenerate', [MfaController::class, 'regenerateRecovery']);
        });
    });
});
