<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConsumptionHistoryController;
use App\Http\Controllers\FoodPackaging\FoodItemController;
use App\Http\Controllers\FoodPackaging\FoodConsumptionController;
use App\Http\Controllers\Transport\TransportModeController;
use App\Http\Controllers\Transport\PrivateVehicleController;
use App\Http\Controllers\Transport\PublicTransitController;
use App\Http\Controllers\Transport\TransportConsumptionController;

Route::prefix('v1')->group(function () {

    /* --------------------------------------------------------------------------
     | AUTH ROUTES — Public
     -------------------------------------------------------------------------- */
    Route::prefix('auth')->group(function () {
        Route::post('/login',    [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    /* --------------------------------------------------------------------------
     | AUTHENTICATED ROUTES
     -------------------------------------------------------------------------- */
    Route::middleware(['auth:sanctum', 'custom.token'])->group(function () {

        // Auth — Logout
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // User Profile
        Route::get('/user/profile',          [UserController::class, 'profile']);
        Route::put('/user/profile',          [UserController::class, 'updateProfile']);
        Route::post('/user/profile/image',   [UserController::class, 'uploadProfileImage']);
        Route::post('/user/change-password', [UserController::class, 'changePassword']);

        /* --------------------------------------------------------------------------
         | DOMAIN: Food & Packaging
         -------------------------------------------------------------------------- */
        Route::prefix('food-packaging')->group(function () {

            // Master data item — GET (dengan filter ?method=fixed|climatiq)
            Route::get('/items',        [FoodItemController::class, 'index']);
            Route::get('/items/{id}',   [FoodItemController::class, 'show']);
            Route::post('/items',       [FoodItemController::class, 'store']);
            Route::put('/items/{id}',   [FoodItemController::class, 'update']);
            Route::delete('/items/{id}',[FoodItemController::class, 'destroy']);

            // Konsumsi
            Route::post('/entries', [FoodConsumptionController::class, 'store']);
        });

        /* --------------------------------------------------------------------------
         | DOMAIN: Transport
         -------------------------------------------------------------------------- */
        Route::prefix('transport')->group(function () {

            // Mode (private / public)
            Route::get('/modes', [TransportModeController::class, 'index']);

            // Private Vehicle — Kendaraan
            Route::get('/private/vehicles',        [PrivateVehicleController::class, 'vehicles']);
            Route::post('/private/vehicles',       [PrivateVehicleController::class, 'storeVehicle']);
            Route::put('/private/vehicles/{id}',   [PrivateVehicleController::class, 'updateVehicle']);
            Route::delete('/private/vehicles/{id}',[PrivateVehicleController::class, 'destroyVehicle']);

            // Private Vehicle — Bahan Bakar
            Route::get('/private/fuels',        [PrivateVehicleController::class, 'fuels']);
            Route::post('/private/fuels',       [PrivateVehicleController::class, 'storeFuel']);
            Route::put('/private/fuels/{id}',   [PrivateVehicleController::class, 'updateFuel']);
            Route::delete('/private/fuels/{id}',[PrivateVehicleController::class, 'destroyFuel']);

            // Public Transit — Kendaraan Umum
            Route::get('/public/vehicles',        [PublicTransitController::class, 'index']);
            Route::get('/public/vehicles/{id}',   [PublicTransitController::class, 'show']);
            Route::post('/public/vehicles',       [PublicTransitController::class, 'store']);
            Route::put('/public/vehicles/{id}',   [PublicTransitController::class, 'update']);
            Route::delete('/public/vehicles/{id}',[PublicTransitController::class, 'destroy']);

            // Konsumsi (private & public melalui satu endpoint, dibedakan oleh field "mode")
            Route::post('/entries', [TransportConsumptionController::class, 'store']);
        });

        /* --------------------------------------------------------------------------
         | DOMAIN: Riwayat Konsumsi (Shared)
         -------------------------------------------------------------------------- */
        Route::prefix('entries')->group(function () {
            Route::get('/',       [ConsumptionHistoryController::class, 'index']);
            Route::get('/{id}',   [ConsumptionHistoryController::class, 'show']);
            Route::delete('/{id}',[ConsumptionHistoryController::class, 'destroy']);
        });
    });
});

/* --------------------------------------------------------------------------
 | Fallback
 -------------------------------------------------------------------------- */
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'Route API tidak ditemukan',
    ], 404);
});
