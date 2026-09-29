<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Category;
use App\Models\ChicksSupplier;
use App\Models\CompoPrice;
use App\Models\Component;
use App\Models\Contact;
use App\Models\Country;
use App\Models\DailyRecord;
use App\Models\Element;
use App\Models\Farm;
use App\Models\FeedSupplier;
use App\Models\Flock;
use App\Models\FlockEnd;
use App\Models\Game;
use App\Models\Hangar;
use App\Models\MaterialStock;
use App\Models\Page;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Role;
use App\Models\Slaughter;
use App\Models\Slide;
use App\Models\StoreView;
use App\Models\Testimonial;
use App\Models\Unit;
use App\Models\Wheel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application backend/dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $modules = $this->getModuleStats($user);
        $siteSlug = request()->route('username');

        return view('index', compact('modules', 'siteSlug'));
    }

    /**
     * Get statistics for all modules based on user permissions
     */
    private function getModuleStats($user)
    {
        $modules = [];
        $isSuperAdmin = $user->role === 'SuperAdmin';

        // Main Category
        $modules['main'] = [
            'title' => 'Main',
            'items' => []
        ];

        // Customer Category
        $modules['customer'] = [
            'title' => 'Customer',
            'items' => []
        ];

        if ($user->can('view.admin')) {
            $adminCount = Admin::where('type', 'admin')
                ->when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['customer']['items'][] = [
                'name' => 'Admins',
                'count' => $adminCount,
                'route' => 'admins.index',
                'icon' => 'fe-user'
            ];
        }

        if ($user->can('view.public_vendor')) {
            $publicVendorCount = Admin::where('type', 'public_vendor')
                ->when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['customer']['items'][] = [
                'name' => 'Public Vendors',
                'count' => $publicVendorCount,
                'route' => 'admins.publicVendor',
                'icon' => 'fe-shopping-cart'
            ];
        }

        if ($user->can('view.private_vendor')) {
            $privateVendorCount = Admin::where('type', 'private_vendor')
                ->when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['customer']['items'][] = [
                'name' => 'Private Vendors',
                'count' => $privateVendorCount,
                'route' => 'admins.privateVendor',
                'icon' => 'fe-lock'
            ];
        }

        if ($user->can('view.user')) {
            $userCount = Admin::where('type', 'user')
                ->when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['customer']['items'][] = [
                'name' => 'Users',
                'count' => $userCount,
                'route' => 'admins.users',
                'icon' => 'fe-users'
            ];
        }

        // Fortune Wheel Category
        $modules['fortune_wheel'] = [
            'title' => 'Fortune Wheel',
            'items' => []
        ];

        if ($user->can('view.game')) {
            $modules['fortune_wheel']['items'][] = [
                'name' => 'Games',
                'count' => Game::count(),
                'route' => 'game.index',
                'icon' => 'fe-play-circle'
            ];
        }

        if ($user->can('view.wheel')) {
            $modules['fortune_wheel']['items'][] = [
                'name' => 'Wheels',
                'count' => Wheel::count(),
                'route' => 'wheel.index',
                'icon' => 'fe-rotate-cw'
            ];
        }

        // Configuration Category
        $modules['configuration'] = [
            'title' => 'Configuration',
            'items' => []
        ];

        if ($user->can('view.store_view')) {
            $modules['configuration']['items'][] = [
                'name' => 'Store View',
                'count' => StoreView::count(),
                'route' => 'store_view.index',
                'icon' => 'fe-eye'
            ];
        }

        if ($user->can('view.category')) {
            $modules['configuration']['items'][] = [
                'name' => 'Category',
                'count' => Category::count(),
                'route' => 'category.index',
                'icon' => 'fe-list'
            ];
        }

        if ($user->can('view.page')) {
            $modules['configuration']['items'][] = [
                'name' => 'Page',
                'count' => Page::count(),
                'route' => 'page.index',
                'icon' => 'fe-file'
            ];
        }

        if ($user->can('view.slide')) {
            $modules['configuration']['items'][] = [
                'name' => 'Slide',
                'count' => Slide::count(),
                'route' => 'slide.index',
                'icon' => 'fe-layers'
            ];
        }

        if ($user->can('view.testimonial')) {
            $modules['configuration']['items'][] = [
                'name' => 'Testimonial',
                'count' => Testimonial::count(),
                'route' => 'testimonial.index',
                'icon' => 'fe-message-square'
            ];
        }

        // Global Data Category
        $modules['global_data'] = [
            'title' => 'Global Data',
            'items' => []
        ];

        if ($user->can('view.country')) {
            $modules['global_data']['items'][] = [
                'name' => 'Country',
                'count' => Country::count(),
                'route' => 'country.index',
                'icon' => 'fe-globe'
            ];
        }

        if ($user->can('view.unit')) {
            $modules['global_data']['items'][] = [
                'name' => 'Unit',
                'count' => Unit::count(),
                'route' => 'unit.index',
                'icon' => 'fe-box'
            ];
        }

        if ($user->can('view.page')) {
            $modules['global_data']['items'][] = [
                'name' => 'Element',
                'count' => Element::count(),
                'route' => 'element.index',
                'icon' => 'fe-settings'
            ];
        }

        // Animal Nutrition Category
        $modules['animal_nutrition'] = [
            'title' => 'Animal Nutrition',
            'items' => []
        ];

        if ($user->can('view.component')) {
            $modules['animal_nutrition']['items'][] = [
                'name' => 'Component',
                'count' => Component::count(),
                'route' => 'component.index',
                'icon' => 'fe-package'
            ];
        }

        if ($user->can('view.compo_price')) {
            $modules['animal_nutrition']['items'][] = [
                'name' => 'Compo Price',
                'count' => CompoPrice::count(),
                'route' => 'compo_price.index',
                'icon' => 'fe-dollar-sign'
            ];
        }

        // ADD2CARE Farm Category
        $modules['farm'] = [
            'title' => 'ADD2CARE Farm',
            'items' => []
        ];

        if ($user->can('view.farm')) {
            $farmCount = Farm::when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where(function ($subQ) use ($user) {
                        $subQ->where('created_by', $user->id)
                             ->orWhere('assigned_to', $user->id)
                             ->orWhereHas('assignedAdmins', function ($query) use ($user) {
                                 $query->where('admin_id', $user->id);
                             });
                    });
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Farms',
                'count' => $farmCount,
                'route' => 'farm.index',
                'icon' => 'fe-home'
            ];
        }

        if ($user->can('view.hangar')) {
            $hangarCount = Hangar::when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->whereHas('farm', function ($subQ) use ($user) {
                        $subQ->where(function ($farmQ) use ($user) {
                            $farmQ->where('created_by', $user->id)
                                  ->orWhere('assigned_to', $user->id)
                                  ->orWhereHas('assignedAdmins', function ($query) use ($user) {
                                      $query->where('admin_id', $user->id);
                                  });
                        });
                    });
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Hangars',
                'count' => $hangarCount,
                'route' => 'hangar.index',
                'icon' => 'fe-inbox'
            ];
        }

        if ($user->can('view.feed_supplier')) {
            $feedSupplierCount = FeedSupplier::when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Feed Suppliers',
                'count' => $feedSupplierCount,
                'route' => 'feed-supplier.index',
                'icon' => 'fe-anchor'
            ];
        }

        if ($user->can('view.slaughter')) {
            $slaughterCount = Slaughter::when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Slaughters',
                'count' => $slaughterCount,
                'route' => 'slaughter.index',
                'icon' => 'fe-alert-circle'
            ];
        }

        if ($user->can('view.chicks_supplier')) {
            $chicksSupplierCount = ChicksSupplier::when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Chicks Suppliers',
                'count' => $chicksSupplierCount,
                'route' => 'chicks-supplier.index',
                'icon' => 'fe-truck'
            ];
        }

        if ($user->can('view.flock')) {
            $flockCount = Flock::when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->whereHas('farm', function ($subQ) use ($user) {
                        $subQ->where(function ($farmQ) use ($user) {
                            $farmQ->where('created_by', $user->id)
                                  ->orWhere('assigned_to', $user->id)
                                  ->orWhereHas('assignedAdmins', function ($query) use ($user) {
                                      $query->where('admin_id', $user->id);
                                  });
                        });
                    })
                    ->orWhere('created_by', $user->id);
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Flocks',
                'count' => $flockCount,
                'route' => 'flock.index',
                'icon' => 'fe-target'
            ];
        }

        if ($user->can('view.chicken_sale')) {
            $flockEndCount = FlockEnd::when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->whereHas('flock.farm', function ($subQ) use ($user) {
                        $subQ->where(function ($farmQ) use ($user) {
                            $farmQ->where('created_by', $user->id)
                                  ->orWhere('assigned_to', $user->id)
                                  ->orWhereHas('assignedAdmins', function ($query) use ($user) {
                                      $query->where('admin_id', $user->id);
                                  });
                        });
                    })
                    ->orWhere('created_by', $user->id);
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Ending Flock',
                'count' => $flockEndCount,
                'route' => 'chicken-sale.index',
                'icon' => 'fe-shopping-bag'
            ];
        }

        if ($user->can('view.feed_material')) {
            $materialNameCount = \DB::table('material_names')
                ->when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Feed Materials',
                'count' => $materialNameCount,
                'route' => 'feedmaterial.index',
                'icon' => 'fe-layers'
            ];
        }

        if ($user->can('view.material_stock')) {
            $materialStockCount = MaterialStock::when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Feed Stock',
                'count' => $materialStockCount,
                'route' => 'material-stock.index',
                'icon' => 'fe-inbox'
            ];
        }

        if ($user->can('view.daily_record')) {
            $dailyRecordCount = DailyRecord::when(!$isSuperAdmin, function ($q) use ($user) {
                    $q->where('created_by', $user->id);
                })
                ->count();
            $modules['farm']['items'][] = [
                'name' => 'Daily Records',
                'count' => $dailyRecordCount,
                'route' => 'daily-record.index',
                'icon' => 'fe-calendar'
            ];
        }

        return array_filter($modules, function ($category) {
            return !empty($category['items']);
        });
    }
}
