<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\FrontController;

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

$domains = array('arden1.local', 'admin.eyt.app', 'add2care.eyt.app');

foreach ($domains as $domain) {
    Route::group(['domain' => $domain], function () {
        Route::get('/', [\App\Http\Controllers\FrontController::class, 'index']);
    });
}
