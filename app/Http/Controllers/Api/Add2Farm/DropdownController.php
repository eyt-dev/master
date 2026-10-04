<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\Farm;
use App\Models\ChicksSupplier;
use App\Models\Admin;
use App\Models\FeedSupplier;
use App\Models\MaterialName;
use App\Models\MaterialType;
use App\Models\Slaughter;
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

        $supervisorType = match($user->type) {
            Admin::PUBLIC_VENDOR => 4,
            Admin::ADMIN => 3,
            default => 3,
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
    public function breeds()
    {
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
     * Get slaughterers for dropdown
     *
     * Returns list of all slaughterers for harvest/sale operations.
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Slaughterers retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Slaughterer Name"
     *     }
     *   ]
     * }
     */
    public function slaughterers()
    {
        $slaughterers = Slaughter::select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Slaughterers retrieved successfully.',
            'data' => $slaughterers,
        ], 200);
    }

    /**
     * Get material names for dropdown
     *
     * Returns list of material names with their types.
     * Can be filtered by material type using query parameter.
     *
     * @authenticated
     * @queryParam material_type string Optional filter by material type (e.g., pelleted_feed, mash_feed, feed_ingredient, premix)
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Material names retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Layer Pellets",
     *       "type": "Pelleted feed"
     *     }
     *   ]
     * }
     */
    public function materialNames(Request $request)
    {
        $query = MaterialName::with('materialType')->select('id', 'name', 'material_type_id');

        if ($request->has('material_type')) {
            $materialType = $request->input('material_type');
            $materialTypeRecord = MaterialType::where('value', $materialType)->first();

            if ($materialTypeRecord) {
                $query->where('material_type_id', $materialTypeRecord->id);
            }
        }

        $materials = $query->orderBy('name')->get()->map(function ($material) {
            return [
                'id' => $material->id,
                'name' => $material->name,
                'type' => $material->materialType?->name ?? 'N/A',
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Material names retrieved successfully.',
            'data' => $materials,
        ], 200);
    }

    /**
     * Get farm types dropdown
     *
     * Returns list of available farm system types for dropdown selection.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farm types retrieved successfully.",
     *   "data": [
     *     {
     *       "value": "closed_system",
     *       "label": "Closed System"
     *     },
     *     {
     *       "value": "open_system",
     *       "label": "Open System"
     *     },
     *     {
     *       "value": "cages",
     *       "label": "Cages"
     *     }
     *   ]
     * }
     */
    public function farmTypes()
    {
        $types = [
            ['value' => 'closed_system', 'label' => 'Closed System'],
            ['value' => 'open_system', 'label' => 'Open System'],
            ['value' => 'cages', 'label' => 'Cages'],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Farm types retrieved successfully.',
            'data' => $types,
        ]);
    }

    /**
     * Get feed suppliers dropdown
     *
     * Returns list of available feed suppliers for dropdown selection.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Feed suppliers retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Feed Supplier Name"
     *     }
     *   ]
     * }
     */
    public function feedSuppliers()
    {
        $feedSuppliers = FeedSupplier::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'name' => $supplier->name,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Feed suppliers retrieved successfully.',
            'data' => $feedSuppliers,
        ]);
    }

    /**
     * Get material types dropdown
     *
     * Returns list of available material types for dropdown selection.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Material types retrieved successfully.",
     *   "data": [
     *     {
     *       "value": "pelleted_feed",
     *       "label": "Pelleted Feed",
     *       "hangar_allocation": true
     *     },
     *     {
     *       "value": "mash_feed",
     *       "label": "Mash Feed",
     *       "hangar_allocation": true
     *     },
     *     {
     *       "value": "feed_ingredient",
     *       "label": "Feed Ingredient",
     *       "hangar_allocation": false
     *     },
     *     {
     *       "value": "premix",
     *       "label": "Premix",
     *       "hangar_allocation": false
     *     }
     *   ]
     * }
     */
    public function materialTypes()
    {
        $types = MaterialType::select('value', 'name as label', 'hangar_allocation')
            ->orderBy('id')
            ->get()
            ->toArray();

        return response()->json([
            'success' => true,
            'message' => 'Material types retrieved successfully.',
            'data' => $types,
        ]);
    }
}
