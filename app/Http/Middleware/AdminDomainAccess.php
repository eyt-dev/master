<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Models\Admin;
use App\Models\Setting;

class AdminDomainAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $request->getHost();
        $setting = Setting::where('domain', $domain)
            ->orWhere('admin_domain', $domain)
            ->first();

        if (!$setting) {
            return redirect()->away(config('app.url'))->with('error', 'Unauthorized domain.');
        }

        $admin = $setting->creator;

        // Ensure current user matches the creator/admin
        if (!$admin || (Auth::check() && Auth::id() !== $admin->id)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->away(config('app.url'))->with('error', 'Unauthorized domain access.');
        }

        // Get the first segment from URL (site1 in /site1/dashboard)
        $siteSlug = $request->segment(1);

        if($siteSlug != 'backend') {
            // Check if this is a valid admin username
            $siteIsValidAdmin = Admin::where('username', $siteSlug)->exists();

            // If not a valid admin username, redirect to /backend/... but preserve the rest of the path
            if (!$siteIsValidAdmin) {
                $pathAfterSite = implode('/', array_slice($request->segments(), 1)); // skip siteSlug
                return redirect("/backend/" . $pathAfterSite);
            }
        }

        // Share site with views if needed
        View::share('siteSlug', $siteSlug);
        $request->attributes->set('site', $siteSlug);

        return $next($request);
    }
}
