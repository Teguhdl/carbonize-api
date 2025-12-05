<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmissionFactorItemController;
use App\Http\Controllers\ConsumptionEntryController;
use App\Http\Controllers\EmissionFactorCategoryController;
/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);      
    Route::post('/register', [AuthController::class, 'register']); 
});

/*
|--------------------------------------------------------------------------
| USER ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/user/profile', [UserController::class, 'profile']);
Route::post('/user/change-password', [UserController::class, 'changePassword']);

/*
|--------------------------------------------------------------------------
| EMISSION FACTORS ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/emission/categories', [EmissionFactorCategoryController::class, 'index']);
Route::get('/emission/factors', [EmissionFactorItemController::class, 'index']);


/*
|--------------------------------------------------------------------------
| CONSUMPTION ENTRIES ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/consumption/entries', [ConsumptionEntryController::class, 'store']);
