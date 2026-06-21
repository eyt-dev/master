<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\FormulationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public auth routes
Route::prefix('auth')->group(function () {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('login',  [AuthController::class, 'login']);
});

// Protected routes — require a valid Sanctum token
Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::prefix('profile')->group(function () {
        Route::get('/',               [ProfileController::class, 'show']);
        Route::put('/',               [ProfileController::class, 'update']);
        Route::put('change-password', [ProfileController::class, 'changePassword']);
    });

    // Units
    Route::apiResource('units', UnitController::class);

    // Components
    Route::get('components/form/{form}/units', [ComponentController::class, 'getUnitsByForm']);
    Route::apiResource('components', ComponentController::class);

    // Formulations
    Route::get('component-types', [FormulationController::class, 'getComponentTypes']);
    Route::get('formulations/template/{formulation}', [FormulationController::class, 'getTemplate']);
    Route::get('formulations/{formulation}/edit', [FormulationController::class, 'edit']);
    Route::apiResource('formulations', FormulationController::class);
});
