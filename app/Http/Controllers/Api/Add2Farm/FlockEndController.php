<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Flock;
use App\Models\FlockEnd;
use App\Models\FlockEndDetail;
use App\Models\FlockHangar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @group Add2Farm Flock Harvest/End Records
 * CRUD APIs for managing flock harvest and end records
 */
class FlockEndController extends BaseController
{
    /**
     * List all harvest records
     *
     * Get paginated list of all harvest records for flocks accessible to the logged-in user.
     *
     * @authenticated
     * @queryParam page integer optional Pagination page number. Example: 1
     * @queryParam per_page integer optional Items per page. Default: 15. Example: 20
     * @queryParam flock_id integer optional Filter by flock ID. Example: 7
     * @queryParam hangar_id integer optional Filter by hangar ID. Example: 22
     * @queryParam search string optional Search by flock name. Example: AdminFlock1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Harvest records retrieved successfully.",
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 4,
     *         "flock_id": 7,
     *         "flock_name": "AdminFlock1",
     *         "hangar_id": 22,
     *         "hangar_name": "Hangar 1",
     *         "slaughter_id": 1,
     *         "slaughter_name": "test11",
     *         "sale_date": "2026-08-28",
     *         "cages_count": 10,
     *         "birds_per_cage": 20,
     *         "total_birds_harvested": 200,
     *         "mortality_rate": "88.89"
     *       }
     *     ],
     *     "total": 1,
     *     "last_page": 1
     *   }
     * }
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        $query = FlockEnd::whereHas('flock', function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhereHas('farm', function ($q) use ($user) {
                  $q->where('assigned_to', $user->id);
              });
        })
        ->with('flock', 'hangar', 'slaughter', 'endedBy')
        ->when($request->flock_id, function ($q) use ($request) {
            return $q->where('flock_id', $request->flock_id);
        })
        ->when($request->hangar_id, function ($q) use ($request) {
            return $q->where('hangar_id', $request->hangar_id);
        })
        ->when($request->search, function ($q) use ($request) {
            return $q->whereHas('flock', function ($subQ) use ($request) {
                $subQ->where('name', 'like', "%{$request->search}%");
            });
        })
        ->orderBy('created_at', 'desc');

        $flockEnds = $query->paginate($request->per_page ?? 15);

        $flockEnds->setCollection($flockEnds->getCollection()->map(function ($record) {
            return $this->formatFlockEnd($record);
        }));

        return response()->json([
            'success' => true,
            'message' => 'Harvest records retrieved successfully.',
            'data' => $flockEnds,
        ]);
    }

    /**
     * Get a single harvest record
     *
     * Retrieve detailed information of a specific harvest record.
     *
     * @authenticated
     * @urlParam id integer required The harvest record ID. Example: 4
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Harvest record retrieved successfully.",
     *   "data": {
     *     "id": 4,
     *     "flock_id": 7,
     *     "flock_name": "AdminFlock1",
     *     "hangar_id": 22,
     *     "hangar_name": "Hangar 1",
     *     "slaughter_id": 1,
     *     "slaughter_name": "test11",
     *     "sale_date": "2026-08-28",
     *     "cages_count": 10,
     *     "birds_per_cage": 20,
     *     "total_birds_harvested": 200,
     *     "available_birds": 1800,
     *     "remaining_birds": 1600,
     *     "mortality_birds": 1600,
     *     "mortality_rate": "88.89",
     *     "batch_weight": "450",
     *     "avg_weight": "10.5",
     *     "notes": "Grade A birds",
     *     "ended_by_id": 2,
     *     "ended_by_name": "Admin",
     *     "created_at": "2026-08-31T07:15:11.000000Z"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Harvest record not found."
     * }
     */
    public function show($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        $flockEnd = FlockEnd::whereHas('flock', function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhereHas('farm', function ($q) use ($user) {
                  $q->where('assigned_to', $user->id);
              });
        })
        ->with('flock', 'hangar', 'slaughter', 'endedBy')
        ->find($id);

