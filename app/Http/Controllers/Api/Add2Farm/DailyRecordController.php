<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\DailyRecord;
use App\Models\Flock;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

/**
 * @group Add2Farm Daily Records
 * CRUD APIs for managing daily records for flocks and hangars in Add2Farm
 */
class DailyRecordController extends BaseController
{
    /**
     * List all daily records
     *
     * Get paginated list of all daily records with search and filtering.
     *
     * @authenticated
     * @queryParam page integer optional Pagination page number. Example: 1
     * @queryParam per_page integer optional Items per page. Default: 15. Example: 20
     * @queryParam flock_id integer optional Filter by flock ID. Example: 1
     * @queryParam farm_id integer optional Filter by farm ID. Example: 1
     * @queryParam record_date string optional Filter by record date (format: yyyy-mm-dd). Example: 2026-08-07
     * @queryParam hangar_id integer optional Filter by hangar ID. Example: 1
     * @queryParam type string optional Grouping type: day, week, month. Default: day. Example: week
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Daily records retrieved successfully.",
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 17,
     *         "period": "Wednesday, 07 Aug 2026",
     *         "period_date": "2026-08-07",
     *         "farm_id": 1,
     *         "farm_name": "Main Farm",
     *         "flock_id": 1,
     *         "flock_name": "Farm1-Flock4",
     *         "flock_age": "Week 1 Day 5",
     *         "flock_status": "Active",
     *         "feed_qty": 931.25,
     *         "eggs_tray_30": 25,
     *         "eggs_count": 750,
     *         "eggs_weight": 38.25,
     *         "mortality": 5,
     *         "hangars": [
     *           {
     *             "id": 17,
     *             "hangar_id": 1,
     *             "hangar_name": "Farm1-Hangar1",
     *             "status": "Active",
     *             "feed_qty": 450.50,
     *             "remaining_qty": 250.75,
     *             "eggs_tray_30": 12,
     *             "eggs_count": 360,
     *             "eggs_weight": 18.50,
     *             "mortality": 3,
     *             "notes": "Good production"
     *           }
     *         ]
     *       }
     *     ],
     *     "total": 50,
     *     "last_page": 3
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

        $type = $request->get('type', 'day');

        if (!in_array($type, ['day', 'week', 'month'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid type parameter. Supported values: day, week, month',
            ], 422);
        }

        if ($type === 'day') {
            return $this->indexByDay($request);
        } elseif ($type === 'week') {
            return $this->indexByWeek($request);
        } else {
            return $this->indexByMonth($request);
        }
    }

    private function indexByDay(Request $request)
    {
        $query = DailyRecord::where('created_by', auth()->id())
            ->when($request->flock_id, function ($q) use ($request) {
                return $q->where('flock_id', $request->flock_id);
            })
            ->when($request->farm_id, function ($q) use ($request) {
                return $q->where('farm_id', $request->farm_id);
            })
            ->when($request->record_date, function ($q) use ($request) {
                return $q->where('record_date', $request->record_date);
            })
            ->when($request->hangar_id, function ($q) use ($request) {
                return $q->where('hangar_id', $request->hangar_id);
            })
            ->with('farm', 'flock', 'flock.flockEnds', 'hangar', 'creator');

        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        $allRecords = $query->orderBy('record_date', 'DESC')->get();

        // Group by date, farm, and flock
        $groupedByDate = $allRecords->groupBy(function ($record) {
            return $record->record_date->format('Y-m-d') . '|' . $record->farm_id . '|' . $record->flock_id;
        })->map(function ($dateGroup) {
            $firstRecord = $dateGroup->first();
            return [
                'record_date' => $firstRecord->record_date,
                'farm_id' => $firstRecord->farm_id,
                'flock_id' => $firstRecord->flock_id,
                'records' => $dateGroup
            ];
        })->values();

        // Paginate the grouped results
        $skip = ($page - 1) * $perPage;
        $paginatedItems = $groupedByDate->slice($skip, $perPage)->values();

        $formattedItems = $paginatedItems->map(function ($groupedRecord) {
            return $this->formatDailyAggregateRecordWithHangars($groupedRecord);
        });

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $formattedItems,
            $groupedByDate->count(),
            $perPage,
            $page,
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('daily_records_retrieved_successfully'),
            'data' => $paginatedData,
        ]);
    }

    private function indexByWeek(Request $request)
    {
        $baseQuery = DailyRecord::where('created_by', auth()->id())
            ->when($request->flock_id, function ($q) use ($request) {
                return $q->where('flock_id', $request->flock_id);
            })
            ->when($request->farm_id, function ($q) use ($request) {
                return $q->where('farm_id', $request->farm_id);
            })
            ->when($request->record_date, function ($q) use ($request) {
                return $q->where('record_date', $request->record_date);
            })
            ->when($request->hangar_id, function ($q) use ($request) {
                return $q->where('hangar_id', $request->hangar_id);
            });

        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        // Get grouped data with hangars
        $allRecords = $baseQuery->with('farm', 'flock', 'hangar', 'creator')->get();

        // Group by week and farm/flock
        $groupedByWeek = $allRecords->groupBy(function ($record) {
            return $record->record_date->format('Y-W') . '|' . $record->farm_id . '|' . $record->flock_id;
        })->map(function ($weekGroup) {
            $firstRecord = $weekGroup->first();
            $weekNumber = $firstRecord->record_date->format('W');
            $year = $firstRecord->record_date->format('Y');
            return [
                'year' => $year,
                'week' => $weekNumber,
                'period_date' => $firstRecord->record_date->startOfWeek(),
                'farm_id' => $firstRecord->farm_id,
                'flock_id' => $firstRecord->flock_id,
                'records' => $weekGroup
            ];
        })->values();

        // Paginate manually
        $total = $groupedByWeek->count();
        $skip = ($page - 1) * $perPage;
        $paginatedResults = $groupedByWeek->slice($skip, $perPage)->values();

        $formattedResults = $paginatedResults->map(function ($groupedRecord) {
            return $this->formatWeeklyRecordWithHangars($groupedRecord);
        });

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $formattedResults,
            $total,
            $perPage,
            $page,
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('daily_records_retrieved_successfully'),
            'data' => $paginatedData,
        ]);
    }

    private function indexByMonth(Request $request)
    {
        $baseQuery = DailyRecord::where('created_by', auth()->id())
            ->when($request->flock_id, function ($q) use ($request) {
                return $q->where('flock_id', $request->flock_id);
            })
            ->when($request->farm_id, function ($q) use ($request) {
                return $q->where('farm_id', $request->farm_id);
            })
            ->when($request->record_date, function ($q) use ($request) {
                return $q->where('record_date', $request->record_date);
            })
            ->when($request->hangar_id, function ($q) use ($request) {
                return $q->where('hangar_id', $request->hangar_id);
            });

        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        // Get grouped data with hangars
        $allRecords = $baseQuery->with('farm', 'flock', 'hangar', 'creator')->get();

        // Group by month and farm/flock
        $groupedByMonth = $allRecords->groupBy(function ($record) {
            return $record->record_date->format('Y-m') . '|' . $record->farm_id . '|' . $record->flock_id;
        })->map(function ($monthGroup) {
            $firstRecord = $monthGroup->first();
            return [
                'year' => $firstRecord->record_date->format('Y'),
                'month' => $firstRecord->record_date->format('m'),
                'period_date' => $firstRecord->record_date->startOfMonth(),
                'farm_id' => $firstRecord->farm_id,
                'flock_id' => $firstRecord->flock_id,
                'records' => $monthGroup
            ];
        })->values();

        // Paginate manually
        $total = $groupedByMonth->count();
        $skip = ($page - 1) * $perPage;
        $paginatedResults = $groupedByMonth->slice($skip, $perPage)->values();

        $formattedResults = $paginatedResults->map(function ($groupedRecord) {
            return $this->formatMonthlyRecordWithHangars($groupedRecord);
        });

        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            $formattedResults,
            $total,
            $perPage,
            $page,
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('daily_records_retrieved_successfully'),
            'data' => $paginatedData,
        ]);
    }

    /**
     * Get a single daily record
     *
     * Retrieve detailed information of a specific daily record with all hangars for that day.
     *
     * @authenticated
     * @urlParam id integer required The daily record ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Daily record retrieved successfully.",
     *   "data": {
     *     "id": 17,
     *     "record_date": "2026-08-07",
     *     "period": "Wednesday, 07 Aug 2026",
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
     *     "flock_id": 1,
     *     "flock_name": "Farm1-Flock4",
     *     "flock_age": "Week 1 Day 5",
     *     "flock_status": "Active",
     *     "breed": "Layer,Lohmann Brown",
     *     "total_feed_qty": 931.25,
     *     "total_remaining_qty": 500.00,
     *     "total_mortality": 5,
     *     "hangars": [
     *       {
     *         "hangar_id": 1,
     *         "hangar_name": "Farm1-Hangar1",
     *         "status": "Active",
     *         "feed_qty": 450.50,
     *         "remaining_qty": 250.75,
     *         "eggs_tray_30": 12,
     *         "eggs_count": 360,
     *         "eggs_weight": 18.50,
     *         "mortality": 3,
     *         "notes": "Good production"
     *       },
     *       {
     *         "hangar_id": 2,
     *         "hangar_name": "Farm1-Hangar2",
     *         "status": "Active",
     *         "feed_qty": 480.75,
     *         "remaining_qty": 249.25,
     *         "eggs_tray_30": 13,
     *         "eggs_count": 390,
     *         "eggs_weight": 19.75,
     *         "mortality": 2,
     *         "notes": "Excellent performance"
     *       }
     *     ],
     *     "recorded_by": "Admin Name",
     *     "created_by": 1,
     *     "created_by_name": "Admin Name",
     *     "created_at": "2026-08-07T10:30:00Z",
     *     "hangars": [
     *       {
     *         "hangar_id": 1,
     *         "hangar_name": "Hangar 1",
     *         "feed_kg": 450.50,
     *         "eggs_tray_30": 12,
     *         "eggs_count": 360,
     *         "eggs_weight": 18.50,
     *         "mortality": 5,
     *         "notes": "Good production"
     *       }
     *     ]
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Daily record not found."
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

        $record = DailyRecord::where('created_by', auth()->id())
            ->with('farm', 'flock', 'flock.flockEnds', 'hangar', 'creator')
            ->find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('daily_record_not_found'),
            ], 404);
        }

        // Fetch all records for this date, flock, and farm
        $allRecordsForDate = DailyRecord::where('created_by', auth()->id())
            ->where('record_date', $record->record_date)
            ->where('flock_id', $record->flock_id)
            ->with('farm', 'flock', 'flock.flockEnds', 'hangar', 'creator')
            ->get();

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('daily_record_retrieved_successfully'),
            'data' => $this->formatDailyRecordDetail($allRecordsForDate),
        ]);
    }

    /**
     * Create new daily records
     *
     * Create daily records for multiple hangars under the same flock and date.
     *
     * @authenticated
     * @bodyParam record_date date required Record date (format: dd-mm-yyyy). Example: 07-08-2026
     * @bodyParam flock_id integer required Flock ID. Example: 1
     * @bodyParam hangars array required Array of hangar records. Example: [{"hangar_id": 1, "feed_kg": 450.50, "mortality": 5, "eggs_tray_30": 12, "eggs_count": 360, "eggs_weight": 18.50}]
     * @bodyParam hangars[].hangar_id integer required Hangar ID
     * @bodyParam hangars[].feed_kg number required Feed quantity in kg
     * @bodyParam hangars[].mortality integer required Mortality count
     * @bodyParam hangars[].eggs_tray_30 integer optional Number of egg trays (30 count, for layer flocks)
     * @bodyParam hangars[].eggs_count integer optional Egg count (for layer flocks)
     * @bodyParam hangars[].eggs_weight number optional Eggs weight in kg (for layer flocks)
     * @bodyParam hangars[].chicks_weight number optional Chicks weight in kg (for broiler flocks)
     * @bodyParam hangars[].notes string optional Notes for this hangar record
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Daily records created successfully.",
     *   "data": {
     *     "id": 17,
     *     "period": "Monday, 07 Aug 2026",
     *     "period_date": "2026-08-07",
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
     *     "flock_id": 1,
     *     "flock_name": "Farm1-Flock4",
     *     "flock_age": "Week 1 Day 2",
     *     "flock_status": "Active",
     *     "feed_qty": 931.25,
     *     "eggs_tray_30": 25,
     *     "eggs_count": 750,
     *     "eggs_weight": 38.25,
     *     "chicks_weight": 0.00,
     *     "mortality": 5,
     *     "hangars": [
     *       {
     *         "id": 17,
     *         "hangar_id": 1,
     *         "hangar_name": "Farm1-Hangar1",
     *         "status": "Active",
     *         "feed_qty": 450.50,
     *         "remaining_qty": 250.75,
     *         "eggs_tray_30": 12,
     *         "eggs_count": 360,
     *         "eggs_weight": 18.50,
     *         "mortality": 3,
     *         "notes": "Good production"
     *       }
     *     ]
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "hangars": ["The hangars field is required."]
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

        $flock = Flock::find($request->flock_id);
        if (!$flock) {
            return response()->json([
                'success' => false,
                'errors' => ['flock_id' => ['The selected flock id is invalid.']],
            ], 422);
        }

        $breedType = $this->extractBreedType($flock->breed);

        $rules = [
            'record_date'    => 'required|date_format:d-m-Y',
            'flock_id'       => 'required|integer|exists:flocks,id',
            'hangars'        => 'required|array|min:1',
            'hangars.*.hangar_id' => 'required|integer|exists:hangars,id',
            'hangars.*.feed_kg' => 'required|numeric|min:0',
            'hangars.*.mortality' => 'required|integer|min:0',
            'hangars.*.notes' => 'nullable|string|max:1000',
        ];

        if ($breedType === 'Layer') {
            $rules['hangars.*.eggs_tray_30'] = 'required|integer|min:0';
            $rules['hangars.*.eggs_count'] = 'required|integer|min:0';
            $rules['hangars.*.eggs_weight'] = 'required|numeric|min:0';
            $rules['hangars.*.chicks_weight'] = 'nullable|numeric|min:0';
        } else {
            $rules['hangars.*.eggs_tray_30'] = 'nullable|integer|min:0';
            $rules['hangars.*.eggs_count'] = 'nullable|integer|min:0';
            $rules['hangars.*.eggs_weight'] = 'nullable|numeric|min:0';
            $rules['hangars.*.chicks_weight'] = 'nullable|numeric|min:0';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            $recordDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->record_date);
            $records = [];

            foreach ($request->hangars as $hangarData) {
                $record = DailyRecord::create([
                    'record_date'   => $recordDate,
                    'farm_id'       => $flock->farm_id,
                    'flock_id'      => $request->flock_id,
                    'hangar_id'     => $hangarData['hangar_id'],
                    'feed_kg'       => $hangarData['feed_kg'],
                    'eggs_tray_30'  => $hangarData['eggs_tray_30'] ?? 0,
                    'eggs_count'    => $hangarData['eggs_count'] ?? 0,
                    'eggs_weight'   => $hangarData['eggs_weight'] ?? 0,
                    'chicks_weight' => $hangarData['chicks_weight'] ?? 0,
                    'mortality'     => $hangarData['mortality'] ?? 0,
                    'notes'         => $hangarData['notes'] ?? null,
                    'created_by'    => auth()->id(),
                ]);
                $records[] = $record;
            }

            DB::commit();

            // Load all relations for the records
            foreach ($records as $record) {
                $record->load('farm', 'flock', 'hangar', 'creator');
            }

            // Format and group records for aggregated response
            $formattedData = $this->formatAndGroupDailyRecords(collect($records));

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('daily_record_created_successfully'),
                'data'    => $formattedData,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Daily record creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create daily record.',
            ], 500);
        }
    }

    /**
     * Update daily records for a date
     *
     * Update existing daily records for multiple hangars on the same date and flock.
     * All records for that date/farm/flock will be replaced with the new data.
     *
     * @authenticated
     * @urlParam id integer required The daily record ID (any record from the date to update). Example: 21
     * @bodyParam record_date date required Record date (format: dd-mm-yyyy). Example: 14-09-2026
     * @bodyParam flock_id integer required Flock ID. Example: 47
     * @bodyParam hangars array required Array of hangar records. Example: [{"hangar_id": 287, "feed_kg": 450.50, "mortality": 3, "eggs_tray_30": 12, "eggs_count": 360, "eggs_weight": 18.50}]
     * @bodyParam hangars[].hangar_id integer required Hangar ID
     * @bodyParam hangars[].feed_kg number required Feed quantity in kg
     * @bodyParam hangars[].mortality integer required Mortality count
     * @bodyParam hangars[].eggs_tray_30 integer optional Number of egg trays (30 count, for layer flocks)
     * @bodyParam hangars[].eggs_count integer optional Egg count (for layer flocks)
     * @bodyParam hangars[].eggs_weight number optional Eggs weight in kg (for layer flocks)
     * @bodyParam hangars[].chicks_weight number optional Chicks weight in kg (for broiler flocks)
     * @bodyParam hangars[].notes string optional Notes for this hangar record
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Daily record updated successfully.",
     *   "data": {
     *     "id": 21,
     *     "period": "Monday, 14 Sep 2026",
     *     "period_date": "2026-09-14",
     *     "farm_id": 70,
     *     "farm_name": "James Owner Farm 1",
     *     "flock_id": 47,
     *     "flock_name": "Flock1",
     *     "flock_age": "Day 4",
     *     "flock_status": "Completed",
     *     "feed_qty": 931.25,
     *     "eggs_tray_30": 25,
     *     "eggs_count": 750,
     *     "eggs_weight": 38.25,
     *     "chicks_weight": 0.00,
     *     "mortality": 5,
     *     "hangars": [
     *       {
     *         "id": 21,
     *         "hangar_id": 287,
     *         "hangar_name": "Hangar 1",
     *         "status": "Active",
     *         "feed_qty": 450.50,
     *         "remaining_qty": 0.00,
     *         "eggs_tray_30": 12,
     *         "eggs_count": 360,
     *         "eggs_weight": 18.50,
     *         "mortality": 3,
     *         "notes": "Good production day for Hangar 1"
     *       },
     *       {
     *         "id": 22,
     *         "hangar_id": 288,
     *         "hangar_name": "Hangar 2",
     *         "status": "Active",
     *         "feed_qty": 480.75,
     *         "remaining_qty": 0.00,
     *         "eggs_tray_30": 13,
     *         "eggs_count": 390,
     *         "eggs_weight": 19.75,
     *         "mortality": 2
     *       }
     *     ]
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Daily record not found."
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "hangars.0.feed_kg": ["The hangars.0.feed kg field is required."]
     *   }
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

        $record = DailyRecord::where('created_by', auth()->id())->find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('daily_record_not_found'),
            ], 404);
        }

        $flock = Flock::findOrFail($record->flock_id);
        $breedType = $this->extractBreedType($flock->breed);

        $rules = [
            'record_date'    => 'required|date_format:d-m-Y',
            'flock_id'       => 'required|integer|exists:flocks,id',
            'hangars'        => 'required|array|min:1',
            'hangars.*.hangar_id' => 'required|integer|exists:hangars,id',
            'hangars.*.feed_kg' => 'required|numeric|min:0',
            'hangars.*.mortality' => 'required|integer|min:0',
            'hangars.*.notes' => 'nullable|string|max:1000',
        ];

        if ($breedType === 'Layer') {
            $rules['hangars.*.eggs_tray_30'] = 'required|integer|min:0';
            $rules['hangars.*.eggs_count'] = 'required|integer|min:0';
            $rules['hangars.*.eggs_weight'] = 'required|numeric|min:0';
            $rules['hangars.*.chicks_weight'] = 'nullable|numeric|min:0';
        } else {
            $rules['hangars.*.eggs_tray_30'] = 'nullable|integer|min:0';
            $rules['hangars.*.eggs_count'] = 'nullable|integer|min:0';
            $rules['hangars.*.eggs_weight'] = 'nullable|numeric|min:0';
            $rules['hangars.*.chicks_weight'] = 'nullable|numeric|min:0';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Convert date format from dd-mm-yyyy to yyyy-mm-dd
            $recordDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->record_date);

            // Get the farm_id from the flock
            $flock = Flock::findOrFail($request->flock_id);

            // Delete all existing records for this date, flock, and farm
            // Use the old record's date if it exists, to ensure proper deletion
            $oldDate = $record->record_date;
            DailyRecord::where('record_date', $oldDate)
                ->where('farm_id', $flock->farm_id)
                ->where('flock_id', $request->flock_id)
                ->where('created_by', auth()->id())
                ->delete();

            $records = [];

            // Create new records for all hangars
            foreach ($request->hangars as $hangarData) {
                $newRecord = DailyRecord::create([
                    'record_date'   => $recordDate,
                    'farm_id'       => $flock->farm_id,
                    'flock_id'      => $request->flock_id,
                    'hangar_id'     => $hangarData['hangar_id'],
                    'feed_kg'       => $hangarData['feed_kg'],
                    'eggs_tray_30'  => $hangarData['eggs_tray_30'] ?? 0,
                    'eggs_count'    => $hangarData['eggs_count'] ?? 0,
                    'eggs_weight'   => $hangarData['eggs_weight'] ?? 0,
                    'chicks_weight' => $hangarData['chicks_weight'] ?? 0,
                    'mortality'     => $hangarData['mortality'] ?? 0,
                    'notes'         => $hangarData['notes'] ?? null,
                    'created_by'    => auth()->id(),
                ]);
                $records[] = $newRecord;
            }

            DB::commit();

            // Load all relations for the records
            foreach ($records as $record) {
                $record->load('farm', 'flock', 'hangar', 'creator');
            }

            // Format and group records for aggregated response
            $formattedData = $this->formatAndGroupDailyRecords(collect($records));

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('daily_record_updated_successfully'),
                'data'    => $formattedData,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Daily record update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update daily record.',
            ], 500);
        }
    }

    /**
     * Delete daily records for a date
     *
     * Delete daily records for the specified date, farm, and flock.
     * The `id` parameter is the main record ID from the grouped response (the first hangar's ID).
     * - Without hangar_id: Deletes ALL hangars for that date
     * - With hangar_id: Deletes only that specific hangar's record for that date
     *
     * @authenticated
     * @urlParam id integer required The main daily record ID (from grouped response). Example: 17
     * @queryParam hangar_id integer optional Hangar ID to delete only one hangar's record. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Daily record deleted successfully.",
     *   "deleted_count": 2
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Daily record not found."
     * }
     */
    public function destroy(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $record = DailyRecord::where('created_by', auth()->id())->find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('daily_record_not_found'),
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Check if hangar_id is provided to delete only one hangar's record
            if ($request->query('hangar_id')) {
                // Delete only the specific hangar's record for this date
                $deletedCount = DailyRecord::where('record_date', $record->record_date)
                    ->where('farm_id', $record->farm_id)
                    ->where('flock_id', $record->flock_id)
                    ->where('hangar_id', $request->query('hangar_id'))
                    ->where('created_by', auth()->id())
                    ->delete();
            } else {
                // Delete all records for this date, farm, and flock (all hangars for that day)
                $deletedCount = DailyRecord::where('record_date', $record->record_date)
                    ->where('farm_id', $record->farm_id)
                    ->where('flock_id', $record->flock_id)
                    ->where('created_by', auth()->id())
                    ->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('daily_record_deleted_successfully'),
                'deleted_count' => $deletedCount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Daily record deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete daily record.',
            ], 500);
        }
    }

    private function formatDailyRecord(DailyRecord $record): array
    {
        // Check if logged-in user created this record
        $assignment = (auth()->check() && $record->created_by === auth()->id()) ? 1 : 0;

        // Calculate flock age if flock exists
        $flockAge = null;
        $flockStatus = null;
        if ($record->flock) {
            $endDate = $record->flock->flockEnds()->latest('sale_date')->first()?->sale_date;
            $breedType = $this->extractBreedType($record->flock->breed);
            $flockAge = $this->calculateFlockAge($record->flock->start_date, $endDate, $breedType);
            $flockStatus = $endDate ? 'Completed' : 'Active';
        }

        $data = [
            'id'              => $record->id,
            'record_date'     => $record->record_date?->format('Y-m-d'),
            'farm_id'         => $record->farm_id,
            'farm_name'       => $record->farm?->name,
            'flock_id'        => $record->flock_id,
            'flock_name'      => $record->flock?->name,
            'flock_age'       => $flockAge,
            'flock_status'    => $flockStatus,
            'hangar_id'       => $record->hangar_id,
            'hangar_name'     => $record->hangar?->name,
            'feed_kg'         => $this->formatDecimal($record->feed_kg),
            'eggs_tray_30'    => (int) $record->eggs_tray_30,
            'eggs_count'      => (int) $record->eggs_count,
            'eggs_weight'     => $this->formatDecimal($record->eggs_weight),
            'chicks_weight'   => $this->formatDecimal($record->chicks_weight),
            'mortality'       => (int) $record->mortality,
            'assignment'      => $assignment,
            'created_by'      => $record->created_by,
            'created_by_name' => $record->creator?->name,
            'created_at'      => $record->created_at,
        ];

        // Add notes if present
        if ($record->notes) {
            $data['notes'] = $record->notes;
        }

        return $data;
    }

    private function formatDailyAggregateRecord($record): array
    {
        $farm = Farm::find($record->farm_id);
        $flock = Flock::with('flockEnds')->find($record->flock_id);

        $periodDate = \Carbon\Carbon::parse($record->period_date);
        $dateLabel = $periodDate->format('l, d M Y');

        // Calculate flock age if flock exists
        $flockAge = null;
        $flockStatus = null;
        if ($flock) {
            $endDate = $flock->flockEnds()->latest('sale_date')->first()?->sale_date;
            $breedType = $this->extractBreedType($flock->breed);
            $flockAge = $this->calculateFlockAge($flock->start_date, $endDate, $breedType);
            $flockStatus = $endDate ? 'Completed' : 'Active';
        }

        return [
            'period'          => $dateLabel,
            'period_date'     => $record->period_date,
            'farm_id'         => $record->farm_id,
            'farm_name'       => $farm?->name,
            'flock_id'        => $record->flock_id,
            'flock_name'      => $flock?->name,
            'flock_age'       => $flockAge,
            'flock_status'    => $flockStatus,
            'feed_kg'         => $this->formatDecimal($record->feed_kg),
            'eggs_tray_30'    => (int) $record->eggs_tray_30,
            'eggs_count'      => (int) $record->eggs_count,
            'eggs_weight'     => $this->formatDecimal($record->eggs_weight),
            'chicks_weight'   => $this->formatDecimal($record->chicks_weight),
            'mortality'       => (int) $record->mortality,
        ];
    }

    private function formatWeeklyRecord($record): array
    {
        $farm = Farm::find($record->farm_id);
        $flock = Flock::with('flockEnds')->find($record->flock_id);

        $periodDate = \Carbon\Carbon::parse($record->period_date);
        $weekLabel = 'Week ' . $record->week . ' • ' . $periodDate->format('F Y');

        // Calculate flock age if flock exists
        $flockAge = null;
        $flockStatus = null;
        if ($flock) {
            $endDate = $flock->flockEnds()->latest('sale_date')->first()?->sale_date;
            $breedType = $this->extractBreedType($flock->breed);
            $flockAge = $this->calculateFlockAge($flock->start_date, $endDate, $breedType);
            $flockStatus = $endDate ? 'Completed' : 'Active';
        }

        // Get remaining quantities from latest MaterialStockHangar records for this farm
        $farmHangars = \App\Models\Hangar::where('farm_id', $record->farm_id)->pluck('id');
        $remainingQty = \App\Models\MaterialStockHangar::whereIn('hangar_id', $farmHangars)
            ->latest('created_at')
            ->first()?->remaining_quantity ?? 0;

        return [
            'period'          => $weekLabel,
            'year'            => $record->year,
            'week'            => $record->week,
            'period_date'     => $record->period_date,
            'farm_id'         => $record->farm_id,
            'farm_name'       => $farm?->name,
            'flock_id'        => $record->flock_id,
            'flock_name'      => $flock?->name,
            'flock_age'       => $flockAge,
            'flock_status'    => $flockStatus,
            'feed_qty'        => $this->formatDecimal($record->feed_kg),
            'remaining_qty'   => $this->formatDecimal($remainingQty),
            'eggs_tray_30'    => (int) $record->eggs_tray_30,
            'eggs_count'      => (int) $record->eggs_count,
            'eggs_weight'     => $this->formatDecimal($record->eggs_weight),
            'chicks_weight'   => $this->formatDecimal($record->chicks_weight),
            'mortality'       => (int) $record->mortality,
        ];
    }

    private function formatMonthlyRecord($record): array
    {
        $farm = Farm::find($record->farm_id);
        $flock = Flock::with('flockEnds')->find($record->flock_id);

        $periodDate = \Carbon\Carbon::parse($record->period_date);
        $monthLabel = $periodDate->format('F Y');

        // Calculate flock age if flock exists
        $flockAge = null;
        $flockStatus = null;
        if ($flock) {
            $endDate = $flock->flockEnds()->latest('sale_date')->first()?->sale_date;
            $breedType = $this->extractBreedType($flock->breed);
            $flockAge = $this->calculateFlockAge($flock->start_date, $endDate, $breedType);
            $flockStatus = $endDate ? 'Completed' : 'Active';
        }

        // Get remaining quantities from latest MaterialStockHangar records for this farm
        $farmHangars = \App\Models\Hangar::where('farm_id', $record->farm_id)->pluck('id');
        $remainingQty = \App\Models\MaterialStockHangar::whereIn('hangar_id', $farmHangars)
            ->latest('created_at')
            ->first()?->remaining_quantity ?? 0;

        return [
            'period'          => $monthLabel,
            'year'            => $record->year,
            'month'           => $record->month,
            'period_date'     => $record->period_date,
            'farm_id'         => $record->farm_id,
            'farm_name'       => $farm?->name,
            'flock_id'        => $record->flock_id,
            'flock_name'      => $flock?->name,
            'flock_age'       => $flockAge,
            'flock_status'    => $flockStatus,
            'feed_qty'        => $this->formatDecimal($record->feed_kg),
            'remaining_qty'   => $this->formatDecimal($remainingQty),
            'eggs_tray_30'    => (int) $record->eggs_tray_30,
            'eggs_count'      => (int) $record->eggs_count,
            'eggs_weight'     => $this->formatDecimal($record->eggs_weight),
            'chicks_weight'   => $this->formatDecimal($record->chicks_weight),
            'mortality'       => (int) $record->mortality,
        ];
    }

    private function formatDailyAggregateRecordWithHangars($groupedRecord): array
    {
        $records = $groupedRecord['records'];
        $firstRecord = $records->first();
        $farm = $firstRecord->farm;
        $flock = $firstRecord->flock;

        $periodDate = $groupedRecord['record_date'];
        $dateLabel = $periodDate->format('l, d M Y');

        // Determine breed type for hangar field filtering
        $breedType = $this->extractBreedType($flock->breed ?? '');
        $isBroiler = $breedType === 'Broiler';

        // Calculate flock age if flock exists
        $flockAge = null;
        $flockStatus = null;
        if ($flock) {
            $endDate = $flock->flockEnds()->latest('sale_date')->first()?->sale_date;
            $flockAge = $this->calculateFlockAge($flock->start_date, $endDate, $breedType);
            $flockStatus = $endDate ? 'Completed' : 'Active';
        }

        // Group records by hangar and format hangar details
        $hangars = $records->groupBy('hangar_id')->map(function ($hangarRecords) use ($isBroiler) {
            $firstHangarRecord = $hangarRecords->first();
            $totalFeed = $hangarRecords->sum('feed_kg');
            $totalEggsTray = $hangarRecords->sum('eggs_tray_30');
            $totalEggs = $hangarRecords->sum('eggs_count');
            $totalEggsWeight = $hangarRecords->sum('eggs_weight');
            $totalChicksWeight = $hangarRecords->sum('chicks_weight');
            $totalMortality = $hangarRecords->sum('mortality');

            // Get remaining quantity from latest MaterialStockHangar record
            $materialStockHangar = \App\Models\MaterialStockHangar::where('hangar_id', $firstHangarRecord->hangar_id)
                ->latest('created_at')
                ->first();
            $remainingQty = $materialStockHangar?->remaining_quantity ?? 0;

            $hangarData = [
                'id' => $firstHangarRecord->id,
                'hangar_id' => $firstHangarRecord->hangar_id,
                'hangar_name' => $firstHangarRecord->hangar?->name,
                'status' => $firstHangarRecord->hangar?->status,
                'feed_qty' => $this->formatDecimal($totalFeed),
                'remaining_qty' => $this->formatDecimal($remainingQty),
                'mortality' => (int) $totalMortality,
            ];

            // Add breed-specific fields
            if ($isBroiler) {
                $hangarData['chicks_weight'] = $this->formatDecimal($totalChicksWeight);
            } else {
                $hangarData['eggs_tray_30'] = (int) $totalEggsTray;
                $hangarData['eggs_count'] = (int) $totalEggs;
                $hangarData['eggs_weight'] = $this->formatDecimal($totalEggsWeight);
            }

            // Add notes if present
            if ($firstHangarRecord->notes) {
                $hangarData['notes'] = $firstHangarRecord->notes;
            }

            return $hangarData;
        })->values();

        $totalFeed = $records->sum('feed_kg');
        $totalEggsTray = $records->sum('eggs_tray_30');
        $totalEggs = $records->sum('eggs_count');
        $totalEggsWeight = $records->sum('eggs_weight');
        $totalChicksWeight = $records->sum('chicks_weight');
        $totalMortality = $records->sum('mortality');

        return [
            'id'              => $firstRecord->id,
            'period'          => $dateLabel,
            'period_date'     => $periodDate->format('Y-m-d'),
            'farm_id'         => $groupedRecord['farm_id'],
            'farm_name'       => $farm?->name,
            'flock_id'        => $groupedRecord['flock_id'],
            'flock_name'      => $flock?->name,
            'flock_age'       => $flockAge,
            'flock_status'    => $flockStatus,
            'feed_qty'        => $this->formatDecimal($totalFeed),
            'eggs_tray_30'    => (int) $totalEggsTray,
            'eggs_count'      => (int) $totalEggs,
            'eggs_weight'     => $this->formatDecimal($totalEggsWeight),
            'chicks_weight'   => $this->formatDecimal($totalChicksWeight),
            'mortality'       => (int) $totalMortality,
            'hangars'         => $hangars,
        ];
    }

    private function formatDailyRecordDetail($records): array
    {
        if ($records->isEmpty()) {
            return [];
        }

        $firstRecord = $records->first();
        $farm = $firstRecord->farm;
        $flock = $firstRecord->flock;

        // Determine breed type
        $breedType = $this->extractBreedType($flock->breed ?? '');
        $isBroiler = $breedType === 'Broiler';

        // Calculate flock age if flock exists
        $flockAge = null;
        $flockStatus = null;
        if ($flock) {
            $endDate = $flock->flockEnds()->latest('sale_date')->first()?->sale_date;
            $flockAge = $this->calculateFlockAge($flock->start_date, $endDate, $breedType);
            $flockStatus = $endDate ? 'Completed' : 'Active';
        }

        // Calculate totals
        $totalFeed = $records->sum('feed_kg');
        $totalMortality = $records->sum('mortality');

        // Get remaining quantities from latest MaterialStockHangar records for this farm
        $farmHangars = \App\Models\Hangar::where('farm_id', $firstRecord->farm_id)->pluck('id');
        $totalRemainingQty = \App\Models\MaterialStockHangar::whereIn('hangar_id', $farmHangars)
            ->latest('created_at')
            ->first()?->remaining_quantity ?? 0;

        // Format hangar details - include only breed-specific fields
        $hangars = $records->map(function ($record) use ($isBroiler) {
            // Get remaining quantity for this specific hangar
            $materialStockHangar = \App\Models\MaterialStockHangar::where('hangar_id', $record->hangar_id)
                ->latest('created_at')
                ->first();
            $remainingQty = $materialStockHangar?->remaining_quantity ?? 0;

            $hangarData = [
                'id' => $record->id,
                'hangar_id' => $record->hangar_id,
                'hangar_name' => $record->hangar?->name,
                'status' => $record->hangar?->status,
                'feed_qty' => $this->formatDecimal($record->feed_kg),
                'remaining_qty' => $this->formatDecimal($remainingQty),
                'mortality' => (int) $record->mortality,
            ];

            // Add breed-specific fields
            if ($isBroiler) {
                $hangarData['chicks_weight'] = $this->formatDecimal($record->chicks_weight);
            } else {
                $hangarData['eggs_tray_30'] = (int) $record->eggs_tray_30;
                $hangarData['eggs_count'] = (int) $record->eggs_count;
                $hangarData['eggs_weight'] = $this->formatDecimal($record->eggs_weight);
            }

            // Add notes if present
            if ($record->notes) {
                $hangarData['notes'] = $record->notes;
            }

            return $hangarData;
        })->values();

        return [
            'id' => $firstRecord->id,
            'record_date' => $firstRecord->record_date->format('Y-m-d'),
            'period' => $firstRecord->record_date->format('l, d M Y'),
            'farm_id' => $firstRecord->farm_id,
            'farm_name' => $farm?->name,
            'flock_id' => $firstRecord->flock_id,
            'flock_name' => $flock?->name,
            'flock_age' => $flockAge,
            'flock_status' => $flockStatus,
            'breed' => $flock->breed,
            'total_feed_qty' => $this->formatDecimal($totalFeed),
            'total_remaining_qty' => $this->formatDecimal($totalRemainingQty),
            'total_mortality' => (int) $totalMortality,
            'recorded_by' => $firstRecord->creator?->name,
            'created_at' => $firstRecord->created_at,
            'hangars' => $hangars,
        ];
    }

    /**
     * Format and group daily records for aggregated response
     *
     * Takes a collection of daily records and returns a single aggregated response
     * grouped by date, farm, and flock with nested hangar details.
     *
     * @param \Illuminate\Database\Eloquent\Collection $records
     * @return array|null Formatted aggregated response or null if records empty
     */
    private function formatWeeklyRecordWithHangars($groupedRecord): array
    {
        $records = $groupedRecord['records'];
        $firstRecord = $records->first();
        $farm = $firstRecord->farm;
        $flock = $firstRecord->flock;

        $periodDate = $groupedRecord['period_date'];
        $weekLabel = 'Week ' . $groupedRecord['week'] . ' • ' . $periodDate->format('F Y');

        $breedType = $this->extractBreedType($flock->breed ?? '');
        $isBroiler = $breedType === 'Broiler';

        $flockAge = null;
        $flockStatus = null;
        if ($flock) {
            $endDate = $flock->flockEnds()->latest('sale_date')->first()?->sale_date;
            $flockAge = $this->calculateFlockAge($flock->start_date, $endDate, $breedType);
            $flockStatus = $endDate ? 'Completed' : 'Active';
        }

        // Group records by hangar and format hangar details
        $hangars = $records->groupBy('hangar_id')->map(function ($hangarRecords) use ($isBroiler) {
            $firstHangarRecord = $hangarRecords->first();
            $totalFeed = $hangarRecords->sum('feed_kg');
            $totalEggsTray = $hangarRecords->sum('eggs_tray_30');
            $totalEggs = $hangarRecords->sum('eggs_count');
            $totalEggsWeight = $hangarRecords->sum('eggs_weight');
            $totalChicksWeight = $hangarRecords->sum('chicks_weight');
            $totalMortality = $hangarRecords->sum('mortality');

            $materialStockHangar = \App\Models\MaterialStockHangar::where('hangar_id', $firstHangarRecord->hangar_id)
                ->latest('created_at')
                ->first();
            $remainingQty = $materialStockHangar?->remaining_quantity ?? 0;

            $hangarData = [
                'id' => $firstHangarRecord->id,
                'hangar_id' => $firstHangarRecord->hangar_id,
                'hangar_name' => $firstHangarRecord->hangar?->name,
                'status' => $firstHangarRecord->hangar?->status,
                'feed_qty' => $this->formatDecimal($totalFeed),
                'remaining_qty' => $this->formatDecimal($remainingQty),
                'mortality' => (int) $totalMortality,
            ];

            if ($isBroiler) {
                $hangarData['chicks_weight'] = $this->formatDecimal($totalChicksWeight);
            } else {
                $hangarData['eggs_tray_30'] = (int) $totalEggsTray;
                $hangarData['eggs_count'] = (int) $totalEggs;
                $hangarData['eggs_weight'] = $this->formatDecimal($totalEggsWeight);
            }

            return $hangarData;
        })->values();

        $totalFeed = $records->sum('feed_kg');
        $totalEggsTray = $records->sum('eggs_tray_30');
        $totalEggs = $records->sum('eggs_count');
        $totalEggsWeight = $records->sum('eggs_weight');
        $totalChicksWeight = $records->sum('chicks_weight');
        $totalMortality = $records->sum('mortality');

        return [
            'period'          => $weekLabel,
            'year'            => $groupedRecord['year'],
            'week'            => $groupedRecord['week'],
            'period_date'     => $periodDate->format('Y-m-d'),
            'farm_id'         => $groupedRecord['farm_id'],
            'farm_name'       => $farm?->name,
            'flock_id'        => $groupedRecord['flock_id'],
            'flock_name'      => $flock?->name,
            'flock_age'       => $flockAge,
            'flock_status'    => $flockStatus,
            'feed_qty'        => $this->formatDecimal($totalFeed),
            'eggs_tray_30'    => (int) $totalEggsTray,
            'eggs_count'      => (int) $totalEggs,
            'eggs_weight'     => $this->formatDecimal($totalEggsWeight),
            'chicks_weight'   => $this->formatDecimal($totalChicksWeight),
            'mortality'       => (int) $totalMortality,
            'hangars'         => $hangars,
        ];
    }

    private function formatMonthlyRecordWithHangars($groupedRecord): array
    {
        $records = $groupedRecord['records'];
        $firstRecord = $records->first();
        $farm = $firstRecord->farm;
        $flock = $firstRecord->flock;

        $periodDate = $groupedRecord['period_date'];
        $monthLabel = $periodDate->format('F Y');

        $breedType = $this->extractBreedType($flock->breed ?? '');
        $isBroiler = $breedType === 'Broiler';

        $flockAge = null;
        $flockStatus = null;
        if ($flock) {
            $endDate = $flock->flockEnds()->latest('sale_date')->first()?->sale_date;
            $flockAge = $this->calculateFlockAge($flock->start_date, $endDate, $breedType);
            $flockStatus = $endDate ? 'Completed' : 'Active';
        }

        // Group records by hangar and format hangar details
        $hangars = $records->groupBy('hangar_id')->map(function ($hangarRecords) use ($isBroiler) {
            $firstHangarRecord = $hangarRecords->first();
            $totalFeed = $hangarRecords->sum('feed_kg');
            $totalEggsTray = $hangarRecords->sum('eggs_tray_30');
            $totalEggs = $hangarRecords->sum('eggs_count');
            $totalEggsWeight = $hangarRecords->sum('eggs_weight');
            $totalChicksWeight = $hangarRecords->sum('chicks_weight');
            $totalMortality = $hangarRecords->sum('mortality');

            $materialStockHangar = \App\Models\MaterialStockHangar::where('hangar_id', $firstHangarRecord->hangar_id)
                ->latest('created_at')
                ->first();
            $remainingQty = $materialStockHangar?->remaining_quantity ?? 0;

            $hangarData = [
                'id' => $firstHangarRecord->id,
                'hangar_id' => $firstHangarRecord->hangar_id,
                'hangar_name' => $firstHangarRecord->hangar?->name,
                'status' => $firstHangarRecord->hangar?->status,
                'feed_qty' => $this->formatDecimal($totalFeed),
                'remaining_qty' => $this->formatDecimal($remainingQty),
                'mortality' => (int) $totalMortality,
            ];

            if ($isBroiler) {
                $hangarData['chicks_weight'] = $this->formatDecimal($totalChicksWeight);
            } else {
                $hangarData['eggs_tray_30'] = (int) $totalEggsTray;
                $hangarData['eggs_count'] = (int) $totalEggs;
                $hangarData['eggs_weight'] = $this->formatDecimal($totalEggsWeight);
            }

            return $hangarData;
        })->values();

        $totalFeed = $records->sum('feed_kg');
        $totalEggsTray = $records->sum('eggs_tray_30');
        $totalEggs = $records->sum('eggs_count');
        $totalEggsWeight = $records->sum('eggs_weight');
        $totalChicksWeight = $records->sum('chicks_weight');
        $totalMortality = $records->sum('mortality');

        return [
            'period'          => $monthLabel,
            'year'            => $groupedRecord['year'],
            'month'           => $groupedRecord['month'],
            'period_date'     => $periodDate->format('Y-m-d'),
            'farm_id'         => $groupedRecord['farm_id'],
            'farm_name'       => $farm?->name,
            'flock_id'        => $groupedRecord['flock_id'],
            'flock_name'      => $flock?->name,
            'flock_age'       => $flockAge,
            'flock_status'    => $flockStatus,
            'feed_qty'        => $this->formatDecimal($totalFeed),
            'eggs_tray_30'    => (int) $totalEggsTray,
            'eggs_count'      => (int) $totalEggs,
            'eggs_weight'     => $this->formatDecimal($totalEggsWeight),
            'chicks_weight'   => $this->formatDecimal($totalChicksWeight),
            'mortality'       => (int) $totalMortality,
            'hangars'         => $hangars,
        ];
    }

    private function formatAndGroupDailyRecords($records)
    {
        if ($records->isEmpty()) {
            return null;
        }

        // Group records by date, farm, and flock
        $groupedByDate = $records->groupBy(function ($record) {
            return $record->record_date->format('Y-m-d') . '|' . $record->farm_id . '|' . $record->flock_id;
        })->map(function ($dateGroup) {
            $firstRecord = $dateGroup->first();
            return [
                'record_date' => $firstRecord->record_date,
                'farm_id' => $firstRecord->farm_id,
                'flock_id' => $firstRecord->flock_id,
                'records' => $dateGroup
            ];
        })->values();

        // Format the grouped data using the existing format method
        return $groupedByDate->map(function ($groupedRecord) {
            return $this->formatDailyAggregateRecordWithHangars($groupedRecord);
        })->first();
    }

}
