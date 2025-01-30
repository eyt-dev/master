<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\UserController;

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
        Route::get('/', 'index')->name('users.index');
        Route::get('create', 'create')->name('users.create');
        Route::post('store', 'store')->name('users.store');
        Route::get('edit/{user}', 'edit')->name('users.edit');
        Route::post('{user}', 'update')->name('users.update');
        Route::get('destroy/{user}', 'destroy')->name('users.destroy');
    });

    
});