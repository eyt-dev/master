<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\FeedSupplier;

/**
 * @group Add2Farm Dropdowns
 * Dropdown data for form selections
 */
class DropdownController extends BaseController
{
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
            'message' => $this->translationService->get('farm_types_retrieved_successfully', 'Farm types retrieved successfully.'),
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
            'message' => $this->translationService->get('feed_suppliers_retrieved_successfully', 'Feed suppliers retrieved successfully.'),
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
     *       "label": "Pelleted Feed"
     *     },
     *     {
     *       "value": "mash_feed",
     *       "label": "Mash Feed"
     *     },
     *     {
     *       "value": "feed_ingredient",
     *       "label": "Feed Ingredient"
     *     },
     *     {
     *       "value": "premix",
     *       "label": "Premix"
     *     }
     *   ]
     * }
     */
    public function materialTypes()
    {
        $types = [
            ['value' => 'pelleted_feed', 'label' => 'Pelleted Feed'],
            ['value' => 'mash_feed', 'label' => 'Mash Feed'],
            ['value' => 'feed_ingredient', 'label' => 'Feed Ingredient'],
            ['value' => 'premix', 'label' => 'Premix'],
        ];

        return response()->json([
            'success' => true,
            'message' => 'Material types retrieved successfully.',
            'data' => $types,
        ]);
    }
}
