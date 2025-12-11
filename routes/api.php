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
            // Categories
            Route::get('/categories', [EmissionFactorCategoryController::class, 'index']);
            Route::post('/categories', [EmissionFactorCategoryController::class, 'store']);
            Route::get('/categories/{id}', [EmissionFactorCategoryController::class, 'show']);
            Route::put('/categories/{id}', [EmissionFactorCategoryController::class, 'update']);
            Route::delete('/categories/{id}', [EmissionFactorCategoryController::class, 'destroy']);

            // Factors
            Route::get('/factors', [EmissionFactorItemController::class, 'index']);
            Route::post('/factors', [EmissionFactorItemController::class, 'store']);
            Route::get('/factors/{id}', [EmissionFactorItemController::class, 'show']);
            Route::put('/factors/{id}', [EmissionFactorItemController::class, 'update']);
            Route::delete('/factors/{id}', [EmissionFactorItemController::class, 'destroy']);
        });

        /* --------------------------------------------------------------------------
     | CONSUMPTION ENTRIES ROUTES
     -------------------------------------------------------------------------- */
        Route::prefix('consumption')->group(function () {
            Route::get('/entries', [ConsumptionEntryController::class, 'index']);
            Route::post('/entries', [ConsumptionEntryController::class, 'store']);
            Route::get('/entries/{id}', [ConsumptionEntryController::class, 'show']);
            Route::delete('/entries/{id}', [ConsumptionEntryController::class, 'destroy']);
        });
    });
});

Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route API tidak ditemukan'
    ], 404);
});
