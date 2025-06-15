<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

class IdentifyTenantFront
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $domains = Setting::pluck('domain')->toArray();

        // Allow if host is not in managed domains
        if (!in_array($host, $domains)) {
            abort(403, 'Access denied.');
        }

        // Allow if it's a plain domain without username in path
        if (!$request->segment(1)) {
            return $next($request);
        }

        $username = $request->route('username') ?? $request->segment(1);
        $admin = Admin::where('username', $username)->first();

        if (!$admin) {
            abort(403, 'Access denied.');
        }

        // Share siteSlug with all views and attach to request
        View::share('siteSlug', $username);
        $request->attributes->set('username', $username);

        // Allow main domain with matching username or specific override
        if ($host === config('domains.main_domain') && ($username === $admin->username || $username === 'e')) {
            return $next($request);
        }

        // Allow super-admin access with no username
        if ($host === config('domains.main_domain') && !$username) {
            return $next($request);
        }

        // Default deny
        abort(403, 'Access denied.');
    }


}
