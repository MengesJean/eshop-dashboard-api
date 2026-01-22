<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Ici route sans admin
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        // Ici les routes qui doivent avoir l'authorisation ADMIN
        Route::middleware('can:access-dashboard')->group(function () {
            Route::apiResource('products', ProductController::class);
        });

        // Ici les routes qui doivent avoir l'authorisation RESTRICTED
        Route::middleware('can:access-restricted-dashboard')->group(function () {
            Route::get('products', [ProductController::class, 'index']);
            Route::get('products/{product}', [ProductController::class, 'show']);
        });
    });
});
