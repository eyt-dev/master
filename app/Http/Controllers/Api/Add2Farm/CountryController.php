<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\Country;
use Illuminate\Http\Request;

/**
 * @group Add2Farm Countries
 * APIs for fetching country data including phone codes
 */
class CountryController extends BaseController
{
    /**
     * List all countries with phone codes
     *
     * Get list of all countries with their phone codes (dial codes).
     * Useful for mobile number input fields and country selection dropdowns.
     *
     * @unauthenticated
     * @queryParam search string optional Search by country name. Example: India
     * @queryParam per_page integer optional Items per page. Default: 50. Example: 20
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Countries retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 101,
     *       "name": "India",
     *       "dial_code": "+91",
     *       "alpha_2_code": "IN",
     *       "alpha_3_code": "IND"
     *     },
     *     {
     *       "id": 185,
     *       "name": "Pakistan",
     *       "dial_code": "+92",
     *       "alpha_2_code": "PK",
     *       "alpha_3_code": "PAK"
     *     },
     *     {
     *       "id": 1,
     *       "name": "United States",
     *       "dial_code": "+1",
     *       "alpha_2_code": "US",
     *       "alpha_3_code": "USA"
     *     }
     *   ]
     * }
     */
    public function index(Request $request)
    {
        $countries = Country::when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', "%{$request->search}%");
            })
            ->orderBy('name', 'asc')
            ->paginate($request->per_page ?? 50);

        $countries->setCollection($countries->getCollection()->map(function ($country) {
            return [
                'id'              => $country->id,
                'name'            => $country->name,
                'dial_code'       => $country->dial_code,
                'alpha_2_code'    => $country->alpha_2_code,
                'alpha_3_code'    => $country->alpha_3_code,
            ];
        }));

        return response()->json([
            'success' => true,
            'message' => 'Countries retrieved successfully.',
            'data' => $countries,
        ]);
    }

    /**
     * Get country by ID or phone code
     *
     * Retrieve specific country information by ID or phone code.
     *
     * @unauthenticated
     * @queryParam phone_code string optional Search by phone code. Example: +91
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Country retrieved successfully.",
     *   "data": {
     *     "id": 101,
     *     "name": "India",
     *     "dial_code": "+91",
     *     "alpha_2_code": "IN",
     *     "alpha_3_code": "IND"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Country not found."
     * }
     */
    public function show(Request $request, $id = null)
    {
        $query = Country::query();

        // If phone_code is provided in query, search by that
        if ($request->has('phone_code')) {
            $query->where('dial_code', $request->phone_code);
        } elseif ($id) {
            // Otherwise search by ID
            $query->where('id', $id);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Country not found.',
            ], 404);
        }

        $country = $query->first();

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Country retrieved successfully.',
            'data' => [
                'id'              => $country->id,
                'name'            => $country->name,
                'dial_code'       => $country->dial_code,
                'alpha_2_code'    => $country->alpha_2_code,
                'alpha_3_code'    => $country->alpha_3_code,
            ],
        ]);
    }

    /**
     * Get all countries as simple array (for dropdowns)
     *
     * Get all countries in a simplified format suitable for dropdown selections.
     *
     * @unauthenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Countries retrieved successfully.",
     *   "data": [
     *     {
     *       "id": 101,
     *       "label": "India (+91)",
     *       "value": "+91",
     *       "country_name": "India"
     *     },
     *     {
     *       "id": 185,
     *       "label": "Pakistan (+92)",
     *       "value": "+92",
     *       "country_name": "Pakistan"
     *     }
     *   ]
     * }
     */
    public function dropdown()
    {
        $countries = Country::orderBy('name', 'asc')->get()->map(function ($country) {
            return [
                'id'              => $country->id,
                'label'           => "{$country->name} ({$country->dial_code})",
                'value'           => $country->dial_code,
                'country_name'    => $country->name,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Countries retrieved successfully.',
            'data' => $countries,
        ]);
    }
}
