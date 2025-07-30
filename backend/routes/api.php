<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/check', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is working',
            'timestamp' => now()
        ]);
    });
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    Route::middleware(['auth:api'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    Route::middleware(['auth:api', 'acl', 'user.active'])->group(function () {
        Route::apiResource('users', UserController::class);
    });
});
