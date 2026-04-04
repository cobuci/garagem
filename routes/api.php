<?php

use App\Http\Controllers\Api\V1\Auth\OtpController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\SalesPushController;
use App\Http\Controllers\Api\V1\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::prefix('auth')->middleware('throttle:otp')->group(function (): void {
        Route::post('otp', [OtpController::class, 'send'])->name('api.v1.auth.otp.send');
        Route::post('otp/verify', [OtpController::class, 'verify'])->name('api.v1.auth.otp.verify');
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('sync', SyncController::class)->name('api.v1.sync');
        Route::get('dashboard', DashboardController::class)->name('api.v1.dashboard');
        Route::post('sales/push', SalesPushController::class)->name('api.v1.sales.push');
    });
});
