<?php

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Ici route sans admin
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::middleware(['auth:sanctum', 'can:access-dashboard'])->group(function () {
        // Ici les routes qui doivent avoir l'authorisation ADMIN
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
    });
});
