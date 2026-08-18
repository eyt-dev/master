<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class RejectPasswordResetToken
{
    /**
     * Handle an incoming request.
     * Rejects any request using a password-reset-token on endpoints other than reset-password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the token from the Authorization header
        $token = $request->bearerToken();

        if ($token) {
            // Find the token in the database
            $personalAccessToken = PersonalAccessToken::findToken($token);

            if ($personalAccessToken && $personalAccessToken->name === 'password-reset-token') {
                return response()->json([
                    'success' => false,
                    'message' => 'This token can only be used for password reset. Please use /api/add2farm/auth/reset-password endpoint.',
                ], 403);
            }
        }

        return $next($request);
    }
}
