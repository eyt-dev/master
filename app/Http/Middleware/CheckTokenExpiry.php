<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenExpiry
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()) {
            $token = $request->user()->currentAccessToken();

            if ($token && $token->expires_at && now()->isAfter($token->expires_at)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access token has expired. Please use refresh token to get a new access token.',
                ], 401);
            }
        }

        return $next($request);
    }
}
