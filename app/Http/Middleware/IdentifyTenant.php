<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

class IdentifyTenant
{
    public function handle(Request $request, Closure $next)
    {
        $admin = auth()->user();
       
        if (! $admin) {
            return redirect()->route('login');
        }

        $host = $request->getHost();
        $username = $request->route('username');
        $setting = $admin->setting;
        // dd($admin,$setting);

        $siteSlug = $request->segment(1);
        // dump($siteSlug);

        $notValid = 0;
        View::share('siteSlug', $siteSlug);
        $request->attributes->set('username', $siteSlug);

        // Check admin-domain match
        if ($setting && $setting->admin_domain === $host) {
            // dd(1);
            return $next($request);
        }

        // Check main domain with username path
        if ($host === config('domains.admin_subdomain') && ($username === $admin->username || $username == 'e')) {
            // dd(2);
            return $next($request);
        }

        // Check for super-admin access
        if ($host === config('domains.admin_subdomain') && $admin->hasRole('SuperAdmin') && ! $username) {
            // dd(3);
            return $next($request);
        }

        auth()->logout();
        abort(403, 'Access denied.');
    }

}
