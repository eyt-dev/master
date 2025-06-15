<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Setting;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating admins for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect admins after login.
     *
     * @var string
     */
    protected $redirectTo = '/e/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function authenticated(Request $request, $user = null)
    {
        $user = $user ?? auth()->user();
        $host = $request->getHost();
        $username = $request->route('username');
        $setting = $user->setting;

        // dd($user, $setting, $setting->admin_domain, $host);

        if ($host === config('domains.admin_subdomain') && $user->hasRole('SuperAdmin') && !$username) {
            return redirect('/e/dashboard');
        }

        if ($host === config('domains.admin_subdomain') && ($username === $user->username || $username == 'e')) {
            return redirect("/$username/dashboard");
        }

        if ($setting && $setting->admin_domain === $host) {
            return redirect('/e/dashboard');
        }

        auth()->logout();
        abort(403, 'Unauthorized context.');

    }
}
