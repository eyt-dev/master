<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Setting;

class AdminDomainAccess
{
    public function handle(Request $request, Closure $next)
    {
        $domain = $request->getHost();
        $setting = Setting::where('domain', $domain)->first();//admin_domain
        $admin = $setting->creator;
        
        if (!$admin || (Auth::check() && Auth::id() !== $admin->id)) {
             Auth::logout(); Auth::logout();

            //  // Invalidate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            // abort(403, 'Unauthorized access to admin panel.');
            return redirect()->away(config('app.url'))->with('error', 'Unauthorized domain access.');
        }

        return $next($request);
    }
}
