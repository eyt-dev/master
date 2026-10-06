<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMobileVerificationPending
{
    protected $allowedPathPatterns = [
        '/auth/logout',
        '/auth/verify-otp',
        '/auth/resend-otp',
        '/profile',  // Allows all profile endpoints (GET and PUT)
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum');

        if ($user && $user->mobile_verification_pending) {
            $path = $request->getPathInfo();

            $isAllowed = false;
            foreach ($this->allowedPathPatterns as $pattern) {
                if (strpos($path, 'add2farm' . $pattern) !== false) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your mobile number change requires verification. Please verify your mobile number first.',
                    'mobile_verification_pending' => true,
                ], 403);
            }
        }

        return $next($request);
    }
}
