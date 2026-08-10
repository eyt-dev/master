<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Flock;
use App\Models\Farm;
use App\Models\FlockHangar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @group Add2Farm Flocks
 * CRUD APIs for managing flocks in Add2Farm
 */
class FlockController extends Controller
{
    /**
     * List all flocks
     *
     * Get paginated list of all flocks with search and filtering.
     *
     * @authenticated
     * @queryParam page integer optional Pagination page number. Example: 1
     * @queryParam per_page integer optional Items per page. Default: 15. Example: 20
     * @queryParam search string optional Search by flock name. Example: Flock1
     * @queryParam farm_id integer optional Filter by farm ID. Example: 1
     * @queryParam status string optional Filter by status. Example: Active
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Flocks retrieved successfully.",
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "name": "Farm1-Flock4",
     *         "farm_id": 1,
     *         "farm_name": "Main Farm",
     *         "chicks_supplier_id": 1,
     *         "chicks_supplier_name": "Al-Rowad Farm",
     *         "breed": "Broiler,Cobb 500",
     *         "start_date": "2026-05-18",
     *         "total_quantity": 12500,
     *         "created_by": 1,
     *         "created_by_name": "Admin Name",
     *         "created_at": "2026-08-07T10:30:00Z",
     *         "updated_at": "2026-08-07T10:30:00Z"
     *       }
     *     ],
     *     "total": 10,
     *     "last_page": 1
     *   }
     * }
     */
    public function index(Request $request)
    {
        $flocks = Flock::when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->farm_id, function ($q) use ($request) {
                return $q->where('farm_id', $request->farm_id);
            })
            ->with('farm', 'chicksSupplier', 'creator')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        $flocks->setCollection($flocks->getCollection()->map(function ($flock) {
            return $this->formatFlock($flock);
        }));

