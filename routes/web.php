<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\FrontController;
use App\Models\Admin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Auth::routes();

$domains = [
    'arden.local', 
    'eytmaster.local', 
    'admin.arden.local', 
    'admin.eytmaster.local',

    'admin.eyt.app', 
    'add2care.eyt.app', 
    'eyt.app'
];

foreach ($domains as $domain) {
    Route::group(['domain' => $domain], function () {
        Route::get('/', [FrontController::class, 'index']);
    });
}


Route::group(['prefix' => '{username}'], function () {
    Auth::routes(); // Registers login, register, logout, etc.

    // Optional: redirect /username/ to dashboard or home
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
});



// $siteSlug = request()->segment(1);
// if($siteSlug != 'backend') {
//     // Check if this is a valid admin username
//     $siteIsValidAdmin = Admin::where('username', $siteSlug)->exists();

//     // If not a valid admin username, redirect to /backend/... but preserve the rest of the path
//     if (!$siteIsValidAdmin) {
//         if (request()->is('*login*')) {
//             return redirect("/login");
//         }
//         $pathAfterSite = implode('/', array_slice($request->segments(), 1)); // skip siteSlug
//         return redirect("/backend/" . $pathAfterSite);
//     }
// }
// Route::get($siteSlug.'/{any?}', function () use ($siteSlug) {
//     // Handle logic in middleware or controller (e.g., check if user exists)
//     return redirect("/login");
// })->where('any', '.*');