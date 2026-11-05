<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminType
{
    /**
     * Handle an incoming request.
     *
     * Allow access only if the user's category matches an allowed entry.
     * Entries are "0" (Super Admin) or a type_mappings parent_type.
     * Usage: middleware('check.admin.type:0,Admin,Public Vendors')
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

        $allowed = $user->isSuperAdmin()
            ? in_array((string) \App\Models\Admin::SUPER_ADMIN, $allowedTypes, true)
            : in_array($user->parent_type, $allowedTypes, true);

        if (!$allowed) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        }

        return $next($request);
    }
}
