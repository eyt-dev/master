<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\WheelController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('index');
    });
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');    
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('{id?}', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('update/{id?}', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('change-password/{id?}', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    });
    Route::controller(AdminController::class)->prefix('admins')->group(function () {
        Route::get('/1', 'index')->name('admins.index')->middleware('permission:view.admin');
        Route::get('/2', 'index')->name('admins.index')->middleware('permission:view.public_vendor');;
        Route::get('/3', 'index')->name('admins.index')->middleware('permission:view.private_vendor');;
        Route::get('create/{type?}', 'create')->name('admins.create');
        Route::post('store', 'store')->name('admins.store');
        Route::get('edit/{admin}', 'edit')->name('admins.edit');
        Route::post('{admin}', 'update')->name('admins.update');
        Route::get('destroy/{admin}', 'destroy')->name('admins.destroy');
    });
    Route::controller(RoleController::class)->prefix('role')->group(function () {
        Route::get('/', 'index')->name('role.index')->middleware('permission:view.role');
        Route::get('create', 'create')->name('role.create')->middleware('permission:create.role');
        Route::post('store', 'store')->name('role.store')->middleware('permission:create.role');
        Route::get('{role}/edit', 'edit')->name('role.edit')->middleware('permission:edit.role');
        Route::post('{role}', 'update')->name('role.update')->middleware('permission:edit.role');
        Route::delete('{role}', 'destroy')->name('role.destroy')->middleware('permission:delete.role');
        Route::get('permission', 'assignPermissionList')->name('role.permission.index');
    });
    Route::controller(PermissionController::class)->prefix('permission')->group(function () {
        Route::get('/', 'index')->name('permission.index')->middleware('permission:view.permission');
        Route::get('create', 'create')->name('permission.create')->middleware('permission:create.permission');
        Route::post('store', 'store')->name('permission.store')->middleware('permission:create.permission');
        Route::get('{permission}/edit', 'edit')->name('permission.edit')->middleware('permission:edit.permission');
        Route::post('update', 'update')->name('permission.update')->middleware('permission:edit.permission');
        Route::delete('{permission}', 'destroy')->name('permission.destroy')->middleware('permission:delete.permission');
        Route::post('permission/delete', 'deleteSinglePermission')->name('permission.delete')->middleware('permission:delete.permission');
        Route::post('module/store', 'moduleStore')->name('permission.module');
    });
    Route::controller(ModuleController::class)->prefix('module')->group(function () {
        Route::get('create', 'create')->name('module.create');
        Route::post('store', 'store')->name('module.store');
    });
    Route::controller(GameController::class)->prefix('games')->group(function () {
        Route::get('/', 'index')->name('games.index')->middleware('permission:view.games');
        Route::get('create', 'create')->name('games.create')->middleware('permission:create.games');
        Route::post('store', 'store')->name('games.store')->middleware('permission:create.games');
        Route::get('{games}/edit', 'edit')->name('games.edit')->middleware('permission:edit.games');
        Route::post('{games}', 'update')->name('games.update')->middleware('permission:edit.games');
        Route::delete('{games}', 'destroy')->name('games.destroy')->middleware('permission:delete.games');
        Route::get('permission', 'assignPermissionList')->name('games.permission.index');
    });
    Route::controller(WheelController::class)->prefix('wheels')->group(function () {
        Route::get('/', 'index')->name('wheels.index')->middleware('permission:view.wheels');
        Route::get('create', 'create')->name('wheels.create')->middleware('permission:create.wheels');
        Route::post('store', 'store')->name('wheels.store')->middleware('permission:create.wheels');
        Route::get('{wheels}/edit', 'edit')->name('wheels.edit')->middleware('permission:edit.wheels');
        Route::post('{wheels}', 'update')->name('wheels.update')->middleware('permission:edit.wheels');
        Route::delete('{wheels}', 'destroy')->name('wheels.destroy')->middleware('permission:delete.wheels');
        Route::get('permission', 'assignPermissionList')->name('wheels.permission.index');
    });

    
});