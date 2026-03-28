<?php

use App\Http\Controllers\Api\V1\Auth\OtpController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->group(function (): void {
        Route::post('otp', [OtpController::class, 'send'])->name('api.v1.auth.otp.send');
        Route::post('otp/verify', [OtpController::class, 'verify'])->name('api.v1.auth.otp.verify');
    });
});
