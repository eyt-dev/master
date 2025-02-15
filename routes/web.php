<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ModuleController;

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
    });

    // Route::get('/{page}', 'AdminController@index');

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('{id?}', [ProfileController::class, 'index'])->name('profile.index');
        Route::post('update/{id?}', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('change-password/{id?}', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    });

    Route::controller(UserController::class)->prefix('users')->group(function () {
        // Route::get('/', 'index')->name('users.index');
        Route::get('/{type?}', [UserController::class, 'index'])->name('users.index');

        Route::get('create/{type?}', 'create')->name('users.create');

        Route::post('store', 'store')->name('users.store');
        Route::get('edit/{user}', 'edit')->name('users.edit');
        Route::post('{user}', 'update')->name('users.update');
        Route::get('destroy/{user}', 'destroy')->name('users.destroy');
    });

    Route::controller(RoleController::class)->prefix('role')->group(function () {
        Route::get('/', 'index')->name('role.index');//->middleware('permission:view.role');
        Route::get('create', 'create')->name('role.create');//->middleware('permission:create.role');
        Route::post('store', 'store')->name('role.store');//->middleware('permission:create.role');
        Route::get('{role}/edit', 'edit')->name('role.edit');//->middleware('permission:edit.role');
        Route::post('{role}', 'update')->name('role.update');//->middleware('permission:edit.role');
        Route::delete('{role}', 'destroy')->name('role.destroy');//->middleware('permission:delete.role');
        Route::get('permission', 'assignPermissionList')->name('role.permission.index');
    });
    Route::controller(PermissionController::class)->prefix('permission')->group(function () {
        Route::get('/', 'index')->name('permission.index');//->middleware('permission:view.permission');
        Route::get('create', 'create')->name('permission.create');//->middleware('permission:create.permission');
        Route::post('store', 'store')->name('permission.store');//->middleware('permission:create.permission');
        Route::get('{permission}/edit', 'edit')->name('permission.edit');//->middleware('permission:edit.permission');
        Route::post('update', 'update')->name('permission.update');//->middleware('permission:edit.permission');
        Route::delete('{permission}', 'destroy')->name('permission.destroy');//->middleware('permission:delete.permission');
        Route::post('permission/delete', 'deleteSinglePermission')->name('permission.delete');//->middleware('permission:delete.permission');
        Route::post('module/store', 'moduleStore')->name('permission.module');
    });

    Route::controller(ModuleController::class)->prefix('module')->group(function () {
        Route::get('create', 'create')->name('module.create');
        Route::post('store', 'store')->name('module.store');
    });

    
});