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
class DropdownController extends Controller
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
            'message' => 'Farms retrieved successfully.',
            'data' => $farms,
        ]);
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
            'message' => 'Suppliers retrieved successfully.',
            'data' => $suppliers,
        ]);
    }

    /**
     * Get all supervisors for dropdown
     *
     * Fetch list of supervisors (Type 3 admins) with id and name only for dropdown/select usage.
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
    public function supervisors()
    {
        $supervisors = Admin::where('type', 3)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Supervisors retrieved successfully.',
            'data' => $supervisors,
        ]);
    }
}
