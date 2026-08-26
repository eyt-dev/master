<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class ValidatePasswordResetToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the token from the request body (not Authorization header)
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Missing password reset token in request body.',
            ], 400);
        }

        // Trim token to remove any whitespace
        $token = trim($token);

        // Find the token in the database
        $personalAccessToken = PersonalAccessToken::findToken($token);

        if (!$personalAccessToken) {
            \Log::warning('Password reset token not found in middleware', [
                'token_length' => strlen($token),
                'token_preview' => substr($token, 0, 20) . '...'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token.',
            ], 401);
        }

        // Check if token is a password-reset-token
        if ($personalAccessToken->name !== 'password-reset-token') {
            return response()->json([
                'success' => false,
                'message' => 'This token is not valid for password reset. Please use a password-reset token.',
            ], 403);
        }

        return $next($request);
    }
}
