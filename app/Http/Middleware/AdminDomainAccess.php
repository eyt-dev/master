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
        // dump($setting);

        if (!$setting) {
            return redirect()->away(config('app.url'))->with('error', 'Unauthorized domain.');
        }

        $admin = $setting->creator;
        $siteSlug = $request->segment(1);
        // dump($siteSlug);

        $notValid = 0;
        View::share('siteSlug', $siteSlug);
        $request->attributes->set('site', $siteSlug);
        $siteIsValidAdmin = Admin::where('username', $siteSlug)->first();
        // dd($siteIsValidAdmin->id, $admin);

        if($siteSlug != 'e') {

            if($admin->getRoleNames()->first() == 'SuperAdmin' && !$siteIsValidAdmin && $siteIsValidAdmin->id != $admin) {
                $notValid = 1;
            } 
            // elseif($admin->getRoleNames()->first() != 'SuperAdmin') {
                // setting-> domain --- if not superadmin domain and the request parameter of any username condition
            // }
        }

        // Ensure current user matches the creator/admin
        if (
            !$admin 
            || (Auth::check() && Auth::id() !== $admin->id) 
            || $notValid == 1
        ) {
            // dd('go  to home');
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->away(config('app.url'))->with('error', 'Unauthorized domain access.');
        }                

        return $next($request);
    }
}