        return response()->json([
            'success' => true,
            'message' => 'Flocks retrieved successfully.',
            'data' => $flocks,
        ]);
    }

    /**
     * Get a single flock
     *
     * Retrieve detailed information of a specific flock.
     *
     * @authenticated
     * @urlParam id integer required The flock ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Flock retrieved successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "Farm1-Flock4",
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
     *     "chicks_supplier_id": 1,
     *     "chicks_supplier_name": "Al-Rowad Farm",
     *     "breed": "Broiler,Cobb 500",
     *     "start_date": "2026-05-18",
     *     "total_quantity": 12500,
     *     "created_by": 1,
     *     "created_by_name": "Admin Name",
     *     "hangars": [
     *       {
     *         "hangar_id": 1,
     *         "hangar_name": "Farm1-Hangar1",
     *         "quantity": 3000
     *       }
     *     ],
     *     "created_at": "2026-08-07T10:30:00Z",
     *     "updated_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Flock not found."
     * }
     */
    public function show($id)
    {
        $flock = Flock::with('farm', 'chicksSupplier', 'creator', 'flockHangarAllocations.hangar')
            ->find($id);

        if (!$flock) {
            return response()->json([
                'success' => false,
                'message' => 'Flock not found.',
            ], 404);
        }

        $data = $this->formatFlock($flock);
        $data['hangars'] = $flock->flockHangarAllocations->map(function ($allocation) {
            return [
                'hangar_id' => $allocation->hangar->id,
                'hangar_name' => $allocation->hangar->name,
                'quantity' => $allocation->quantity,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Flock retrieved successfully.',
            'data' => $data,
        ]);
    }

    /**
     * Create a new flock
     *
     * Create a new flock with hangar allocations.
     *
     * @authenticated
     * @bodyParam name string required Flock name. Example: Farm1-Flock4
     * @bodyParam farm_id integer required Farm ID. Example: 1
     * @bodyParam chicks_supplier_id integer required Chicks supplier ID. Example: 1
     * @bodyParam breed string required Breed name. Example: Broiler,Cobb 500
     * @bodyParam start_date date required Start date (format: dd-mm-yyyy). Example: 18-05-2026
     * @bodyParam total_quantity integer required Total number of chicks. Example: 12500
     * @bodyParam hangar_allocations array required Array of hangar allocations. Example: [{"hangar_id": 1, "quantity": 3000}]
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Flock created successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "Farm1-Flock4",
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
     *     "chicks_supplier_id": 1,
     *     "chicks_supplier_name": "Al-Rowad Farm",
     *     "breed": "Broiler,Cobb 500",
     *     "start_date": "2026-05-18",
     *     "total_quantity": 12500,
     *     "created_by": 1,
     *     "created_by_name": "Admin Name",
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "name": ["The name field is required."]
     *   }
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'farm_id'               => 'required|integer|exists:farms,id',
            'chicks_supplier_id'    => 'required|integer|exists:chicks_suppliers,id',
            'breed'                 => 'required|string|max:255',
            'start_date'            => 'required|date_format:d-m-Y',
            'total_quantity'        => 'required|integer|min:1',
            'hangar_allocations'    => 'required|array|min:1',
            'hangar_allocations.*.hangar_id' => 'required|integer|exists:hangars,id',
            'hangar_allocations.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Validate hangar quantities sum matches total quantity
        $totalAllocated = array_sum(array_column($request->hangar_allocations, 'quantity'));
        if ($totalAllocated != $request->total_quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Sum of hangar quantities must equal total quantity.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Convert date format from dd-mm-yyyy to yyyy-mm-dd
            $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->start_date);

            $flock = Flock::create([
                'name'                  => $request->name,
                'farm_id'               => $request->farm_id,
                'chicks_supplier_id'    => $request->chicks_supplier_id,
                'breed'                 => $request->breed,
                'start_date'            => $startDate,
                'total_quantity'        => $request->total_quantity,
                'created_by'            => auth()->id(),
            ]);

            // Create hangar allocations
            foreach ($request->hangar_allocations as $allocation) {
                FlockHangar::create([
                    'flock_id'  => $flock->id,
                    'hangar_id' => $allocation['hangar_id'],
                    'quantity'  => $allocation['quantity'],
                ]);
            }

            DB::commit();

            $flock->load('farm', 'chicksSupplier', 'creator');

            return response()->json([
                'success' => true,
                'message' => 'Flock created successfully.',
                'data'    => $this->formatFlock($flock),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create flock.',
            ], 500);
        }
    }

    /**
     * Update a flock
     *
     * Update flock details and hangar allocations.
     *
     * @authenticated
     * @urlParam id integer required The flock ID. Example: 1
     * @bodyParam name string required Flock name. Example: Farm1-Flock4 Updated
     * @bodyParam chicks_supplier_id integer required Chicks supplier ID. Example: 1
     * @bodyParam breed string required Breed name. Example: Broiler,Cobb 500
     * @bodyParam start_date date required Start date (format: dd-mm-yyyy). Example: 18-05-2026
     * @bodyParam total_quantity integer required Total number of chicks. Example: 12500
     * @bodyParam hangar_allocations array required Array of hangar allocations. Example: [{"hangar_id": 1, "quantity": 3000}]
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Flock updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "Farm1-Flock4 Updated",
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
     *     "chicks_supplier_id": 1,
     *     "chicks_supplier_name": "Al-Rowad Farm",
     *     "breed": "Broiler,Cobb 500",
     *     "start_date": "2026-05-18",
     *     "total_quantity": 12500,
     *     "created_by": 1,
     *     "created_by_name": "Admin Name",
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Flock not found."
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "Sum of hangar quantities must equal total quantity."
     * }
     */
    public function update(Request $request, $id)
    {
        $flock = Flock::find($id);

        if (!$flock) {
            return response()->json([
                'success' => false,
                'message' => 'Flock not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'chicks_supplier_id'    => 'required|integer|exists:chicks_suppliers,id',
            'breed'                 => 'required|string|max:255',
            'start_date'            => 'required|date_format:d-m-Y',
            'total_quantity'        => 'required|integer|min:1',
            'hangar_allocations'    => 'required|array|min:1',
            'hangar_allocations.*.hangar_id' => 'required|integer|exists:hangars,id',
            'hangar_allocations.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Validate hangar quantities sum matches total quantity
        $totalAllocated = array_sum(array_column($request->hangar_allocations, 'quantity'));
        if ($totalAllocated != $request->total_quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Sum of hangar quantities must equal total quantity.',
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Convert date format from dd-mm-yyyy to yyyy-mm-dd
            $startDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->start_date);

            $flock->update([
                'name'                  => $request->name,
                'chicks_supplier_id'    => $request->chicks_supplier_id,
                'breed'                 => $request->breed,
                'start_date'            => $startDate,
                'total_quantity'        => $request->total_quantity,
            ]);

            // Delete existing hangar allocations
            $flock->flockHangarAllocations()->delete();

            // Create new hangar allocations
            foreach ($request->hangar_allocations as $allocation) {
                FlockHangar::create([
                    'flock_id'  => $flock->id,
                    'hangar_id' => $allocation['hangar_id'],
                    'quantity'  => $allocation['quantity'],
                ]);
            }

            DB::commit();

            $flock->load('farm', 'chicksSupplier', 'creator');

            return response()->json([
                'success' => true,
                'message' => 'Flock updated successfully.',
                'data'    => $this->formatFlock($flock),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update flock.',
            ], 500);
        }
    }

    /**
     * Delete a flock
     *
     * Delete a flock and all its hangar allocations.
     *
     * @authenticated
     * @urlParam id integer required The flock ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Flock deleted successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Flock not found."
     * }
     */
    public function destroy($id)
    {
        $flock = Flock::find($id);

        if (!$flock) {
            return response()->json([
                'success' => false,
                'message' => 'Flock not found.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Delete hangar allocations
            $flock->flockHangarAllocations()->delete();

            // Delete the flock
            $flock->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Flock deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete flock.',
            ], 500);
        }
    }

    private function formatFlock(Flock $flock): array
    {
        return [
            'id'                    => $flock->id,
            'name'                  => $flock->name,
            'farm_id'               => $flock->farm_id,
            'farm_name'             => $flock->farm?->name,
            'chicks_supplier_id'    => $flock->chicks_supplier_id,
            'chicks_supplier_name'  => $flock->chicksSupplier?->name,
            'breed'                 => $flock->breed,
            'start_date'            => $flock->start_date?->format('Y-m-d'),
            'total_quantity'        => $flock->total_quantity,
            'created_by'            => $flock->created_by,
            'created_by_name'       => $flock->creator?->name,
            'created_at'            => $flock->created_at,
            'updated_at'            => $flock->updated_at,
        ];
    }
}
