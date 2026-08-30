<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\Farm;
use App\Models\ChicksSupplier;
use App\Models\Admin;
use App\Models\Slaughter;
use App\Models\MaterialName;
use Illuminate\Http\Request;

/**
 * @group Add2Farm Dropdowns
 * APIs for fetching dropdown data (farms, suppliers, supervisors)
 * All dropdown APIs require authentication
 */
class DropdownController extends BaseController
{
    /**
     * Get farms for dropdown
     *
     * Fetch list of farms created by the logged-in user with id and name only for dropdown/select usage.
     * - Type 2 (Farm Owner) sees: farms they created
     * - Type 3 (Supervisor) sees: farms where they are assigned
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
    public function farms(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_authenticated'),
            ], 401);
        }

        $farms = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })
            ->select('id', 'name')
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
     * Fetch list of all chicks suppliers with id and name only for dropdown/select usage.
     * Returns all available suppliers (not filtered by user).
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
    public function suppliers(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_authenticated'),
            ], 401);
        }

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
     * Get supervisors for dropdown
     *
     * Returns supervisors created by the logged-in user.
     * Returns different supervisor types based on the logged-in user's type:
     * - If user type = 2 (Farm Owner): returns farmers (type 4) created by this user
     * - If user type = 1 or 3 (Admin/Supervisor): returns supervisors (type 3) created by this user
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
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_authenticated'),
            ], 401);
        }

        // Determine supervisor type based on logged-in user's type
        $supervisorType = match($user->type) {
            Admin::PUBLIC_VENDOR => 4,  // type 2 users get type 4 supervisors (farmers)
            Admin::ADMIN => 3,           // type 1 users get type 3 supervisors
            default => 3,                // default to type 3
        };

        $supervisors = Admin::where('type', $supervisorType)
            ->where('created_by', $user->id)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('supervisors_retrieved_successfully'),
            'data' => $supervisors,
        ], 200);
    }

    /**
     * Get chicken breeds for dropdown
     *
     * Returns available chicken breeds organized by category (Broiler and Layer).
     * Used for flock creation form.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Breeds retrieved successfully.",
     *   "data": [
     *     {
     *       "category": "Broiler",
     *       "breeds": [
     *         "Ross 308",
     *         "Cobb 500"
     *       ]
     *     },
     *     {
     *       "category": "Layer",
     *       "breeds": [
     *         "Lohmann Brown",
     *         "Lohmann White"
     *       ]
     *     }
     *   ]
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function breeds(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_authenticated'),
            ], 401);
        }

        $breeds = [
            [
                'category' => 'Broiler',
                'breeds' => [
                    'Ross 308',
                    'Cobb 500',
                ],
            ],
            [
                'category' => 'Layer',
                'breeds' => [
                    'Lohmann Brown',
                    'Lohmann White',
                ],
            ],
        ];

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('breeds_retrieved_successfully'),
            'data' => $breeds,
        ], 200);
    }

    /**
     * Get material names for dropdown
     *
     * Fetch list of all material names for feed and ingredients dropdown.
     * Returns material id and name for dropdown usage.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Material names retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Starter feed"
     *     },
     *     {
     *       "id": 2,
     *       "name": "Grower feed"
     *     },
     *     {
     *       "id": 3,
     *       "name": "Finisher feed"
     *     },
     *     {
     *       "id": 4,
     *       "name": "Corn"
     *     },
     *     {
     *       "id": 5,
     *       "name": "Soybean meal"
     *     },
     *     {
     *       "id": 6,
     *       "name": "Wheat"
     *     },
     *     {
     *       "id": 7,
     *       "name": "Barley"
     *     },
     *     {
     *       "id": 8,
     *       "name": "Premix"
     *     },
     *     {
     *       "id": 9,
     *       "name": "Limestone"
     *     }
     *   ]
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function materialNames(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_authenticated'),
            ], 401);
        }

        $materials = MaterialName::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Material names retrieved successfully.',
            'data' => $materials,
        ], 200);
    }

    /**
     * Get slaughter houses for dropdown
     *
     * Fetch list of all slaughter houses/buyers for end flock sales.
     * Returns slaughter house id and name for dropdown usage.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Slaughter houses retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Al Saeed Trading Co."
     *     },
     *     {
     *       "id": 2,
     *       "name": "Premium Poultry Ltd"
     *     }
     *   ]
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated"
     * }
     */
    public function slaughterers(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_authenticated'),
            ], 401);
        }

        $slaughterers = Slaughter::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Slaughter houses retrieved successfully.',
            'data' => $slaughterers,
        ], 200);
    }
}
