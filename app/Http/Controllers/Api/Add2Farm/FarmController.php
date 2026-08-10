<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @group Add2Farm Farms
 * CRUD APIs for managing Farms - Accessible to Type 2 (Farm Owner) and Type 3 (Supervisor)
 */
class FarmController extends Controller
{
    /**
     * List all farms
     *
     * Get paginated list of all farms with search and filtering.
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
        $farms = Farm::when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('location', 'like', "%{$request->search}%");
            })
            ->when($request->type, function ($q) use ($request) {
                return $q->where('type', $request->type);
            })
            ->when($request->assigned_to, function ($q) use ($request) {
                return $q->where('assigned_to', $request->assigned_to);
            })
            ->with('assignedAdmin', 'creator')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        $farms->setCollection($farms->getCollection()->map(function ($farm) {
            return $this->formatFarm($farm);
        }));

        return response()->json([
            'success' => true,
            'message' => 'Farms retrieved successfully.',
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
        $farm = Farm::with('assignedAdmin', 'creator')->find($id);

        if (!$farm) {
            return response()->json([
                'success' => false,
                'message' => 'Farm not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Farm retrieved successfully.',
            'data' => $this->formatFarm($farm),
        ]);
    }

    /**
     * Create a new farm
     *
     * Create a new farm record. Accessible to Type 2 and Type 3 users.
     * Type 2 (Farm Owner) can create maximum 3 farms.
     * Type 3 (Supervisor) can create unlimited farms.
     * Each farmer (Type 4) can be assigned to only 1 farm.
     *
     * @authenticated
     * @bodyParam name string required Farm name. Example: Main Farm
     * @bodyParam location string required Farm location. Example: Village A
     * @bodyParam type string required Farm type (Layer, Broiler, etc.). Example: Layer
     * @bodyParam number_of_hangars integer required Number of hangars. Example: 5
     * @bodyParam assigned_to integer optional Admin ID (Type 4 Farmer) to assign this farm to. Example: 1
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Farm created successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "Main Farm",
     *     "location": "Village A",
     *     "type": "Layer",
     *     "number_of_hangars": 5,
     *     "assigned_to": 1,
     *     "assigned_admin_name": "John Supervisor",
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "name": ["The name field is required."]
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
            'name'               => 'required|string|max:255',
            'location'           => 'required|string|max:255',
            'type'               => 'required|string|max:255',
            'number_of_hangars'  => 'required|integer|min:1|max:999',
            'assigned_to'        => 'nullable|integer|exists:admins,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
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
                'number_of_hangars'  => $request->number_of_hangars,
                'assigned_to'        => $request->assigned_to,
                'created_by'         => auth()->id(),
            ]);

            DB::commit();

            $farm->load('assignedAdmin', 'creator');

            return response()->json([
                'success' => true,
                'message' => 'Farm created successfully.',
                'data'    => $this->formatFarm($farm),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Farm creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create farm.',
            ], 500);
        }
    }

    /**
     * Update a farm
     *
     * Update farm information.
     * When reassigning a farmer, ensure the new farmer doesn't already have a farm assigned.
     *
     * @authenticated
     * @urlParam id integer required The farm ID. Example: 1
     * @bodyParam name string required Farm name. Example: Updated Farm Name
     * @bodyParam location string required Farm location. Example: Updated Location
     * @bodyParam type string required Farm type. Example: Broiler
     * @bodyParam number_of_hangars integer required Number of hangars. Example: 8
     * @bodyParam assigned_to integer optional Admin ID (Type 4 Farmer) to assign/reassign farm. Example: 2
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farm updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "Updated Farm Name",
     *     "location": "Updated Location",
     *     "type": "Broiler",
     *     "number_of_hangars": 8,
     *     "assigned_to": 2,
     *     "assigned_admin_name": "Jane Admin"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Farm not found."
     * }
     */
    public function update(Request $request, $id)
    {
        $farm = Farm::find($id);

        if (!$farm) {
            return response()->json([
                'success' => false,
                'message' => 'Farm not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'               => 'required|string|max:255',
            'location'           => 'required|string|max:255',
            'type'               => 'required|string|max:255',
            'number_of_hangars'  => 'required|integer|min:1|max:999',
            'assigned_to'        => 'nullable|integer|exists:admins,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
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
                'number_of_hangars'  => $request->number_of_hangars,
                'assigned_to'        => $request->assigned_to,
            ]);

            DB::commit();

            $farm->load('assignedAdmin', 'creator');

            return response()->json([
                'success' => true,
                'message' => 'Farm updated successfully.',
                'data'    => $this->formatFarm($farm->fresh()),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Farm update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update farm.',
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
                'message' => 'Farm not found.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Delete the farm
            $farm->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Farm deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Farm deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete farm.',
            ], 500);
        }
    }

    /**
     * Format farm data for responses
     */
    private function formatFarm(Farm $farm): array
    {
        return [
            'id'                    => $farm->id,
            'name'                  => $farm->name,
            'location'              => $farm->location,
            'type'                  => $farm->type,
            'number_of_hangars'     => $farm->number_of_hangars,
            'assigned_to'           => $farm->assigned_to,
            'assigned_admin_name'   => $farm->assignedAdmin?->name ?? null,
            'created_by_name'       => $farm->creator?->name ?? null,
            'created_at'            => $farm->created_at,
        ];
    }
}
