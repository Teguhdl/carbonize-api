<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmissionFactorItemController;
use App\Http\Controllers\ConsumptionEntryController;
use App\Http\Controllers\EmissionFactorCategoryController;

Route::prefix('v1')->group(function () {

    /* --------------------------------------------------------------------------
     | AUTH ROUTES
     -------------------------------------------------------------------------- */
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    Route::middleware(['auth:sanctum', 'custom.token'])->group(function () {
        /* --------------------------------------------------------------------------
     | USER ROUTES
     -------------------------------------------------------------------------- */
        Route::get('/user/profile', [UserController::class, 'profile']);
        Route::post('/user/change-password', [UserController::class, 'changePassword']);

        /* --------------------------------------------------------------------------
     | EMISSION FACTORS ROUTES
     -------------------------------------------------------------------------- */
        Route::prefix('emission')->group(function () {
            Route::get('/categories', [EmissionFactorCategoryController::class, 'index']);
            Route::get('/factors', [EmissionFactorItemController::class, 'index']);
        });

        /* --------------------------------------------------------------------------
     | CONSUMPTION ENTRIES ROUTES
     -------------------------------------------------------------------------- */
        Route::prefix('consumption')->group(function () {
            Route::post('/entries', [ConsumptionEntryController::class, 'store']);
        });
    });
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route API tidak ditemukan'
    ], 404);
});
