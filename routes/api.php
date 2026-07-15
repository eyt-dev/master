<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\ComponentController;
use App\Http\Controllers\Api\FormulationController;
use App\Http\Controllers\Api\ElementController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\CompoPriceController;

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

    // Elements
    Route::get('elements', [ElementController::class, 'index']);
    Route::get('elements/{element}', [ElementController::class, 'show']);

    // Forms
    Route::get('forms', [FormController::class, 'index']);
    Route::get('forms/{form}', [FormController::class, 'show']);

    // Compo Prices
    Route::get('compo-prices', [CompoPriceController::class, 'index']);
    Route::get('compo-prices/{compoPrice}', [CompoPriceController::class, 'show']);
    Route::get('compo-prices/component/get-by-component', [CompoPriceController::class, 'getByComponent']);
    Route::get('compo-prices/component/latest', [CompoPriceController::class, 'getLatestByComponent']);

    // Components
    Route::get('components/form/{form}/units', [ComponentController::class, 'getUnitsByForm']);
    Route::apiResource('components', ComponentController::class);

    // Formulations
    Route::get('component-types', [FormulationController::class, 'getComponentTypes']);
    Route::get('formulations/template/{formulation}', [FormulationController::class, 'getTemplate']);
    Route::get('formulations/{formulation}/edit', [FormulationController::class, 'edit']);
    Route::apiResource('formulations', FormulationController::class);
});
