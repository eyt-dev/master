<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Flock;
use App\Models\Farm;
use App\Models\FlockHangar;
use App\Models\FlockEnd;
use App\Models\FlockEndDetail;
use App\Models\DailyRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @group Add2Farm Flocks
 * CRUD APIs for managing flocks in Add2Farm
 */
class FlockController extends BaseController
{
    /**
     * Get available flocks for logged-in user
     *
     * Get simplified list of all flocks available to the logged-in user.
     * Useful for dropdowns and quick selection in Daily Records.
     *
     * @authenticated
     * @queryParam farm_id integer optional Filter by farm ID. Example: 1
     * @queryParam status string optional Filter by status (active, pending). Example: active
     * @queryParam start_date string optional Filter flocks starting from this date (format: Y-m-d). Example: 2026-01-01
     * @queryParam end_date string optional Filter flocks up to this date (format: Y-m-d). Example: 2026-06-30
     * @queryParam period integer optional Filter by period in months (3, 6, 12). Example: 6
     * @queryParam breed_type string optional Filter by breed type. Example: Broiler
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Available flocks retrieved successfully.",
     *   "data": [
     *     {
     *       "flock_id": 1,
     *       "flock_name": "Farm1-Flock4",
     *       "farm": {
     *         "farm_id": 1,
     *         "farm_name": "Main Farm"
     *       }
     *     },
     *     {
     *       "flock_id": 2,
     *       "flock_name": "Farm2-Flock1",
     *       "farm": {
     *         "farm_id": 2,
     *         "farm_name": "Secondary Farm"
     *       }
     *     }
     *   ]
     * }
     */
    public function available(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        // Validate period parameter if provided
        if ($request->period && !in_array((int)$request->period, [3, 6, 12])) {
            return response()->json([
                'success' => false,
                'message' => 'Period must be one of: 3, 6, 12',
            ], 422);
        }

        // Calculate aggregates for available flocks
        $allFlocks = Flock::whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->when($request->farm_id, function ($q) use ($request) {
                return $q->where('farm_id', $request->farm_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $status = strtolower($request->status);
                if ($status === 'active') {
                    return $q->whereDate('start_date', '<=', now()->toDateString());
                } elseif ($status === 'pending') {
                    return $q->whereDate('start_date', '>', now()->toDateString());
                }
                return $q;
            })
            ->when($request->start_date, function ($q) use ($request) {
                return $q->whereDate('start_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->whereDate('start_date', '<=', $request->end_date);
            })
            ->when($request->period, function ($q) use ($request) {
                $months = (int)$request->period;
                $startDate = now()->subMonths($months)->toDateString();
                return $q->whereDate('start_date', '>=', $startDate);
            })
            ->when($request->breed_type, function ($q) use ($request) {
                return $q->where('breed', 'like', "%{$request->breed_type}%");
            })
            ->select('id', 'farm_id', 'total_quantity', 'start_date')
            ->get();

        $totalQty = $allFlocks->sum('total_quantity');
        $totalFarms = $allFlocks->pluck('farm_id')->unique()->count();

        // Count active flocks (started on or before today)
        $activeFlocks = $allFlocks->filter(function ($flock) {
            return $flock->start_date && $flock->start_date->format('Y-m-d') <= now()->format('Y-m-d');
        })->count();

        // Count pending flocks (not yet started)
        $completedFlocks = $allFlocks->filter(function ($flock) {
            return $flock->start_date && $flock->start_date->format('Y-m-d') > now()->format('Y-m-d');
        })->count();

        $flocks = Flock::whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->when($request->farm_id, function ($q) use ($request) {
                return $q->where('farm_id', $request->farm_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $status = strtolower($request->status);
                if ($status === 'active') {
                    return $q->whereDate('start_date', '<=', now()->toDateString());
                } elseif ($status === 'pending') {
                    return $q->whereDate('start_date', '>', now()->toDateString());
                }
                return $q;
            })
            ->when($request->start_date, function ($q) use ($request) {
                return $q->whereDate('start_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->whereDate('start_date', '<=', $request->end_date);
            })
            ->when($request->period, function ($q) use ($request) {
                $months = (int)$request->period;
                $startDate = now()->subMonths($months)->toDateString();
                return $q->whereDate('start_date', '>=', $startDate);
            })
            ->when($request->breed_type, function ($q) use ($request) {
                return $q->where('breed', 'like', "%{$request->breed_type}%");
            })
            ->with('farm', 'chicksSupplier', 'creator', 'flockHangarAllocations.hangar', 'flockEnds')
            ->orderBy('name', 'asc')
            ->get();

        $data = $flocks->map(function ($flock) {
            return $this->formatFlock($flock);
        });

        return response()->json([
            'success' => true,
            'message' => 'Available flocks retrieved successfully.',
            'total_qty' => $totalQty,
            'total_farms' => $totalFarms,
            'active_flocks' => $activeFlocks,
            'completed_flocks' => $completedFlocks,
            'data' => $data,
        ]);
    }

    /**
     * Get hangars for a specific flock
     *
     * Get all hangars allocated to a specific flock with their allocated quantities.
     * Used for end flock form to select hangar and enter final quantity.
     *
     * @authenticated
     * @urlParam flock_id integer required The flock ID. Example: 4
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Flock hangars retrieved successfully.",
     *   "data": [
     *     {
     *       "hangar_id": 12,
     *       "hangar_name": "Hangar 1",
     *       "allocated_qty": 300
     *     },
     *     {
     *       "hangar_id": 13,
     *       "hangar_name": "Hangar 2",
     *       "allocated_qty": 200
     *     }
     *   ]
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Flock not found."
     * }
     */
    public function flockHangars($flockId)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        $flock = Flock::where('created_by', $user->id)
            ->whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->with('flockHangarAllocations.hangar')
            ->find($flockId);

        if (!$flock) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('flock_not_found'),
            ], 404);
        }

        $hangars = $flock->flockHangarAllocations->map(function ($allocation) {
            return [
                'hangar_id' => $allocation->hangar_id,
                'hangar_name' => $allocation->hangar->name,
                'allocated_qty' => $allocation->quantity,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Flock hangars retrieved successfully.',
            'data' => $hangars,
        ]);
    }

    /**
     * Get farm hangars with allocation status
     *
     * Get all hangars for a farm with their allocation status in existing flocks.
     * Shows which hangars are already allocated and their quantities.
     *
     * @authenticated
     * @urlParam farm_id integer required The farm ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farm hangars retrieved successfully.",
     *   "data": {
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
     *     "hangars": [
     *       {
     *         "hangar_id": 1,
     *         "hangar_name": "Farm1-Hangar1",
     *         "is_allocated": true,
     *         "allocated_quantity": 10,
     *         "allocated_to_flock_id": 1,
     *         "allocated_to_flock_name": "Flock1"
     *       },
     *       {
     *         "hangar_id": 2,
     *         "hangar_name": "Farm1-Hangar2",
     *         "is_allocated": false,
     *         "allocated_quantity": 0,
     *         "allocated_to_flock_id": null,
     *         "allocated_to_flock_name": null
     *       }
     *     ]
     *   }
     * }
     */
    public function farmHangars($farmId)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        // Verify user has access to this farm
        $farm = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->find($farmId);

        if (!$farm) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('farm_not_found'),
            ], 404);
        }

        // Get all hangars for the farm
        $hangars = $farm->hangars()->get();

        // Get allocations for all flocks in this farm
        $allocations = FlockHangar::whereHas('flock', function ($q) use ($farmId) {
            $q->where('farm_id', $farmId);
        })->with('flock')->get()->keyBy('hangar_id');

        $data = $hangars->map(function ($hangar) use ($allocations) {
            $allocation = $allocations->get($hangar->id);

            return [
                'hangar_id' => $hangar->id,
                'hangar_name' => $hangar->name,
                'is_allocated' => $allocation ? true : false,
                'allocated_quantity' => $allocation?->quantity ?? 0,
                'allocated_to_flock_id' => $allocation?->flock?->id ?? null,
                'allocated_to_flock_name' => $allocation?->flock?->name ?? null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Farm hangars retrieved successfully.',
            'data' => [
                'farm_id' => $farm->id,
                'farm_name' => $farm->name,
                'hangars' => $data,
            ],
        ]);
    }

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
     * @queryParam status string optional Filter by status (active, pending). Example: active
     * @queryParam start_date string optional Filter flocks starting from this date (format: Y-m-d). Example: 2026-01-01
     * @queryParam end_date string optional Filter flocks up to this date (format: Y-m-d). Example: 2026-06-30
     * @queryParam period integer optional Filter by period in months (3, 6, 12). Example: 6
     * @queryParam breed_type string optional Filter by breed type. Example: Broiler
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
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        // Validate period parameter if provided
        if ($request->period && !in_array((int)$request->period, [3, 6, 12])) {
            return response()->json([
                'success' => false,
                'message' => 'Period must be one of: 3, 6, 12',
            ], 422);
        }

        // Calculate aggregates for all flocks of logged-in user
        $allFlocks = Flock::where('created_by', $user->id)
            ->whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->select('id', 'farm_id', 'total_quantity', 'start_date')
            ->get();

        $totalQty = $allFlocks->sum('total_quantity');
        $totalFarms = $allFlocks->pluck('farm_id')->unique()->count();

        // Count active flocks (started on or before today)
        $activeFlocks = $allFlocks->filter(function ($flock) {
            return $flock->start_date && $flock->start_date->format('Y-m-d') <= now()->format('Y-m-d');
        })->count();

        // Count pending flocks (not yet started)
        $completedFlocks = $allFlocks->filter(function ($flock) {
            return $flock->start_date && $flock->start_date->format('Y-m-d') > now()->format('Y-m-d');
        })->count();

        $flocks = Flock::where('created_by', $user->id)
            ->whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', "%{$request->search}%");
            })
            ->when($request->farm_id, function ($q) use ($request) {
                return $q->where('farm_id', $request->farm_id);
            })
            ->when($request->status, function ($q) use ($request) {
                $status = strtolower($request->status);
                if ($status === 'active') {
                    return $q->whereDate('start_date', '<=', now()->toDateString());
                } elseif ($status === 'pending') {
                    return $q->whereDate('start_date', '>', now()->toDateString());
                }
                return $q;
            })
            ->when($request->start_date, function ($q) use ($request) {
                return $q->whereDate('start_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->whereDate('start_date', '<=', $request->end_date);
            })
            ->when($request->period, function ($q) use ($request) {
                $months = (int)$request->period;
                $startDate = now()->subMonths($months)->toDateString();
                return $q->whereDate('start_date', '>=', $startDate);
            })
            ->when($request->breed_type, function ($q) use ($request) {
                return $q->where('breed', 'like', "%{$request->breed_type}%");
            })
            ->with('farm', 'chicksSupplier', 'creator', 'flockHangarAllocations.hangar', 'flockEnds')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        $flocks->setCollection($flocks->getCollection()->map(function ($flock) {
            return $this->formatFlock($flock);
        }));

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('flocks_retrieved_successfully'),
            'total_qty' => $totalQty,
            'total_farms' => $totalFarms,
            'active_flocks' => $activeFlocks,
            'completed_flocks' => $completedFlocks,
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
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        $flock = Flock::where('created_by', $user->id)
            ->whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->with('farm', 'chicksSupplier', 'creator', 'flockHangarAllocations.hangar', 'flockEnds')
            ->find($id);

        if (!$flock) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('flock_not_found'),
            ], 404);
        }

        $totalBird = $flock->flockHangarAllocations->sum('quantity');
        $latestFlockEnd = $flock->flockEnds()->latest('sale_date')->first();
        $age = $this->calculateFlockAge($flock->start_date, $latestFlockEnd?->sale_date);

        // Fetch daily records for this flock
        $dailyRecords = DailyRecord::where('flock_id', $flock->id)->orderBy('record_date', 'asc')->get();

        // Calculate dynamic metrics
        $totalMortality = $dailyRecords->sum('mortality');
        $totalEggs = $dailyRecords->sum('eggs_count');
        $totalFeedKg = $dailyRecords->sum('feed_kg');
        $avgWeight = $dailyRecords->avg('chicks_weight');
        $recordCount = $dailyRecords->count();

        // Calculate total harvested birds from all FlockEnd records
        $totalHarvested = FlockEnd::where('flock_id', $flock->id)->sum('total_birds_harvested');

        // Calculate mortality rate (only from daily records, not from unaccounted birds in harvest)
        $mortalityRate = $totalBird > 0 ? ($totalMortality / $totalBird) * 100 : 0;

        // Calculate average production (eggs per bird as percentage)
        $avgProduction = $totalBird > 0 && $recordCount > 0 ? ($totalEggs / ($totalBird * $recordCount)) * 100 : 0;

        // Determine flock type first
        $breedType = $this->extractBreedType($flock->breed);
        $isLayer = $breedType === 'Layer';
        $isBroiler = !$isLayer;
        $isEnded = (bool) $latestFlockEnd?->sale_date;

        // Calculate FCR (Feed Conversion Ratio) - varies by flock type
        $fcr = 0;
        if ($isLayer) {
            // For layers: FCR = feed_kg per egg
            $fcr = $totalEggs > 0 ? round($totalFeedKg / $totalEggs, 2) : 0;
        } elseif ($isBroiler && $isEnded) {
            // For broilers at harvest: FCR = feed_kg per kg of weight
            $totalWeight = $latestFlockEnd->total_weight ?? 0;
            $fcr = $totalWeight > 0 ? round($totalFeedKg / $totalWeight, 2) : 0;
        }

        // Calculate live birds: total - harvested - mortality from daily records
        $liveBirds = $totalBird - $totalHarvested - $totalMortality;

        // Generate chart data
        $chartData = $this->generateChartData($dailyRecords, $flock, $totalBird, $isLayer);

        $data = [
            'flock_name' => $flock->name,
            'breed' => $flock->breed,
            'flock_type' => $breedType,
            'total_bird' => $totalBird,
            'start_date' => $flock->start_date->format('Y-m-d'),
            'end_date' => $isEnded ? $latestFlockEnd->sale_date->format('Y-m-d') : null,
            'age' => $age,
            'live_birds' => $liveBirds,
            'mortality_rate' => round($mortalityRate, 2),
            'feed_consumed' => number_format($totalFeedKg, 2) . ' kg',
            'avg_weight' => $avgWeight ? round($avgWeight, 2) . ' kg' : 'N/A',
            'chart_data' => $chartData,
        ];

        // Add flock-condition flag and type-specific metrics
        if ($isBroiler) {
            $data['flock-condition'] = $isEnded ? 'broiler-ended' : 'broiler-active';
            if ($isEnded) {
                $data['fcr'] = $fcr;
            }
        } else {
            // Layer flock
            $data['flock-condition'] = $isEnded ? 'layer-ended' : 'layer-active';
            $data['total_eggs'] = $totalEggs;
            $data['avg_production'] = round($avgProduction, 2) . '%';
            if ($isEnded) {
                $data['fcr'] = $fcr;
            } else {
                $data['production_rate'] = round($avgProduction, 2);
            }
        }

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('flock_retrieved_successfully'),
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
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'farm_id'               => 'required|integer|exists:farms,id',
            'chicks_supplier_id'    => 'required|integer|exists:chicks_suppliers,id',
            'breed'                 => 'required|string|max:255',
            'start_date'            => 'required|date_format:d-m-Y',
            'total_quantity'        => 'required|integer|min:1',
            'hangar_allocations'    => 'required|array|min:1',
            'hangar_allocations.*.hangar_id' => 'required|integer|exists:hangars,id',
            'hangar_allocations.*.quantity' => 'required|integer|min:0',
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

        // Validate that hangars aren't already allocated to other flocks in the same farm
        $allocatedHangars = FlockHangar::whereHas('flock', function ($q) use ($request) {
            $q->where('farm_id', $request->farm_id);
        })->pluck('hangar_id')->toArray();

        $requestHangarIds = array_column($request->hangar_allocations, 'hangar_id');
        $doubleAllocated = array_intersect($requestHangarIds, $allocatedHangars);

        if (!empty($doubleAllocated)) {
            return response()->json([
                'success' => false,
                'message' => 'One or more hangars are already allocated to another flock in this farm.',
                'already_allocated_hangar_ids' => $doubleAllocated,
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

            $flock->load('farm', 'chicksSupplier', 'creator', 'flockHangarAllocations.hangar', 'flockEnds');

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('flock_created_successfully'),
                'data'    => $this->formatFlock($flock),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create flock.',
                'error' => $e->getMessage(),
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
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        $flock = Flock::where('created_by', $user->id)
            ->whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->find($id);

        if (!$flock) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('flock_not_found'),
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
            'hangar_allocations.*.quantity' => 'required|integer|min:0',
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

        // Validate that hangars aren't already allocated to other flocks in the same farm
        // (excluding the current flock being updated)
        $allocatedHangars = FlockHangar::whereHas('flock', function ($q) use ($flock) {
            $q->where('farm_id', $flock->farm_id)
              ->where('id', '!=', $flock->id);
        })->pluck('hangar_id')->toArray();

        $requestHangarIds = array_column($request->hangar_allocations, 'hangar_id');
        $doubleAllocated = array_intersect($requestHangarIds, $allocatedHangars);

        if (!empty($doubleAllocated)) {
            return response()->json([
                'success' => false,
                'message' => 'One or more hangars are already allocated to another flock in this farm.',
                'already_allocated_hangar_ids' => $doubleAllocated,
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

            $flock->load('farm', 'chicksSupplier', 'creator', 'flockHangarAllocations.hangar', 'flockEnds');

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('flock_updated_successfully'),
                'data'    => $this->formatFlock($flock),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update flock.',
                'error' => $e->getMessage(),
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
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $user = auth()->user();

        $flock = Flock::where('created_by', $user->id)
            ->whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->find($id);

        if (!$flock) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('flock_not_found'),
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
                'message' => $this->translationService->get('flock_deleted_successfully'),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete flock.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function generateChartData($dailyRecords, $flock, $totalBird, $isLayer)
    {
        if ($dailyRecords->isEmpty()) {
            return null;
        }

        // Group records by week
        $weeklyData = [];
        foreach ($dailyRecords as $record) {
            $week = $record->record_date->weekOfYear;
            $year = $record->record_date->year;
            $key = $year . '-W' . str_pad($week, 2, '0', STR_PAD_LEFT);

            if (!isset($weeklyData[$key])) {
                $weeklyData[$key] = [
                    'week' => $week,
                    'year' => $year,
                    'records' => [],
                ];
            }
            $weeklyData[$key]['records'][] = $record;
        }

        // Calculate metrics per week
        $performanceTrend = [];
        $mortalityTrend = [];

        foreach ($weeklyData as $weekKey => $data) {
            $records = collect($data['records']);
            $week = $data['week'];

            if ($isLayer) {
                // LAYER - Avg Production, Feed Intake, Mortality
                $weekEggs = $records->sum('eggs_count');
                $weekDays = $records->count();
                $weekMortality = $records->sum('mortality');
                $weekFeed = $records->sum('feed_kg');

                $avgProd = $totalBird > 0 && $weekDays > 0 ? ($weekEggs / ($totalBird * $weekDays)) * 100 : 0;
                $mortalityPct = $totalBird > 0 ? ($weekMortality / $totalBird) * 100 : 0;

                $performanceTrend[] = [
                    'week' => 'Week ' . $week,
                    'avg_production' => round($avgProd, 2),
                    'feed_intake' => round($weekFeed, 2),
                    'mortality' => round($mortalityPct, 2),
                ];
            } else {
                // BROILER - Avg Weight, FCR, Mortality
                $weekWeight = $records->avg('chicks_weight');
                $weekMortality = $records->sum('mortality');
                $weekFeed = $records->sum('feed_kg');
                $weekDays = $records->count();

                $mortalityPct = $totalBird > 0 ? ($weekMortality / $totalBird) * 100 : 0;

                // FCR for broiler = feed / (avg_weight * number_of_birds)
                // Simplified: feed / average_weight_per_day
                $fcr = $weekWeight > 0 ? round($weekFeed / $weekWeight, 2) : 0;

                $performanceTrend[] = [
                    'week' => 'Week ' . $week,
                    'avg_weight' => round($weekWeight, 2),
                    'fcr' => $fcr,
                    'mortality' => round($mortalityPct, 2),
                ];
            }

            $mortalityTrend[] = [
                'week' => 'Week ' . $week,
                'mortality' => round($mortalityPct, 2),
            ];
        }

        return [
            'flock_type' => $isLayer ? 'Layer' : 'Broiler',
            'performance_trend' => $performanceTrend,
            'is_layer' => $isLayer,
        ];
    }

    private function formatFlock(Flock $flock): array
    {
        // Load flockHangarAllocations if not already loaded
        if (!$flock->relationLoaded('flockHangarAllocations')) {
            $flock->load('flockHangarAllocations.hangar');
        }

        // Load flockEnds if not already loaded
        if (!$flock->relationLoaded('flockEnds')) {
            $flock->load('flockEnds');
        }

        // Check if logged-in user created this flock
        $assignment = (auth()->check() && $flock->created_by === auth()->id()) ? 1 : 0;

        // Format hangar allocations with details
        $hangarAllocations = $flock->flockHangarAllocations->map(function ($allocation) {
            return [
                'hangar_id'     => $allocation->hangar_id,
                'hangar_name'   => $allocation->hangar?->name,
                'quantity'      => $allocation->quantity,
                'area_sqm'      => $allocation->hangar?->area_sqm,
            ];
        })->toArray();

        // Determine end_date and status - get latest harvest record
        $endDate = $flock->flockEnds()->latest('sale_date')->first()?->sale_date;
        $isEnded = (bool) $endDate;
        $status = $isEnded ? 'Completed' : 'Active';

        // Calculate age
        $age = $this->calculateFlockAge($flock->start_date, $endDate);

        // Determine flock type (Broiler or Layer)
        $breedType = $this->extractBreedType($flock->breed);
        $isBroiler = $breedType === 'Broiler';

        // Get total bird allocation
        $totalBird = $flock->flockHangarAllocations->sum('quantity');

        // Build base response
        $response = [
            'id'                    => $flock->id,
            'name'                  => $flock->name,
            'farm_id'               => $flock->farm_id,
            'farm_name'             => $flock->farm?->name,
            'chicks_supplier_id'    => $flock->chicks_supplier_id,
            'chicks_supplier_name'  => $flock->chicksSupplier?->name,
            'breed'                 => $flock->breed,
            'start_date'            => $flock->start_date?->format('Y-m-d'),
            'end_date'              => $endDate?->format('Y-m-d') ?? null,
            'status'                => $status,
            'age'                   => $age,
            'total_quantity'        => $flock->total_quantity,
            'hangar_allocations'    => $hangarAllocations,
            'assignment'            => $assignment,
            'created_by'            => $flock->created_by,
            'created_by_name'       => $flock->creator?->name,
            'created_at'            => $flock->created_at,
            'updated_at'            => $flock->updated_at,
        ];

        // Add flock-condition flag
        if ($isBroiler) {
            $response['flock-condition'] = $isEnded ? 'broiler-ended' : 'broiler-active';
        } else {
            $response['flock-condition'] = $isEnded ? 'layer-ended' : 'layer-active';
        }

        // Fetch daily records for conditional metrics
        $dailyRecords = DailyRecord::where('flock_id', $flock->id)->get();

        // Calculate metrics based on flock type and status
        if ($isBroiler) {
            if ($isEnded) {
                // Broiler ended: send avg FCR %
                $totalFeedKg = $dailyRecords->sum('feed_kg');
                $totalEggs = $dailyRecords->sum('eggs_count');
                $avgFcr = $totalEggs > 0 ? round($totalFeedKg / $totalEggs, 2) : 0;
                $response['avg_fcr'] = $avgFcr;
            } else {
                // Broiler active: send avg weight
                $avgWeight = $dailyRecords->avg('chicks_weight');
                $response['avg_weight'] = $avgWeight ? round($avgWeight, 2) : 0;
            }
        } else {
            if ($isEnded) {
                // Layer ended: send total eggs
                $totalEggs = $dailyRecords->sum('eggs_count');
                $response['total_eggs'] = $totalEggs;
            } else {
                // Layer active: send production rate %
                $totalEggs = $dailyRecords->sum('eggs_count');
                $recordCount = $dailyRecords->count();
                $avgProduction = $totalBird > 0 && $recordCount > 0 ? ($totalEggs / ($totalBird * $recordCount)) * 100 : 0;
                $response['production_rate'] = round($avgProduction, 2);
            }
        }

        return $response;
    }
}
