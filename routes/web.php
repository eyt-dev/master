<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\FrontController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

$domains = ['arden.local', 'eytmaster.local', 'admin.eyt.app', 'add2care.eyt.app', 'eyt.app'];

foreach ($domains as $domain) {
    Route::group(['domain' => $domain], function () {
        Route::get('/', [FrontController::class, 'index']);
    });
}
