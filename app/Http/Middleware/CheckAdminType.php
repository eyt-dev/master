<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminType
{
    /**
     * Handle an incoming request.
     *
     * Allow access only if user type matches allowed types.
     * Usage: middleware('check.admin.type:1,2')
     */
    public function handle(Request $request, Closure $next, ...$allowedTypes)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Convert string types to integers for comparison
        $allowedTypes = array_map('intval', $allowedTypes);

        if (!in_array($user->type, $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}
