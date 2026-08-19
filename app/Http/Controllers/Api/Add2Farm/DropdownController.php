<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\ChicksSupplier;
use App\Models\Admin;
use Illuminate\Http\Request;

/**
 * @group Add2Farm Dropdowns
 * APIs for fetching dropdown data (farms, suppliers, supervisors)
 */
class DropdownController extends BaseController
{
    /**
     * Get all farms for dropdown
     *
     * Fetch list of farms with id and name only for dropdown/select usage.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farms retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Main Farm"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Secondary Farm"
     *     }
     *   ]
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function farms()
    {
        $farms = Farm::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('farms_retrieved_successfully'),
            'data' => $farms,
        ], 200);
    }

    /**
     * Get all chicks suppliers for dropdown
     *
     * Fetch list of chicks suppliers with id and name only for dropdown/select usage.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Suppliers retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Al-Rowad Farm"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Premium Chicks Co"
     *     }
     *   ]
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function suppliers()
    {
        $suppliers = ChicksSupplier::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('suppliers_retrieved_successfully'),
            'data' => $suppliers,
        ], 200);
    }

    /**
     * Get supervisors for dropdown based on logged-in user type
     *
     * Returns different supervisor types based on the logged-in user's type:
     * - If user type = 2 (PUBLIC_VENDOR): returns supervisors with type 4
     * - If user type = 1 (ADMIN): returns supervisors with type 3
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Supervisors retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "John Supervisor"
     *     },
     *     {
     *       "id": 3,
     *       "name": "Alice Supervisor"
     *     }
     *   ]
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function supervisors(Request $request)
    {
        $user = auth()->user();
        
        // Determine supervisor type based on logged-in user's type
        $supervisorType = match($user->type) {
            Admin::PUBLIC_VENDOR => 4,  // type 2 users get type 4 supervisors
            Admin::ADMIN => 3,           // type 1 users get type 3 supervisors
            default => 3,                // default to type 3
        };

        $supervisors = Admin::where('type', $supervisorType)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('supervisors_retrieved_successfully'),
            'data' => $supervisors,
        ], 200);
    }
}