        if (!$flockEnd) {
            return response()->json([
                'success' => false,
                'message' => 'Harvest record not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Harvest record retrieved successfully.',
            'data' => $this->formatFlockEnd($flockEnd),
        ]);
    }

    /**
     * Create a new harvest record
     *
     * Record a harvest/sale event for a hangar in a flock.
     *
     * @authenticated
     * @bodyParam flock_id integer required The flock ID. Example: 7
     * @bodyParam slaughter_id integer optional Slaughter house ID. Example: 1
     * @bodyParam hangar_id integer required Hangar ID. Example: 22
     * @bodyParam sale_date date required Sale date (format: Y-m-d). Example: 2026-08-28
     * @bodyParam cages_count integer required Number of cages. Example: 10
     * @bodyParam cages_weight decimal required Weight per cage (kg). Example: 1.85
     * @bodyParam birds_per_cage integer required Birds per cage (1-25). Example: 20
     * @bodyParam gross_weight decimal required Total gross weight (kg). Example: 450
     * @bodyParam batch_weights array optional Batch weights details. Example: [{"batch_number": 1, "weight": 225}, {"batch_number": 2, "weight": 225}]
     * @bodyParam net_weight decimal required Net weight after processing (kg). Example: 431.5
     * @bodyParam avg_weight decimal required Average weight per bird (kg). Example: 10.5
     * @bodyParam notes string optional Additional notes. Example: Grade A birds
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Harvest recorded successfully.",
     *   "data": {
     *     "id": 4,
     *     "flock_id": 7,
     *     "flock_name": "AdminFlock1",
     *     "hangar_id": 22,
     *     "hangar_name": "Hangar 1",
     *     "slaughter_id": 1,
     *     "slaughter_name": "test11",
     *     "sale_date": "2026-08-28",
     *     "cages_count": 10,
     *     "birds_per_cage": 20,
     *     "total_birds_harvested": 200,
     *     "mortality_rate": "88.89"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {"cages_count": ["The cages count must be at least 1."]}
     * }
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'flock_id' => 'required|integer|exists:flocks,id',
            'sale_date' => 'required|date_format:Y-m-d',
            'slaughter_id' => 'nullable|integer|exists:slaughters,id',
            'hangar_id' => 'required|integer|exists:hangars,id',
            'cages_count' => 'required|integer|min:1',
            'cages_weight' => 'required|numeric|min:0.1',
            'birds_per_cage' => 'required|integer|min:1|max:25',
            'gross_weight' => 'required|numeric|min:0',
            'batch_weights' => 'nullable|array',
            'batch_weights.*.batch_number' => 'integer',
            'batch_weights.*.weight' => 'numeric|min:0',
            'net_weight' => 'required|numeric|min:0',
            'avg_weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $flock = Flock::where('created_by', $user->id)
            ->whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->with('flockHangarAllocations.hangar')
            ->find($request->flock_id);

        if (!$flock) {
            return response()->json([
                'success' => false,
                'message' => 'Flock not found.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            $hangarAllocation = $flock->flockHangarAllocations
                ->where('hangar_id', $request->hangar_id)
                ->first();

            if (!$hangarAllocation) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Hangar is not allocated to this flock.",
                ], 422);
            }

            $previousHarvests = FlockEnd::where('flock_id', $flock->id)
                ->where('hangar_id', $request->hangar_id)
                ->sum('total_birds_harvested');

            $availableBirds = $hangarAllocation->quantity - $previousHarvests;
            $totalBirdsHarvested = $request->cages_count * $request->birds_per_cage;

            if ($totalBirdsHarvested > $availableBirds) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Cannot harvest {$totalBirdsHarvested} birds. Only {$availableBirds} birds available.",
                ], 422);
            }

            $remainingBirds = $availableBirds - $totalBirdsHarvested;

            $flockEnd = FlockEnd::create([
                'flock_id' => $flock->id,
                'slaughter_id' => $request->slaughter_id,
                'hangar_id' => $request->hangar_id,
                'sale_date' => $request->sale_date,
                'cages_count' => $request->cages_count,
                'cages_weight' => $request->cages_weight,
                'birds_per_cage' => $request->birds_per_cage,
                'total_birds_harvested' => $totalBirdsHarvested,
                'available_birds' => $availableBirds,
                'remaining_birds' => $remainingBirds,
                'total_weight' => $request->gross_weight,
                'avg_weight_per_bird' => $request->avg_weight,
                'notes' => $request->notes,
                'ended_by' => auth()->id(),
            ]);

            if ($request->has('batch_weights')) {
                FlockEndDetail::create([
                    'flock_end_id' => $flockEnd->id,
                    'batch_number' => 1,
                    'gross_weight' => $request->gross_weight,
                    'batch_weights' => $request->batch_weights,
                ]);
            }

            DB::commit();

            $flockEnd->load('flock', 'hangar', 'slaughter', 'endedBy');

            return response()->json([
                'success' => true,
                'message' => 'Harvest recorded successfully.',
                'data' => $this->formatFlockEnd($flockEnd),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock harvest error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to record harvest.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a harvest record
     *
     * Update an existing harvest/end record for a flock.
     *
     * @authenticated
     * @urlParam id integer required The harvest record ID. Example: 4
     * @bodyParam slaughter_id integer optional Slaughter house ID. Example: 1
     * @bodyParam sale_date date required Sale date (format: Y-m-d). Example: 2026-08-28
     * @bodyParam cages_count integer required Number of cages. Example: 10
     * @bodyParam cages_weight decimal required Weight per cage (kg). Example: 1.85
     * @bodyParam birds_per_cage integer required Birds per cage (1-25). Example: 20
     * @bodyParam gross_weight decimal required Total gross weight (kg). Example: 450
     * @bodyParam batch_weights array optional Batch weights details. Example: [{"batch_number": 1, "weight": 225}, {"batch_number": 2, "weight": 225}]
     * @bodyParam net_weight decimal required Net weight after processing (kg). Example: 431.5
     * @bodyParam avg_weight decimal required Average weight per bird (kg). Example: 10.5
     * @bodyParam notes string optional Additional notes. Example: Grade A birds
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Harvest record updated successfully.",
     *   "data": {...}
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Harvest record not found."
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {...}
     * }
     */
    public function update(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        $flockEnd = FlockEnd::whereHas('flock', function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhereHas('farm', function ($q) use ($user) {
                  $q->where('assigned_to', $user->id);
              });
        })->find($id);

