<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\FrontController;
use App\Http\Controllers\Auth\LoginController;
use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ModuleController;
use App\Http\Controllers\Backend\GameController;
use App\Http\Controllers\Backend\WheelController;
use App\Http\Controllers\Backend\StoreViewController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\SlideController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\MyContactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/  
$domains = Schema::hasTable('settings') ? Setting::pluck('domain')->toArray() : [];
$adminDomains = Schema::hasTable('settings') ? Setting::pluck('admin_domain')->toArray() : [];
$currentHost = request()->getHost();
$allowRoute = 0;

if ($currentHost === config('domains.main_domain')) {
    Route::domain(config('domains.main_domain'))->group(function () {
        Route::get('/', [FrontController::class, 'index']);
        if (request()->segment(1)) {
            Route::group(['prefix' => '{username}'], function () {
                Route::middleware(['identify.tenant.front'])->group(function () {
                    Route::get('/', [FrontController::class, 'index']);
                });
            });
        } 
    });
} elseif (in_array($currentHost, $domains)) {
    Route::domain($currentHost)->group(function () {
        Route::middleware(['identify.tenant.front'])->group(function () {
            Route::get('/', [FrontController::class, 'index']);
        });
    });
}
// Inside the Auth::routes() section, add:

if ($currentHost === config('domains.admin_subdomain')) {
    Route::domain(config('domains.admin_subdomain'))->group(function () {
        Auth::routes([
            'register' => false,
            'login' => false,
        ]);
        Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
        Route::post('/', [App\Http\Controllers\Auth\LoginController::class, 'login']);

        Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

        if (request()->segment(1)) {
            Route::group(['prefix' => '{username}'], function () {
                Auth::routes([
                    'register' => false,
                    'login' => false,
                ]);
                Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
                Route::post('/', [App\Http\Controllers\Auth\LoginController::class, 'login']);

                Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
                Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

                Route::middleware(['auth', 'identify.tenant'])->group(function () {
                    Route::get('/dashboard', function () {
                        return view('index');
                    })->name('dashboard');
                    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
                        Route::get('/', 'index')->name('profile.index');
                        Route::get('{id?}', 'index')->name('profile.index');
                        Route::post('update/{id?}', 'update')->name('profile.update');
                        Route::post('change-password/{id?}', 'changePassword')->name('profile.change-password');
                    });
                    Route::controller(AdminController::class)->prefix('admins')->group(function () {
                        Route::get('/admin', 'index')->name('admins.index')->middleware('permission:view.admin');
                        Route::get('/publicvendor', 'publicVendor')->name('admins.publicVendor')->middleware('permission:view.public_vendor');
                        Route::get('/privatevendor', 'privateVendor')->name('admins.privateVendor')->middleware('permission:view.private_vendor');
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
                        Route::get('destroy/{store_view}', 'destroy')->name('store_view.destroy')->middleware('permission:delete.store_view');
                    });
                    Route::controller(CategoryController::class)->prefix('category')->group(function () {
                        Route::get('/', 'index')->name('category.index')->middleware('permission:view.category');
                        Route::get('create', 'create')->name('category.create')->middleware('permission:create.category');
                        Route::post('store', 'store')->name('category.store')->middleware('permission:create.category');
                        Route::get('{category}/edit', 'edit')->name('category.edit')->middleware('permission:edit.category');
                        Route::post('{category}', 'update')->name('category.update')->middleware('permission:edit.category');
                        Route::get('destroy/{category}', 'destroy')->name('category.destroy')->middleware('permission:delete.category');
                    });
                    Route::controller(PageController::class)->prefix('page')->group(function () {
                        Route::get('/', 'index')->name('page.index')->middleware('permission:view.page');
                        Route::get('create', 'create')->name('page.create')->middleware('permission:create.page');
                        Route::post('store', 'store')->name('page.store')->middleware('permission:create.page');
                        Route::get('{page}/edit', 'edit')->name('page.edit')->middleware('permission:edit.page');
                        Route::post('{page}', 'update')->name('page.update')->middleware('permission:edit.page');
                        Route::get('destroy/{page}', 'destroy')->name('page.destroy')->middleware('permission:delete.page');
                    });
                    Route::controller(SettingController::class)->prefix('setting')->group(function () {
                        Route::get('/', 'index')->name('setting.index')->middleware('permission:view.setting');
                        Route::get('create/{admin?}', 'create')->name('setting.create')->middleware('permission:create.setting');
                        Route::post('store', 'store')->name('setting.store')->middleware('permission:create.setting');
                        Route::get('{setting}/edit', 'edit')->name('setting.edit')->middleware('permission:edit.setting');
                        Route::post('{setting}', 'update')->name('setting.update')->middleware('permission:edit.setting');
                        Route::get('destroy/{admin}', 'destroy')->name('setting.destroy')->middleware('permission:delete.setting');
                        Route::get('/check-setting/{created_by}', 'checkSetting')->name('setting.checkSetting');
                    }); 
                    
                    Route::prefix('global_contacts')
                        ->name('global_contacts.')
                        ->controller(ContactController::class)
                        ->group(function () {
                            Route::get('/', 'index')
                                ->name('index')
                                ->middleware('permission:view.global_contacts');

                            Route::get('/create', 'create')
                                ->name('create')
                                ->middleware('permission:create.global_contacts');

                            Route::post('/', 'store')
                                ->name('store')
                                ->middleware('permission:create.global_contacts');

                            Route::get('/search', 'search')
                                ->name('search');
                                // ->middleware('permission:view.global_contacts');

                            Route::get('/{contact}/edit', 'edit')
                                ->name('edit')
                                ->middleware('permission:edit.global_contacts');

                            Route::put('/{contact}', 'update')
                                ->name('update')
                                ->middleware('permission:edit.global_contacts');

                            Route::delete('/{contact}', 'destroy')
                                ->name('destroy')
                                ->middleware('permission:delete.global_contacts');
                        });

                    Route::prefix('contacts')
                        ->name('contacts.')
                        ->controller(MyContactController::class)
                        ->group(function () {
                            Route::get('/', 'index')
                                ->name('index')
                                ->middleware('permission:view.contacts');

                            Route::get('/create', 'create')
                                ->name('create')
                                ->middleware('permission:create.contacts');

                            Route::post('/', 'store')
                                ->name('store')
                                ->middleware('permission:create.contacts');

                            Route::get('/{mycontact}/edit', 'edit')
                                ->name('edit')
                                ->middleware('permission:edit.contacts');

                            Route::put('/{mycontact}', 'update')
                                ->name('update')
                                ->middleware('permission:edit.contacts');

                            Route::delete('/{mycontact}', 'destroy')
                                ->name('destroy')
                                ->middleware('permission:delete.contacts');
                        });

                    Route::controller(SlideController::class)->prefix('slide')->group(function () {
                        Route::get('/', 'index')->name('slide.index')->middleware('permission:view.slide');
                        Route::get('create', 'create')->name('slide.create')->middleware('permission:create.slide');
                        Route::post('store', 'store')->name('slide.store')->middleware('permission:create.slide');
                        Route::get('{slide}/edit', 'edit')->name('slide.edit')->middleware('permission:edit.slide');
                        Route::post('{slide}', 'update')->name('slide.update')->middleware('permission:edit.slide');
                        Route::get('destroy/{slide}', 'destroy')->name('slide.destroy')->middleware('permission:delete.slide');
                    });
                    Route::controller(TestimonialController::class)->prefix('testimonial')->group(function () {
                        Route::get('/', 'index')->name('testimonial.index')->middleware('permission:view.testimonial');
                        Route::get('create', 'create')->name('testimonial.create')->middleware('permission:create.testimonial');
                        Route::post('store', 'store')->name('testimonial.store')->middleware('permission:create.testimonial');
                        Route::get('{testimonial}/edit', 'edit')->name('testimonial.edit')->middleware('permission:edit.testimonial');
                        Route::post('{testimonial}', 'update')->name('testimonial.update')->middleware('permission:edit.testimonial');
                        Route::get('destroy/{testimonial}', 'destroy')->name('testimonial.destroy')->middleware('permission:delete.testimonial');
                    });
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

        Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
        
        Route::group(['prefix' => '{username}'], function () {
            Route::middleware(['auth', 'identify.tenant'])->group(function () {
                Route::get('/dashboard', function () {
                return view('index');
            })->name('dashboard');
            Route::controller(ProfileController::class)->prefix('profile')->group(function () {
                Route::get('/', 'index')->name('profile.index');
                Route::get('{id?}', 'index')->name('profile.index');
                Route::post('update/{id?}', 'update')->name('profile.update');
                Route::post('change-password/{id?}', 'changePassword')->name('profile.change-password');
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
                Route::get('destroy/{store_view}', 'destroy')->name('store_view.destroy')->middleware('permission:delete.store_view');
            });
            Route::controller(CategoryController::class)->prefix('category')->group(function () {
                Route::get('/', 'index')->name('category.index')->middleware('permission:view.category');
                Route::get('create', 'create')->name('category.create')->middleware('permission:create.category');
                Route::post('store', 'store')->name('category.store')->middleware('permission:create.category');
                Route::get('{category}/edit', 'edit')->name('category.edit')->middleware('permission:edit.category');
                Route::post('{category}', 'update')->name('category.update')->middleware('permission:edit.category');
                Route::get('destroy/{category}', 'destroy')->name('category.destroy')->middleware('permission:delete.category');
            });
            Route::controller(PageController::class)->prefix('page')->group(function () {
                Route::get('/', 'index')->name('page.index')->middleware('permission:view.page');
                Route::get('create', 'create')->name('page.create')->middleware('permission:create.page');
                Route::post('store', 'store')->name('page.store')->middleware('permission:create.page');
                Route::get('{page}/edit', 'edit')->name('page.edit')->middleware('permission:edit.page');
                Route::post('{page}', 'update')->name('page.update')->middleware('permission:edit.page');
                Route::get('destroy/{page}', 'destroy')->name('page.destroy')->middleware('permission:delete.page');
            });
            Route::controller(SettingController::class)->prefix('setting')->group(function () {
                Route::get('/', 'index')->name('setting.index')->middleware('permission:view.setting');
                Route::get('create/{admin?}', 'create')->name('setting.create')->middleware('permission:create.setting');
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
                Route::get('destroy/{slide}', 'destroy')->name('slide.destroy')->middleware('permission:delete.slide');
            });
            Route::controller(TestimonialController::class)->prefix('testimonial')->group(function () {
                Route::get('/', 'index')->name('testimonial.index')->middleware('permission:view.testimonial');
                Route::get('create', 'create')->name('testimonial.create')->middleware('permission:create.testimonial');
                Route::post('store', 'store')->name('testimonial.store')->middleware('permission:create.testimonial');
                Route::get('{testimonial}/edit', 'edit')->name('testimonial.edit')->middleware('permission:edit.testimonial');
                Route::post('{testimonial}', 'update')->name('testimonial.update')->middleware('permission:edit.testimonial');
                Route::get('destroy/{testimonial}', 'destroy')->name('testimonial.destroy')->middleware('permission:delete.testimonial');
            });
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
        return redirect()->route('dashboard',  ['username' => request()->segment(1)]);
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