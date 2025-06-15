<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\FrontController;
use App\Http\Controllers\Auth\LoginController;
use App\Models\Admin;
use App\Models\Setting;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/  
$adminDomains = Setting::pluck('admin_domain')->toArray();
$currentHost = request()->getHost();

if (request()->getHost() === config('domains.admin_subdomain')) {
    Route::domain(config('domains.admin_subdomain'))->group(function () {
        Auth::routes([
            'register' => false,
            'login' => false,
        ]);
        Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/', [App\Http\Controllers\Auth\LoginController::class, 'login']);

        if (request()->segment(1)) {
            Route::group(['prefix' => '{username}'], function () {
                Auth::routes([
                    'register' => false,
                    'login' => false,
                ]);
                Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
                Route::post('/', [App\Http\Controllers\Auth\LoginController::class, 'login']);

                Route::middleware(['auth', 'identify.tenant'])->group(function () {
                    Route::get('/dashboard', function () {
                        return view('index');
                    })->name('dashboard'); 
                    // Add tenant modules here
                });
            });
        }

    });
} elseif (in_array($currentHost, $adminDomains)) {
    
    Route::domain($currentHost)->group(function () {    
        Auth::routes([
            'register' => false,
            'login' => false,
        ]);
        Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/', [App\Http\Controllers\Auth\LoginController::class, 'login']);
        Route::group(['prefix' => '{username}'], function () {
            Route::middleware(['auth', 'identify.tenant'])->group(function () {
                Route::get('/dashboard', function () {
                    return view('index');
                })->name('dashboard'); 
                // other modules here
            });
        });
    });
}





/*
$domains = [
    'add2care.test', 
    'eyt.test', 
    'admin.add2care.test', 
    'admin.eyt.test',

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
        return redirect()->route('dashboard',  ['site' => request()->segment(1)]);
    });
});

*/

// $siteSlug = request()->segment(1);
// if($siteSlug != 'e') {
//     // Check if this is a valid admin username
//     $siteIsValidAdmin = Admin::where('username', $siteSlug)->exists();

//     // If not a valid admin username, redirect to /e/... but preserve the rest of the path
//     if (!$siteIsValidAdmin) {
//         if (request()->is('*login*')) {
//             return redirect("/login");
//         }
//         $pathAfterSite = implode('/', array_slice($request->segments(), 1)); // skip siteSlug
//         return redirect("/e/" . $pathAfterSite);
//     }
// }
// Route::get($siteSlug.'/{any?}', function () use ($siteSlug) {
//     // Handle logic in middleware or controller (e.g., check if user exists)
//     return redirect("/login");
// })->where('any', '.*');