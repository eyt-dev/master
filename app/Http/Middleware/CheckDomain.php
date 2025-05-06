<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;
use App\Models\Setting;


class CheckDomain
{
    public function handle(Request $request, Closure $next)
    {
        $currentHost = $request->getHost(); // e.g. shop1.example.com
        // $url = request()->url();
        // Check if domain exists in users table
        $setting = Setting::where('domain', $currentHost)
            ->orWhere('admin_domain', $currentHost)
            ->first();
        // dd($currentHost,$setting);
        if (!$setting) {
            return redirect()->away(config('app.url'))->with('error', 'Something went wrong. Please contact the administrator.');
        }

        // You can optionally bind the user globally if needed:
        // app()->instance('current_user', $user);

        return $next($request);
    }
}