        if (!$flockEnd) {
            return response()->json([
                'success' => false,
                'message' => 'Harvest record not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'sale_date' => 'required|date_format:Y-m-d',
            'slaughter_id' => 'nullable|integer|exists:slaughters,id',
            'cages_count' => 'required|integer|min:1',
            'cages_weight' => 'required|numeric|min:0.1',
            'birds_per_cage' => 'required|integer|min:1|max:25',
            'gross_weight' => 'required|numeric|min:0',
            'batch_weights' => 'nullable|array',
            'batch_weights.*.batch_number' => 'integer',
            'batch_weights.*.weight' => 'numeric|min:0',
            'net_weight' => 'required|numeric|min:0',
            'avg_weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $flock = $flockEnd->flock;
            $hangarAllocation = $flock->flockHangarAllocations()
                ->where('hangar_id', $flockEnd->hangar_id)
                ->first();

            if (!$hangarAllocation) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Hangar is not allocated to this flock.",
                ], 422);
            }

            // Calculate available birds excluding current record
            $previousHarvests = FlockEnd::where('flock_id', $flock->id)
                ->where('hangar_id', $flockEnd->hangar_id)
                ->where('id', '!=', $id)
                ->sum('total_birds_harvested');

            $availableBirds = $hangarAllocation->quantity - $previousHarvests;
            $totalBirdsHarvested = $request->cages_count * $request->birds_per_cage;

