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
    Route::controller(GameController::class)->prefix('game')->group(function () {
        Route::get('/', 'index')->name('game.index')->middleware('permission:view.game');
        Route::get('create', 'create')->name('game.create')->middleware('permission:create.game');
        Route::post('store', 'store')->name('game.store')->middleware('permission:create.game');
        Route::get('{game}/edit', 'edit')->name('game.edit')->middleware('permission:edit.game');
        Route::post('{game}', 'update')->name('game.update')->middleware('permission:edit.game');
        Route::delete('{game}', 'destroy')->name('game.destroy')->middleware('permission:delete.game');
        Route::get('permission', 'assignPermissionList')->name('game.permission.index');
    });
    Route::controller(WheelController::class)->prefix('wheel')->group(function () {
        Route::get('/', 'index')->name('wheel.index')->middleware('permission:view.wheel');
        Route::get('create', 'create')->name('wheel.create')->middleware('permission:create.wheel');
        Route::post('store', 'store')->name('wheel.store')->middleware('permission:create.wheel');
        Route::get('{wheel}/edit', 'edit')->name('wheel.edit')->middleware('permission:edit.wheel');
        Route::post('{wheel}', 'update')->name('wheel.update')->middleware('permission:edit.wheel');
        Route::delete('{wheel}', 'destroy')->name('wheel.destroy')->middleware('permission:delete.wheel');
        Route::get('permission', 'assignPermissionList')->name('wheel.permission.index');
        Route::get('/getClipsByGame', 'getClipsByGame')->name('getClipsByGame');
    });
    Route::controller(StoreViewController::class)->prefix('store_view')->group(function () {
        Route::get('/', 'index')->name('store_view.index')->middleware('permission:view.store_view');
        Route::get('create', 'create')->name('store_view.create')->middleware('permission:create.store_view');
        Route::post('store', 'store')->name('store_view.store')->middleware('permission:create.store_view');
        Route::get('{store_view}/edit', 'edit')->name('store_view.edit')->middleware('permission:edit.store_view');
        Route::post('{store_view}', 'update')->name('store_view.update')->middleware('permission:edit.store_view');
        Route::get('destroy/{admin}', 'destroy')->name('store_view.destroy')->middleware('permission:delete.store_view');
    });
    Route::controller(CategoryController::class)->prefix('category')->group(function () {
        Route::get('/', 'index')->name('category.index')->middleware('permission:view.category');
        Route::get('create', 'create')->name('category.create')->middleware('permission:create.category');
        Route::post('store', 'store')->name('category.store')->middleware('permission:create.category');
        Route::get('{category}/edit', 'edit')->name('category.edit')->middleware('permission:edit.category');
        Route::post('{category}', 'update')->name('category.update')->middleware('permission:edit.category');
        Route::get('destroy/{admin}', 'destroy')->name('category.destroy')->middleware('permission:delete.category');
    });
    Route::controller(PageController::class)->prefix('page')->group(function () {
        Route::get('/', 'index')->name('page.index')->middleware('permission:view.page');
        Route::get('create', 'create')->name('page.create')->middleware('permission:create.page');
        Route::post('store', 'store')->name('page.store')->middleware('permission:create.page');
        Route::get('{page}/edit', 'edit')->name('page.edit')->middleware('permission:edit.page');
        Route::post('{page}', 'update')->name('page.update')->middleware('permission:edit.page');
        Route::get('destroy/{admin}', 'destroy')->name('page.destroy')->middleware('permission:delete.page');
    });
    Route::controller(SettingController::class)->prefix('setting')->group(function () {
        Route::get('/', 'index')->name('setting.index')->middleware('permission:view.setting');
        Route::get('create', 'create')->name('setting.create')->middleware('permission:create.setting');
        Route::post('store', 'store')->name('setting.store')->middleware('permission:create.setting');
        Route::get('{setting}/edit', 'edit')->name('setting.edit')->middleware('permission:edit.setting');
        Route::post('{setting}', 'update')->name('setting.update')->middleware('permission:edit.setting');
        Route::get('destroy/{admin}', 'destroy')->name('setting.destroy')->middleware('permission:delete.setting');
        Route::get('/check-setting/{created_by}', 'checkSetting')->name('setting.checkSetting');
    });
    Route::controller(SlideController::class)->prefix('slide')->group(function () {
        Route::get('/', 'index')->name('slide.index')->middleware('permission:view.slide');
        Route::get('create', 'create')->name('slide.create')->middleware('permission:create.slide');
        Route::post('store', 'store')->name('slide.store')->middleware('permission:create.slide');
        Route::get('{slide}/edit', 'edit')->name('slide.edit')->middleware('permission:edit.slide');
        Route::post('{slide}', 'update')->name('slide.update')->middleware('permission:edit.slide');
        Route::get('destroy/{admin}', 'destroy')->name('slide.destroy')->middleware('permission:delete.slide');
    });
    Route::controller(TestimonialController::class)->prefix('testimonial')->group(function () {
        Route::get('/', 'index')->name('testimonial.index')->middleware('permission:view.testimonial');
        Route::get('create', 'create')->name('testimonial.create')->middleware('permission:create.testimonial');
        Route::post('store', 'store')->name('testimonial.store')->middleware('permission:create.testimonial');
        Route::get('{testimonial}/edit', 'edit')->name('testimonial.edit')->middleware('permission:edit.testimonial');
        Route::post('{testimonial}', 'update')->name('testimonial.update')->middleware('permission:edit.testimonial');
        Route::get('destroy/{admin}', 'destroy')->name('testimonial.destroy')->middleware('permission:delete.testimonial');
    });
});