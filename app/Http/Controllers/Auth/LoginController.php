<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;

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

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Handle a login attempt with email or mobile number.
     */
    protected function attemptLogin(Request $request)
    {
        $login = $request->input('login');
        $password = $request->input('password');

        if (empty($login) || empty($password)) {
            return false;
        }

        // Determine if login is email or mobile number
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            // Login with email
            $admin = Admin::where('email', $login)->first();
        } else {
            // Login with mobile number (supports formats like "+91 9876543210", "+919876543210", "9876543210")
            $admin = $this->findAdminByMobileNumber($login);
        }

        if ($admin && Hash::check($password, $admin->password)) {
            // Set the login field for the guard
            return $this->guard()->loginUsingId($admin->id, $request->filled('remember'));
        }

        return false;
    }

    /**
     * Find admin by mobile number with flexible format support.
     * Handles: "+91 9876543210", "+919876543210", "9876543210"
     */
    private function findAdminByMobileNumber($input): ?Admin
    {
        // First, try exact match (stored as just the mobile number)
        $admin = Admin::where('mobile_number', $input)->first();
        if ($admin) {
            return $admin;
        }

        // Check if input starts with + (international format)
        if (strpos($input, '+') === 0) {
            // Remove spaces first
            $cleaned = str_replace(' ', '', $input);

            // Remove the + for easier parsing
            $digitsOnly = ltrim($cleaned, '+');

            // Try different phone code lengths (1, 2, 3 digits)
            for ($codeLength = 1; $codeLength <= 3; $codeLength++) {
                if (strlen($digitsOnly) <= $codeLength) {
                    continue;
                }

                $phoneCode = substr($digitsOnly, 0, $codeLength);
                $mobileNumber = substr($digitsOnly, $codeLength);

                // Try without + in phone code
                $admin = Admin::where('phone_code', $phoneCode)
                    ->where('mobile_number', $mobileNumber)
                    ->first();
                if ($admin) {
                    return $admin;
                }

                // Also try with + in phone code
                $admin = Admin::where('phone_code', '+' . $phoneCode)
                    ->where('mobile_number', $mobileNumber)
                    ->first();
                if ($admin) {
                    return $admin;
                }
            }

            // Try as full mobile number without phone code
            $admin = Admin::where('mobile_number', $digitsOnly)->first();
            if ($admin) {
                return $admin;
            }
        }

        return null;
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return back()
            ->withInput($request->only('login', 'remember'))
            ->withErrors([
                'login' => trans('auth.failed'),
            ]);
    }

    public function authenticated(Request $request, $user = null)
    {
        $user = $user ?? auth()->user();
        $host = $request->getHost();
        $username = $request->route('username');
        $setting = $user->setting;

        // Check conditions for specific routing
        if ($host === config('domains.admin_subdomain') && $user->hasRole('SuperAdmin') && !$username) {
            return redirect('/e/dashboard');
        }

        if ($host === config('domains.admin_subdomain') && ($username === $user->username || $username == 'e')) {
            return redirect("/$username/dashboard");
        }

        if ($setting && $setting->admin_domain === $host) {
            return redirect('/e/dashboard');
        }

        // Default redirect to dashboard if no specific conditions matched
        return redirect($this->redirectTo ?? '/e/dashboard');
    }
}