            if ($totalBirdsHarvested > $availableBirds) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => "Cannot harvest {$totalBirdsHarvested} birds. Only {$availableBirds} birds available.",
                ], 422);
            }

            $remainingBirds = $availableBirds - $totalBirdsHarvested;

            $flockEnd->update([
                'slaughter_id' => $request->slaughter_id,
                'sale_date' => $request->sale_date,
                'cages_count' => $request->cages_count,
                'cages_weight' => $request->cages_weight,
                'birds_per_cage' => $request->birds_per_cage,
                'total_birds_harvested' => $totalBirdsHarvested,
                'available_birds' => $availableBirds,
                'remaining_birds' => $remainingBirds,
                'total_weight' => $request->gross_weight,
                'avg_weight_per_bird' => $request->avg_weight,
                'notes' => $request->notes,
            ]);

            if ($request->has('batch_weights')) {
                $detail = FlockEndDetail::where('flock_end_id', $flockEnd->id)->first();
                if ($detail) {
                    $detail->update([
                        'gross_weight' => $request->gross_weight,
                        'batch_weights' => $request->batch_weights,
                    ]);
                } else {
                    FlockEndDetail::create([
                        'flock_end_id' => $flockEnd->id,
                        'batch_number' => 1,
                        'gross_weight' => $request->gross_weight,
                        'batch_weights' => $request->batch_weights,
                    ]);
                }
            }

            DB::commit();

            $flockEnd->load('flock', 'hangar', 'slaughter', 'endedBy');

            return response()->json([
                'success' => true,
                'message' => 'Harvest record updated successfully.',
                'data' => $this->formatFlockEnd($flockEnd),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock harvest update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update harvest record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a harvest record
     *
     * Delete a harvest record for a flock.
     *
     * @authenticated
     * @urlParam id integer required The harvest record ID. Example: 4
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Harvest record deleted successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Harvest record not found."
     * }
     */
    public function destroy($id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        $flockEnd = FlockEnd::whereHas('flock', function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhereHas('farm', function ($q) use ($user) {
                  $q->where('assigned_to', $user->id);
              });
        })->find($id);

        if (!$flockEnd) {
            return response()->json([
                'success' => false,
                'message' => 'Harvest record not found.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            $flockEnd->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Harvest record deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock harvest deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete harvest record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function formatFlockEnd(FlockEnd $flockEnd): array
    {
        $mortality = $flockEnd->available_birds - $flockEnd->total_birds_harvested;
        $mortalityRate = $flockEnd->available_birds > 0
            ? ($mortality / $flockEnd->available_birds) * 100
            : 0;

        $batchDetails = FlockEndDetail::where('flock_end_id', $flockEnd->id)->first();

        return [
            'id' => $flockEnd->id,
            'flock_id' => $flockEnd->flock_id,
            'flock_name' => $flockEnd->flock?->name,
            'hangar_id' => $flockEnd->hangar_id,
            'hangar_name' => $flockEnd->hangar?->name,
            'slaughter_id' => $flockEnd->slaughter_id,
            'slaughter_name' => $flockEnd->slaughter?->name,
            'sale_date' => $flockEnd->sale_date?->format('Y-m-d'),
            'cages_count' => $flockEnd->cages_count,
            'birds_per_cage' => $flockEnd->birds_per_cage,
            'cages_weight' => $this->formatDecimal($flockEnd->cages_weight),
            'total_birds' => $flockEnd->available_birds,
            'total_birds_harvested' => $flockEnd->total_birds_harvested,
            'mortality_birds' => $mortality,
            'mortality_rate' => $this->formatDecimal($mortalityRate),
            'remaining_birds' => $flockEnd->remaining_birds,
            'gross_weight' => $this->formatDecimal($flockEnd->total_weight),
            'batch_weights' => $batchDetails?->batch_weights ?? null,
            'avg_weight' => $this->formatDecimal($flockEnd->avg_weight_per_bird),
            'notes' => $flockEnd->notes,
            'ended_by_id' => $flockEnd->ended_by,
            'ended_by_name' => $flockEnd->endedBy?->name,
            'created_at' => $flockEnd->created_at,
            'updated_at' => $flockEnd->updated_at,
        ];
    }
}
