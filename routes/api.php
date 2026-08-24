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
use App\Http\Controllers\Api\Add2Farm\AuthController as Add2FarmAuthController;
use App\Http\Controllers\Api\Add2Farm\ProfileController as Add2FarmProfileController;
use App\Http\Controllers\Api\Add2Farm\SupervisorController as Add2FarmSupervisorController;
use App\Http\Controllers\Api\Add2Farm\FarmerController as Add2FarmFarmerController;
use App\Http\Controllers\Api\Add2Farm\FarmController as Add2FarmFarmController;
use App\Http\Controllers\Api\Add2Farm\FlockController as Add2FarmFlockController;
use App\Http\Controllers\Api\Add2Farm\DailyRecordController as Add2FarmDailyRecordController;
use App\Http\Controllers\Api\Add2Farm\DropdownController as Add2FarmDropdownController;

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

/*
|--------------------------------------------------------------------------
| Add2Farm API Routes
|--------------------------------------------------------------------------
| Separate routes for add2farm mobile/web application
| Public routes for authentication, protected routes for user operations
*/

// Add2Farm public authentication routes
Route::prefix('add2farm/auth')->middleware('set.add2farm.language')->group(function () {
    Route::post('register', [Add2FarmAuthController::class, 'register']);
    Route::post('login', [Add2FarmAuthController::class, 'login']);
    Route::post('verify-otp', [Add2FarmAuthController::class, 'verifyOtp']);
    Route::post('resend-otp', [Add2FarmAuthController::class, 'resendOtp']);
    Route::post('forgot-password', [Add2FarmAuthController::class, 'forgotPassword']);
    Route::post('reset-password', [Add2FarmAuthController::class, 'resetPassword'])->middleware('validate.password.reset.token');
});

// Add2Farm protected routes — require a valid Sanctum token
Route::prefix('add2farm')->middleware(['reject.password.reset.token', 'set.add2farm.language'])->group(function () {
    // Public endpoints (no auth required for Scribe)
    Route::post('auth/logout', [Add2FarmAuthController::class, 'logout'])->middleware('auth:sanctum');

    // User profile endpoints (protected)
    Route::prefix('profile')->middleware('auth:sanctum')->group(function () {
        Route::get('/', [Add2FarmProfileController::class, 'show']);
        Route::put('/', [Add2FarmProfileController::class, 'update']);
        Route::put('change-password', [Add2FarmProfileController::class, 'changePassword']);
        Route::get('settings', [Add2FarmProfileController::class, 'getSettings']);
        Route::put('settings', [Add2FarmProfileController::class, 'updateSettings']);
    });

    // Supervisors (Type 3) - Only Type 1 (Farm Admin) can access
    Route::middleware(['auth:sanctum', 'check.admin.type:1'])->group(function () {
        Route::get('supervisors', [Add2FarmSupervisorController::class, 'index']);
        Route::post('supervisors', [Add2FarmSupervisorController::class, 'store']);
        Route::get('supervisors/{supervisor}', [Add2FarmSupervisorController::class, 'show']);
        Route::put('supervisors/{supervisor}', [Add2FarmSupervisorController::class, 'update']);
        Route::delete('supervisors/{supervisor}', [Add2FarmSupervisorController::class, 'destroy']);
    });

    // Farmers (Type 4) - Only Type 2 (Farm Owner) can access
    Route::middleware(['auth:sanctum', 'check.admin.type:2'])->group(function () {
        Route::get('farmers', [Add2FarmFarmerController::class, 'index']);
        Route::post('farmers', [Add2FarmFarmerController::class, 'store']);
        Route::get('farmers/{farmer}', [Add2FarmFarmerController::class, 'show']);
        Route::put('farmers/{farmer}', [Add2FarmFarmerController::class, 'update']);
        Route::delete('farmers/{farmer}', [Add2FarmFarmerController::class, 'destroy']);
    });

    // Farms - Type 2 (Farm Owner) and Type 3 (Supervisor) can access
    Route::middleware(['auth:sanctum', 'check.admin.type:1,2,3'])->group(function () {
        Route::get('farms', [Add2FarmFarmController::class, 'index']);
        Route::post('farms', [Add2FarmFarmController::class, 'store']);
        Route::get('farms/{farm}', [Add2FarmFarmController::class, 'show']);
        Route::put('farms/{farm}', [Add2FarmFarmController::class, 'update']);
        Route::delete('farms/{farm}', [Add2FarmFarmController::class, 'destroy']);
    });

    // Flocks - Accessible to authenticated users
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('flocks/available', [Add2FarmFlockController::class, 'available']);
        Route::get('flocks', [Add2FarmFlockController::class, 'index']);
        Route::post('flocks', [Add2FarmFlockController::class, 'store']);
        Route::get('flocks/{flock}', [Add2FarmFlockController::class, 'show']);
        Route::put('flocks/{flock}', [Add2FarmFlockController::class, 'update']);
        Route::delete('flocks/{flock}', [Add2FarmFlockController::class, 'destroy']);
    });

    // Daily Records - Accessible to authenticated users
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('daily-records', [Add2FarmDailyRecordController::class, 'index']);
        Route::post('daily-records', [Add2FarmDailyRecordController::class, 'store']);
        Route::get('daily-records/{daily_record}', [Add2FarmDailyRecordController::class, 'show']);
        Route::put('daily-records/{daily_record}', [Add2FarmDailyRecordController::class, 'update']);
        Route::delete('daily-records/{daily_record}', [Add2FarmDailyRecordController::class, 'destroy']);
    });

    // Dropdowns - Accessible to authenticated users
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('dropdowns/farms', [Add2FarmDropdownController::class, 'farms']);
        Route::get('dropdowns/suppliers', [Add2FarmDropdownController::class, 'suppliers']);
        Route::get('dropdowns/supervisors', [Add2FarmDropdownController::class, 'supervisors']);
    });

    // File serving route - Public access to uploaded images
    Route::get('files/{path}', function ($path) {
        $filePath = storage_path('app/public/' . urldecode($path));

        // Security check - ensure the file is within the allowed directory
        $realPath = realpath($filePath);
        $allowedPath = realpath(storage_path('app/public'));

        if (!$realPath || strpos($realPath, $allowedPath) !== 0) {
            abort(404, 'File not found');
        }

        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        return response()->file($filePath);
    })->where('path', '.*')->name('api.files');
});
