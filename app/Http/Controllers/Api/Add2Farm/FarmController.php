<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\Admin;
use App\Models\Hangar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @group Add2Farm Farms
 * CRUD APIs for managing Farms - Accessible to Type 2 (Farm Owner) and Type 3 (Supervisor)
 */
class FarmController extends BaseController
{
    /**
     * List farms for current user
     *
     * Get paginated list of farms created by the user or assigned to them as supervisor.
     * - Type 2 (Farm Owner) sees: farms they created
     * - Type 3 (Supervisor) sees: farms where they are assigned
     *
     * Accessible to Type 2 (Farm Owner) and Type 3 (Supervisor).
     *
     * @authenticated
     * @queryParam page integer optional Pagination page number. Example: 1
     * @queryParam per_page integer optional Items per page. Default: 15. Example: 20
     * @queryParam search string optional Search by name or location. Example: Main Farm
     * @queryParam type string optional Filter by farm type. Example: Layer
     * @queryParam assigned_to integer optional Filter by assigned admin ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farms retrieved successfully.",
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "name": "Main Farm",
     *         "location": "Village A",
     *         "type": "Layer",
     *         "number_of_hangars": 5,
     *         "assigned_to": 1,
     *         "assigned_admin_name": "John Supervisor",
     *         "created_by_name": "Admin Name",
     *         "created_at": "2026-08-07T10:30:00Z"
     *       }
     *     ],
     *     "total": 10,
     *     "last_page": 1
     *   }
     * }
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Filter by user's own farms or assigned farms
        // Type 2 (Farm Owner) sees farms they created
        // Type 3 (Supervisor) sees farms where they are assigned
        $userFarms = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        });

        // Get total, active, and inactive farm counts for this user
        $totalFarms = (clone $userFarms)->count();
        $activeFarms = (clone $userFarms)->whereHas('flocks')->count();
        $inactiveFarms = $totalFarms - $activeFarms;

        $farms = (clone $userFarms)
            ->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('location', 'like', "%{$request->search}%");
            })
            ->when($request->type, function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->when($request->assigned_to, function ($q) use ($request) {
                return $q->where('assigned_to', $request->assigned_to);
            })
            ->with('assignedAdmin', 'creator', 'hangars')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        $farms->setCollection($farms->getCollection()->map(function ($farm) {
            return $this->formatFarm($farm);
        }));

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('farms_retrieved_successfully'),
            'total_farms' => $totalFarms,
            'active_farms' => $activeFarms,
            'inactive_farms' => $inactiveFarms,
            'data' => $farms,
        ]);
    }

    /**
     * Get a single farm
     *
     * Retrieve detailed information of a specific farm.
     *
     * @authenticated
     * @urlParam id integer required The farm ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farm retrieved successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "Main Farm",
     *     "location": "Village A",
     *     "type": "Layer",
     *     "number_of_hangars": 5,
     *     "assigned_to": 1,
     *     "assigned_admin_name": "John Supervisor",
     *     "created_by_name": "Admin Name",
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Farm not found."
     * }
     */
    public function show($id)
    {
        $farm = Farm::with('assignedAdmin', 'creator', 'hangars')->find($id);

        if (!$farm) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('farm_not_found'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('farm_retrieved_successfully'),
            'data' => $this->formatFarm($farm),
        ]);
    }

    /**
     * Create a new farm
     *
     * Create a new farm record with hangars. Accessible to Type 2 and Type 3 users.
     * Type 2 (Farm Owner) can create maximum 3 farms.
     * Type 3 (Supervisor) can create unlimited farms.
     * Each farmer (Type 4) can be assigned to only 1 farm.
     *
     * @authenticated
     * @bodyParam name string required Farm name. Example: Main Farm
     * @bodyParam location string required Farm location. Example: Village A
     * @bodyParam type string required Farm type (closed_system, open_system, or cages). Example: closed_system
     * @bodyParam phone_code string optional Farm phone country code. Example: +92
     * @bodyParam mobile_number string optional Farm phone number (without country code). Example: 3001234567
     * @bodyParam number_of_hangars integer required Number of hangars (must match hangars array length). Example: 3
     * @bodyParam assigned_to integer optional Admin ID (Type 4 Farmer) to assign this farm to. Example: 1
     * @bodyParam hangars array required Array of hangar objects (length must equal number_of_hangars). Example: [{"name": "Hangar 1", "area_sqm": 1000, "layer_hens": 5000}, {"name": "Hangar 2", "area_sqm": 1200, "layer_hens": 6000}, {"name": "Hangar 3", "area_sqm": 1100, "layer_hens": 5500}]
     * @bodyParam hangars[].name string required Hangar name. Example: Hangar 1
     * @bodyParam hangars[].area_sqm numeric optional Hangar area in square meters. Example: 1000
     * @bodyParam hangars[].layer_hens integer optional Number of layer hens. Example: 5000
     * @bodyParam hangars[].broiler_hens integer optional Number of broiler hens. Example: 0
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Farm created successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "Main Farm",
     *     "location": "Village A",
     *     "type": "closed_system",
     *     "mobile_number": "+92300123456",
     *     "number_of_hangars": 3,
     *     "assigned_to": null,
     *     "assigned_admin_name": null,
     *     "created_by_name": "John Admin",
     *     "has_flocks": false,
     *     "status": "Inactive",
     *     "hangars": [
     *       {
     *         "id": 1,
     *         "name": "Hangar 1",
     *         "area_sqm": 1000,
     *         "layer_hens": 5000,
     *         "broiler_hens": null
     *       },
     *       {
     *         "id": 2,
     *         "name": "Hangar 2",
     *         "area_sqm": 1200,
     *         "layer_hens": 6000,
     *         "broiler_hens": null
     *       },
     *       {
     *         "id": 3,
     *         "name": "Hangar 3",
     *         "area_sqm": 1100,
     *         "layer_hens": 5500,
     *         "broiler_hens": null
     *       }
     *     ],
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "name": ["The name field is required."],
     *     "type": ["The type must be one of: closed_system, open_system, cages."]
     *   }
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "You have reached the maximum limit of 3 farms."
     * }
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Type 2 (Farm Owner) can create maximum 3 farms
        if ($user->type == 2) {
            $farmCount = Farm::where('created_by', $user->id)->count();
            if ($farmCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the maximum limit of 3 farms.',
                ], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'name'                      => 'required|string|max:255',
            'location'                  => 'required|string|max:255',
            'type'                      => 'required|string|in:closed_system,open_system,cages',
            'phone_code'                => 'nullable|string|max:10',
            'mobile_number'             => 'nullable|string|max:20',
            'number_of_hangars'         => 'required|integer|min:1|max:999',
            'assigned_to'               => 'nullable|integer|exists:admins,id',
            'hangars'                   => 'required|array|min:1',
            'hangars.*.name'            => 'required|string|max:255',
            'hangars.*.area_sqm'        => 'required|numeric|min:0',
            'hangars.*.layer_hens'      => 'nullable|integer|min:0',
            'hangars.*.broiler_hens'    => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Validate hangars count matches number_of_hangars
        if (count($request->hangars) !== $request->number_of_hangars) {
            return response()->json([
                'success' => false,
                'message' => "Number of hangars provided (" . count($request->hangars) . ") does not match the specified count (" . $request->number_of_hangars . ").",
            ], 422);
        }

        // Validate that the assigned farmer doesn't already have a farm
        if ($request->filled('assigned_to')) {
            $existingFarm = Farm::where('assigned_to', $request->assigned_to)->first();
            if ($existingFarm) {
                return response()->json([
                    'success' => false,
                    'message' => 'This farmer is already assigned to another farm. Each farmer can be assigned to only 1 farm.',
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            $farm = Farm::create([
                'name'               => $request->name,
                'location'           => $request->location,
                'type'               => $request->type,
                'phone_code'         => $request->phone_code,
                'mobile_number'      => $request->mobile_number,
                'number_of_hangars'  => $request->number_of_hangars,
                'assigned_to'        => $request->assigned_to,
                'created_by'         => auth()->id(),
            ]);

            // Create hangars
            foreach ($request->hangars as $hangarData) {
                Hangar::create([
                    'farm_id'       => $farm->id,
                    'name'          => $hangarData['name'],
                    'area_sqm'      => $hangarData['area_sqm'] ?? null,
                    'layer_hens'    => $hangarData['layer_hens'] ?? null,
                    'broiler_hens'  => $hangarData['broiler_hens'] ?? null,
                    'created_by'    => auth()->id(),
                ]);
            }

            DB::commit();

            $farm->load('assignedAdmin', 'creator', 'hangars');

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('farm_created_successfully'),
                'data'    => $this->formatFarm($farm),
            ], 201);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('Farm creation database error: ' . $e->getMessage());

            $message = 'Database error occurred.';
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'Farm name already exists.';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'error'   => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Farm creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create farm.',
                'error'   => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update a farm
     *
     * Update farm details and hangars.
     * - To update existing hangars, include their ID
     * - To create new hangars, omit the ID
     * - Hangars not included in the array will be deleted
     * - Total hangars provided must equal number_of_hangars
     *
     * @authenticated
     * @urlParam id integer required The farm ID. Example: 1
     * @bodyParam name string required Farm name. Example: Updated Farm Name
     * @bodyParam location string required Farm location. Example: Updated Location
     * @bodyParam type string required Farm type (closed_system, open_system, or cages). Example: open_system
     * @bodyParam phone_code string optional Farm phone country code. Example: +92
     * @bodyParam mobile_number string optional Farm phone number (without country code). Example: 3001234567
     * @bodyParam number_of_hangars integer required Number of hangars (must match hangars array length). Example: 3
     * @bodyParam assigned_to integer optional Farmer ID to assign the farm to. Example: 2
     * @bodyParam hangars array required Array of hangar objects (length must equal number_of_hangars). Example: [{"id": 1, "name": "Hangar 1 Updated", "area_sqm": 1100, "layer_hens": 5500}, {"id": 2, "name": "Hangar 2 Updated", "area_sqm": 1300, "layer_hens": 6500}, {"name": "Hangar 3 New", "area_sqm": 1000, "layer_hens": 5000}]
     * @bodyParam hangars[].id integer optional Hangar ID (if updating existing hangar). Example: 1
     * @bodyParam hangars[].name string required Hangar name. Example: Hangar 1 Updated
     * @bodyParam hangars[].area_sqm numeric optional Hangar area in square meters. Example: 1100
     * @bodyParam hangars[].layer_hens integer optional Number of layer hens. Example: 5500
     * @bodyParam hangars[].broiler_hens integer optional Number of broiler hens. Example: 0
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farm updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "Updated Farm Name",
     *     "location": "Updated Location",
     *     "type": "open_system",
     *     "mobile_number": "+92300123456",
     *     "number_of_hangars": 3,
     *     "assigned_to": 2,
     *     "assigned_admin_name": "Farmer Name",
     *     "created_by_name": "Farm Owner Name",
     *     "has_flocks": false,
     *     "status": "Inactive",
     *     "hangars": [
     *       {
     *         "id": 1,
     *         "name": "Hangar 1 Updated",
     *         "area_sqm": 1100,
     *         "layer_hens": 5500,
     *         "broiler_hens": null
     *       },
     *       {
     *         "id": 2,
     *         "name": "Hangar 2 Updated",
     *         "area_sqm": 1300,
     *         "layer_hens": 6500,
     *         "broiler_hens": null
     *       },
     *       {
     *         "id": 3,
     *         "name": "Hangar 3 New",
     *         "area_sqm": 1000,
     *         "layer_hens": 5000,
     *         "broiler_hens": null
     *       }
     *     ],
     *     "created_at": "2026-08-07T10:30:00Z",
     *     "updated_at": "2026-08-10T14:22:00Z"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Farm not found."
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "This farmer is already assigned to another farm. Each farmer can be assigned to only 1 farm."
     * }
     */
    public function update(Request $request, $id)
    {
        $farm = Farm::find($id);

        if (!$farm) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('farm_not_found'),
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'                      => 'required|string|max:255',
            'location'                  => 'required|string|max:255',
            'type'                      => 'required|string|in:closed_system,open_system,cages',
            'phone_code'                => 'nullable|string|max:10',
            'mobile_number'             => 'nullable|string|max:20',
            'number_of_hangars'         => 'required|integer|min:1|max:999',
            'assigned_to'               => 'nullable|integer|exists:admins,id',
            'hangars'                   => 'required|array|min:1',
            'hangars.*.id'              => 'nullable|integer|exists:hangars,id',
            'hangars.*.name'            => 'required|string|max:255',
            'hangars.*.area_sqm'        => 'required|numeric|min:0',
            'hangars.*.layer_hens'      => 'nullable|integer|min:0',
            'hangars.*.broiler_hens'    => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Validate hangars count matches number_of_hangars
        if (count($request->hangars) !== $request->number_of_hangars) {
            return response()->json([
                'success' => false,
                'message' => "Number of hangars provided (" . count($request->hangars) . ") does not match the specified count (" . $request->number_of_hangars . ").",
            ], 422);
        }

        // Validate that the assigned farmer doesn't already have a different farm
        if ($request->filled('assigned_to') && $request->assigned_to !== $farm->assigned_to) {
            $existingFarm = Farm::where('assigned_to', $request->assigned_to)
                ->where('id', '!=', $id)
                ->first();
            if ($existingFarm) {
                return response()->json([
                    'success' => false,
                    'message' => 'This farmer is already assigned to another farm. Each farmer can be assigned to only 1 farm.',
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            $farm->update([
                'name'               => $request->name,
                'location'           => $request->location,
                'type'               => $request->type,
                'phone_code'         => $request->phone_code,
                'mobile_number'      => $request->mobile_number,
                'number_of_hangars'  => $request->number_of_hangars,
                'assigned_to'        => $request->assigned_to,
            ]);

            // Update hangars
            // Delete hangars not in the new list
            $providedHangarIds = array_filter(array_column($request->hangars, 'id'));
            Hangar::where('farm_id', $farm->id)
                ->whereNotIn('id', $providedHangarIds)
                ->delete();

            // Create or update hangars
            foreach ($request->hangars as $hangarData) {
                if (isset($hangarData['id']) && !empty($hangarData['id'])) {
                    // Update existing hangar
                    Hangar::where('id', $hangarData['id'])->update([
                        'name'          => $hangarData['name'],
                        'area_sqm'      => $hangarData['area_sqm'] ?? null,
                        'layer_hens'    => $hangarData['layer_hens'] ?? null,
                        'broiler_hens'  => $hangarData['broiler_hens'] ?? null,
                    ]);
                } else {
                    // Create new hangar
                    Hangar::create([
                        'farm_id'       => $farm->id,
                        'name'          => $hangarData['name'],
                        'area_sqm'      => $hangarData['area_sqm'] ?? null,
                        'layer_hens'    => $hangarData['layer_hens'] ?? null,
                        'broiler_hens'  => $hangarData['broiler_hens'] ?? null,
                        'created_by'    => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            $farm->load('assignedAdmin', 'creator', 'hangars');

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('farm_updated_successfully'),
                'data'    => $this->formatFarm($farm),
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('Farm update database error: ' . $e->getMessage());

            $message = 'Database error occurred.';
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $message = 'Farm name already exists.';
            }

            return response()->json([
                'success' => false,
                'message' => $message,
                'error'   => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Farm update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update farm.',
                'error'   => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Delete a farm
     *
     * Delete a farm and all its related records.
     *
     * @authenticated
     * @urlParam id integer required The farm ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farm deleted successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Farm not found."
     * }
     */
    public function destroy($id)
    {
        $farm = Farm::find($id);

        if (!$farm) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('farm_not_found'),
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Delete the farm
            $farm->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('farm_deleted_successfully'),
            ], 200);

        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            \Log::error('Farm deletion database error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cannot delete farm. It may have related records.',
                'error'   => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Farm deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete farm.',
                'error'   => env('APP_DEBUG') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Format farm data for responses
     */
    private function formatFarm(Farm $farm): array
    {
        // Always load hangars if not already loaded
        if (!$farm->relationLoaded('hangars')) {
            $farm->load('hangars');
        }

        $hangars = $farm->hangars->map(function ($hangar) {
            return [
                'id'            => $hangar->id,
                'name'          => $hangar->name,
                'area_sqm'      => $hangar->area_sqm,
                'layer_hens'    => $hangar->layer_hens,
                'broiler_hens'  => $hangar->broiler_hens,
            ];
        })->toArray();

        // Check if farm has flocks
        $hasFlocks = $farm->flocks()->exists();

        // Check if logged-in user created or is assigned to this farm
        $assignment = (auth()->check() &&
            ($farm->created_by === auth()->id() || $farm->assigned_to === auth()->id())) ? 1 : 0;

        // Calculate total hangars count
        $totalHangars = $farm->hangars->count();

        // Calculate total area from all hangars
        $totalArea = $farm->hangars->sum('area_sqm');

        // Calculate total birds from all flocks
        $totalBirds = $farm->flocks()->sum('total_quantity') ?? 0;

        return [
            'id'                    => $farm->id,
            'name'                  => $farm->name,
            'location'              => $farm->location,
            'type'                  => $farm->type,
            'mobile_number'         => $farm->getFullPhoneNumber(),
            'number_of_hangars'     => $farm->number_of_hangars,
            'assigned_to'           => $farm->assigned_to,
            'assigned_admin_name'   => $farm->assignedAdmin?->name ?? null,
            'created_by_name'       => $farm->creator?->name ?? null,
            'assignment'            => $assignment,
            'has_flocks'            => $hasFlocks,
            'status'                => $hasFlocks ? 'Active' : 'Inactive',
            'hangars_count'         => $totalHangars,
            'area'                  => $totalArea,
            'birds'                 => $totalBirds,
            'hangars'               => $hangars,
            'created_at'            => $farm->created_at,
        ];
    }
}
