<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\DailyRecord;
use App\Models\Flock;
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
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Daily records retrieved successfully.",
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "record_date": "2026-08-07",
     *         "farm_id": 1,
     *         "farm_name": "Main Farm",
     *         "flock_id": 1,
     *         "flock_name": "Farm1-Flock4",
     *         "hangar_id": 1,
     *         "hangar_name": "Farm1-Hangar1",
     *         "feed_kg": 450.50,
     *         "eggs_tray_30": 12,
     *         "eggs_count": 360,
     *         "eggs_weight": 18.50,
     *         "chicks_weight": 1.85,
     *         "mortality": 5,
     *         "created_by": 1,
     *         "created_by_name": "Admin Name",
     *         "created_at": "2026-08-07T10:30:00Z"
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

        $records = DailyRecord::where('created_by', auth()->id())
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
            ->with('farm', 'flock', 'hangar', 'creator')
            ->orderBy('record_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        $records->setCollection($records->getCollection()->map(function ($record) {
            return $this->formatDailyRecord($record);
        }));

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('daily_records_retrieved_successfully'),
            'data' => $records,
        ]);
    }

    /**
     * Get a single daily record
     *
     * Retrieve detailed information of a specific daily record.
     *
     * @authenticated
     * @urlParam id integer required The daily record ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Daily record retrieved successfully.",
     *   "data": {
     *     "id": 1,
     *     "record_date": "2026-08-07",
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
     *     "flock_id": 1,
     *     "flock_name": "Farm1-Flock4",
     *     "hangar_id": 1,
     *     "hangar_name": "Farm1-Hangar1",
     *     "feed_kg": 450.50,
     *     "eggs_tray_30": 12,
     *     "eggs_count": 360,
     *     "eggs_weight": 18.50,
     *     "chicks_weight": 1.85,
     *     "mortality": 5,
     *     "created_by": 1,
     *     "created_by_name": "Admin Name",
     *     "created_at": "2026-08-07T10:30:00Z"
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
            ->with('farm', 'flock', 'hangar', 'creator')
            ->find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('daily_record_not_found'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('daily_record_retrieved_successfully'),
            'data' => $this->formatDailyRecord($record),
        ]);
    }

    /**
     * Create a new daily record
     *
     * Create a new daily record for a flock/hangar combination.
     *
     * @authenticated
     * @bodyParam record_date date required Record date (format: dd-mm-yyyy). Example: 07-08-2026
     * @bodyParam flock_id integer required Flock ID. Example: 1
     * @bodyParam hangar_id integer required Hangar ID. Example: 1
     * @bodyParam feed_kg number required Feed quantity in kg. Example: 450.50
     * @bodyParam eggs_tray_30 integer optional Number of egg trays (30 count). Example: 12
     * @bodyParam eggs_count integer optional Egg count. Example: 360
     * @bodyParam eggs_weight number optional Eggs weight in kg. Example: 18.50
     * @bodyParam chicks_weight number optional Chicks weight in kg. Example: 1.85
     * @bodyParam mortality integer optional Mortality count. Example: 5
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Daily record created successfully.",
     *   "data": {
     *     "id": 1,
     *     "record_date": "2026-08-07",
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
     *     "flock_id": 1,
     *     "flock_name": "Farm1-Flock4",
     *     "hangar_id": 1,
     *     "hangar_name": "Farm1-Hangar1",
     *     "feed_kg": 450.50,
     *     "eggs_tray_30": 12,
     *     "eggs_count": 360,
     *     "eggs_weight": 18.50,
     *     "chicks_weight": 1.85,
     *     "mortality": 5,
     *     "created_by": 1,
     *     "created_by_name": "Admin Name",
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "record_date": ["The record date field is required."]
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
            'record_date'    => 'required|date_format:d-m-Y',
            'flock_id'       => 'required|integer|exists:flocks,id',
            'hangar_id'      => 'required|integer|exists:hangars,id',
            'feed_kg'        => 'required|numeric|min:0',
            'eggs_tray_30'   => 'nullable|integer|min:0',
            'eggs_count'     => 'nullable|integer|min:0',
            'eggs_weight'    => 'nullable|numeric|min:0',
            'chicks_weight'  => 'nullable|numeric|min:0',
            'mortality'      => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Get flock to retrieve farm_id
            $flock = Flock::findOrFail($request->flock_id);

            // Convert date format from dd-mm-yyyy to yyyy-mm-dd
            $recordDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->record_date);

            $record = DailyRecord::create([
                'record_date'   => $recordDate,
                'farm_id'       => $flock->farm_id,
                'flock_id'      => $request->flock_id,
                'hangar_id'     => $request->hangar_id,
                'feed_kg'       => $request->feed_kg,
                'eggs_tray_30'  => $request->eggs_tray_30 ?? 0,
                'eggs_count'    => $request->eggs_count ?? 0,
                'eggs_weight'   => $request->eggs_weight ?? 0,
                'chicks_weight' => $request->chicks_weight ?? 0,
                'mortality'     => $request->mortality ?? 0,
                'created_by'    => auth()->id(),
            ]);

            DB::commit();

            $record->load('farm', 'flock', 'hangar', 'creator');

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('daily_record_created_successfully'),
                'data'    => $this->formatDailyRecord($record),
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
     * Update a daily record
     *
     * Update an existing daily record.
     *
     * @authenticated
     * @urlParam id integer required The daily record ID. Example: 1
     * @bodyParam record_date date required Record date (format: dd-mm-yyyy). Example: 07-08-2026
     * @bodyParam hangar_id integer required Hangar ID. Example: 1
     * @bodyParam feed_kg number required Feed quantity in kg. Example: 450.50
     * @bodyParam eggs_tray_30 integer optional Number of egg trays (30 count). Example: 12
     * @bodyParam eggs_count integer optional Egg count. Example: 360
     * @bodyParam eggs_weight number optional Eggs weight in kg. Example: 18.50
     * @bodyParam chicks_weight number optional Chicks weight in kg. Example: 1.85
     * @bodyParam mortality integer optional Mortality count. Example: 5
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Daily record updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "record_date": "2026-08-07",
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
     *     "flock_id": 1,
     *     "flock_name": "Farm1-Flock4",
     *     "hangar_id": 1,
     *     "hangar_name": "Farm1-Hangar1",
     *     "feed_kg": 450.50,
     *     "eggs_tray_30": 12,
     *     "eggs_count": 360,
     *     "eggs_weight": 18.50,
     *     "chicks_weight": 1.85,
     *     "mortality": 5,
     *     "created_by": 1,
     *     "created_by_name": "Admin Name",
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Daily record not found."
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "feed_kg": ["The feed kg field is required."]
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

        $validator = Validator::make($request->all(), [
            'record_date'    => 'required|date_format:d-m-Y',
            'hangar_id'      => 'required|integer|exists:hangars,id',
            'feed_kg'        => 'required|numeric|min:0',
            'eggs_tray_30'   => 'nullable|integer|min:0',
            'eggs_count'     => 'nullable|integer|min:0',
            'eggs_weight'    => 'nullable|numeric|min:0',
            'chicks_weight'  => 'nullable|numeric|min:0',
            'mortality'      => 'nullable|integer|min:0',
        ]);

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

            $record->update([
                'record_date'   => $recordDate,
                'hangar_id'     => $request->hangar_id,
                'feed_kg'       => $request->feed_kg,
                'eggs_tray_30'  => $request->eggs_tray_30 ?? 0,
                'eggs_count'    => $request->eggs_count ?? 0,
                'eggs_weight'   => $request->eggs_weight ?? 0,
                'chicks_weight' => $request->chicks_weight ?? 0,
                'mortality'     => $request->mortality ?? 0,
            ]);

            DB::commit();

            $record->load('farm', 'flock', 'hangar', 'creator');

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('daily_record_updated_successfully'),
                'data'    => $this->formatDailyRecord($record),
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
     * Delete a daily record
     *
     * Delete a daily record.
     *
     * @authenticated
     * @urlParam id integer required The daily record ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Daily record deleted successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Daily record not found."
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

        $record = DailyRecord::where('created_by', auth()->id())->find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('daily_record_not_found'),
            ], 404);
        }

        try {
            DB::beginTransaction();

            $record->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('daily_record_deleted_successfully'),
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

        return [
            'id'              => $record->id,
            'record_date'     => $record->record_date?->format('Y-m-d'),
            'farm_id'         => $record->farm_id,
            'farm_name'       => $record->farm?->name,
            'flock_id'        => $record->flock_id,
            'flock_name'      => $record->flock?->name,
            'hangar_id'       => $record->hangar_id,
            'hangar_name'     => $record->hangar?->name,
            'feed_kg'         => (float) $record->feed_kg,
            'eggs_tray_30'    => (int) $record->eggs_tray_30,
            'eggs_count'      => (int) $record->eggs_count,
            'eggs_weight'     => (float) $record->eggs_weight,
            'chicks_weight'   => (float) $record->chicks_weight,
            'mortality'       => (int) $record->mortality,
            'assignment'      => $assignment,
            'created_by'      => $record->created_by,
            'created_by_name' => $record->creator?->name,
            'created_at'      => $record->created_at,
        ];
    }
}
