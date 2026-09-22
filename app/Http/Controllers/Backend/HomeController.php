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
            $modules['customer']['items'][] = [
                'name' => 'Admins',
                'count' => Admin::where('type', 'admin')->count(),
                'route' => 'admins.index',
                'icon' => 'fe-user'
            ];
        }

        if ($user->can('view.public_vendor')) {
            $modules['customer']['items'][] = [
                'name' => 'Public Vendors',
                'count' => Admin::where('type', 'public_vendor')->count(),
                'route' => 'admins.publicVendor',
                'icon' => 'fe-shopping-cart'
            ];
        }

        if ($user->can('view.private_vendor')) {
            $modules['customer']['items'][] = [
                'name' => 'Private Vendors',
                'count' => Admin::where('type', 'private_vendor')->count(),
                'route' => 'admins.privateVendor',
                'icon' => 'fe-lock'
            ];
        }

        if ($user->can('view.user')) {
            $modules['customer']['items'][] = [
                'name' => 'Users',
                'count' => Admin::where('type', 'user')->count(),
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
            $modules['farm']['items'][] = [
                'name' => 'Farms',
                'count' => Farm::count(),
                'route' => 'farm.index',
                'icon' => 'fe-home'
            ];
        }

        if ($user->can('view.hangar')) {
            $modules['farm']['items'][] = [
                'name' => 'Hangars',
                'count' => Hangar::count(),
                'route' => 'hangar.index',
                'icon' => 'fe-inbox'
            ];
        }

        if ($user->can('view.feed_supplier')) {
            $modules['farm']['items'][] = [
                'name' => 'Feed Suppliers',
                'count' => FeedSupplier::count(),
                'route' => 'feed-supplier.index',
                'icon' => 'fe-anchor'
            ];
        }

        if ($user->can('view.slaughter')) {
            $modules['farm']['items'][] = [
                'name' => 'Slaughters',
                'count' => Slaughter::count(),
                'route' => 'slaughter.index',
                'icon' => 'fe-alert-circle'
            ];
        }

        if ($user->can('view.chicks_supplier')) {
            $modules['farm']['items'][] = [
                'name' => 'Chicks Suppliers',
                'count' => ChicksSupplier::count(),
                'route' => 'chicks-supplier.index',
                'icon' => 'fe-truck'
            ];
        }

        if ($user->can('view.flock')) {
            $modules['farm']['items'][] = [
                'name' => 'Flocks',
                'count' => Flock::count(),
                'route' => 'flock.index',
                'icon' => 'fe-target'
            ];
        }

        if ($user->can('view.chicken_sale')) {
            $modules['farm']['items'][] = [
                'name' => 'Ending Flock',
                'count' => FlockEnd::count(),
                'route' => 'chicken-sale.index',
                'icon' => 'fe-shopping-bag'
            ];
        }

        if ($user->can('view.feed_material')) {
            $modules['farm']['items'][] = [
                'name' => 'Feed Materials',
                'count' => \DB::table('material_names')->count(),
                'route' => 'feedmaterial.index',
                'icon' => 'fe-layers'
            ];
        }

        if ($user->can('view.material_stock')) {
            $modules['farm']['items'][] = [
                'name' => 'Feed Stock',
                'count' => MaterialStock::count(),
                'route' => 'material-stock.index',
                'icon' => 'fe-inbox'
            ];
        }

        if ($user->can('view.daily_record')) {
            $modules['farm']['items'][] = [
                'name' => 'Daily Records',
                'count' => DailyRecord::count(),
                'route' => 'daily-record.index',
                'icon' => 'fe-calendar'
            ];
        }

        return array_filter($modules, function ($category) {
            return !empty($category['items']);
        });
    }
}
