<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\MaterialStock;
use App\Models\MaterialStockHangar;
use App\Models\Hangar;
use App\Models\Farm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @group Add2Farm Material Stock
 * APIs for managing material stock (feed) inventory in Add2Farm
 */
class FeedStockController extends BaseController
{
    /**
     * List all material stocks
     *
     * Get paginated list of all material stocks with filtering.
     *
     * @authenticated
     * @queryParam farm_id integer optional Filter by farm ID. Example: 1
     * @queryParam search string optional Search by material name. Example: Starter
     * @queryParam page integer optional Pagination page. Example: 1
     * @queryParam per_page integer optional Items per page. Default: 15. Example: 20
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Material stocks retrieved.",
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "stock_date": "2026-08-28",
     *         "name": "Starter feed",
     *         "quantity": 5000,
     *         "supplier_name": "Al-Rowad",
     *         "farm_name": "North Farm"
     *       }
     *     ]
     *   }
     * }
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = auth()->user();

        $records = MaterialStock::whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->when($request->farm_id, fn($q) => $q->where('farm_id', $request->farm_id))
            ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->with('farm', 'supplier', 'creator', 'materialStockHangarAllocations.hangar')
            ->orderBy('stock_date', 'desc')
            ->paginate($request->per_page ?? 15);

        $records->setCollection($records->getCollection()->map(fn($r) => $this->formatRecord($r)));

        return response()->json(['success' => true, 'message' => 'Material stocks retrieved.', 'data' => $records]);
    }

    /**
     * Get a single material stock
     *
     * Retrieve detailed information of a specific material stock.
     *
     * @authenticated
     * @urlParam id integer required The material stock ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": {...}
     * }
     */
    public function show($id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = auth()->user();

        $record = MaterialStock::whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->with('farm', 'supplier', 'creator', 'materialStockHangarAllocations.hangar')
            ->find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Material stock not found.'], 404);
        }

        return response()->json(['success' => true, 'data' => $this->formatRecord($record)]);
    }

    /**
     * Create a new material stock
     *
     * Create a new material stock with hangar allocations.
     *
     * @authenticated
     * @bodyParam stock_date date required Stock date (format: d-m-Y). Example: 28-08-2026
     * @bodyParam farm_id integer required Farm ID. Example: 1
     * @bodyParam name string required Material name (e.g., Starter feed, Corn). Example: Starter feed
     * @bodyParam quantity integer required Total quantity. Example: 5000
     * @bodyParam supplier_id integer required Supplier ID. Example: 1
     * @bodyParam hangar_allocations array required Array of hangar allocations. Example: [{"hangar_id": 1, "quantity": 1250}]
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Material stock created.",
     *   "data": {...}
     * }
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'stock_date' => 'required|date_format:d-m-Y',
            'farm_id' => 'required|integer|exists:farms,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'required|integer|exists:chicks_suppliers,id',
            'hangar_allocations' => 'required|array|min:1',
            'hangar_allocations.*.hangar_id' => 'required|integer|exists:hangars,id',
            'hangar_allocations.*.quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Verify farm access
            $farm = Farm::where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            })->find($request->farm_id);

            if (!$farm) {
                return response()->json(['success' => false, 'message' => 'Farm not found or access denied.'], 403);
            }

            // Validate all hangars belong to this farm
            $farmHangarIds = Hangar::where('farm_id', $request->farm_id)->pluck('id')->toArray();
            $requestHangarIds = array_column($request->hangar_allocations, 'hangar_id');
            $invalidHangars = array_diff($requestHangarIds, $farmHangarIds);

            if (!empty($invalidHangars)) {
                return response()->json(['success' => false, 'message' => 'One or more hangars do not belong to this farm.'], 422);
            }

            // Validate total quantity matches allocations
            $totalAllocated = collect($request->hangar_allocations)->sum('quantity');
            if ($totalAllocated != $request->quantity) {
                return response()->json(['success' => false, 'message' => 'Hangar quantities must equal total quantity.'], 422);
            }

            $stockDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->stock_date);

            $record = MaterialStock::create([
                'stock_date' => $stockDate,
                'farm_id' => $request->farm_id,
                'name' => $request->name,
                'quantity' => $request->quantity,
                'supplier_id' => $request->supplier_id,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->hangar_allocations as $alloc) {
                MaterialStockHangar::create([
                    'material_stock_id' => $record->id,
                    'hangar_id' => $alloc['hangar_id'],
                    'quantity' => $alloc['quantity'],
                    'remaining_quantity' => $alloc['quantity'],
                ]);
            }

            DB::commit();
            $record->load('farm', 'supplier', 'creator', 'materialStockHangarAllocations.hangar');

            return response()->json(['success' => true, 'message' => 'Material stock created.', 'data' => $this->formatRecord($record)], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Material stock creation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create material stock.'], 500);
        }
    }

    /**
     * Update a material stock
     *
     * Update material stock and hangar allocations.
     *
     * @authenticated
     * @urlParam id integer required The material stock ID. Example: 1
     * @bodyParam stock_date date required Stock date (format: d-m-Y). Example: 28-08-2026
     * @bodyParam name string required Material name. Example: Starter feed
     * @bodyParam quantity integer required Total quantity. Example: 5000
     * @bodyParam supplier_id integer required Supplier ID. Example: 1
     * @bodyParam hangar_allocations array required Array of hangar allocations.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Material stock updated."
     * }
     */
    public function update(Request $request, $id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = auth()->user();

        $record = MaterialStock::whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Material stock not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'stock_date' => 'required|date_format:d-m-Y',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'supplier_id' => 'required|integer|exists:chicks_suppliers,id',
            'hangar_allocations' => 'required|array|min:1',
            'hangar_allocations.*.hangar_id' => 'required|integer|exists:hangars,id',
            'hangar_allocations.*.quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Validate all hangars belong to this farm
            $farmHangarIds = Hangar::where('farm_id', $record->farm_id)->pluck('id')->toArray();
            $requestHangarIds = array_column($request->hangar_allocations, 'hangar_id');
            $invalidHangars = array_diff($requestHangarIds, $farmHangarIds);

            if (!empty($invalidHangars)) {
                return response()->json(['success' => false, 'message' => 'One or more hangars do not belong to this farm.'], 422);
            }

            // Validate total quantity matches allocations
            $totalAllocated = collect($request->hangar_allocations)->sum('quantity');
            if ($totalAllocated != $request->quantity) {
                return response()->json(['success' => false, 'message' => 'Hangar quantities must equal total quantity.'], 422);
            }

            $stockDate = \Carbon\Carbon::createFromFormat('d-m-Y', $request->stock_date);

            $record->update([
                'stock_date' => $stockDate,
                'name' => $request->name,
                'quantity' => $request->quantity,
                'supplier_id' => $request->supplier_id,
            ]);

            $record->materialStockHangarAllocations()->delete();

            foreach ($request->hangar_allocations as $alloc) {
                MaterialStockHangar::create([
                    'material_stock_id' => $record->id,
                    'hangar_id' => $alloc['hangar_id'],
                    'quantity' => $alloc['quantity'],
                    'remaining_quantity' => $alloc['quantity'],
                ]);
            }

            DB::commit();
            $record->load('farm', 'supplier', 'creator', 'materialStockHangarAllocations.hangar');

            return response()->json(['success' => true, 'message' => 'Material stock updated.', 'data' => $this->formatRecord($record)]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Material stock update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update material stock.'], 500);
        }
    }

    /**
     * Delete a material stock
     *
     * Delete a material stock and all hangar allocations.
     *
     * @authenticated
     * @urlParam id integer required The material stock ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Material stock deleted."
     * }
     */
    public function destroy($id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = auth()->user();

        $record = MaterialStock::whereHas('farm', function ($q) use ($user) {
                $q->where(function ($q) use ($user) {
                    $q->where('created_by', $user->id)
                      ->orWhere('assigned_to', $user->id);
                });
            })
            ->find($id);

        if (!$record) {
            return response()->json(['success' => false, 'message' => 'Material stock not found.'], 404);
        }

        try {
            DB::beginTransaction();
            $record->materialStockHangarAllocations()->delete();
            $record->delete();
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Material stock deleted.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Material stock deletion error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete material stock.'], 500);
        }
    }

    private function formatRecord($record)
    {
        return [
            'id' => $record->id,
            'stock_date' => $record->stock_date->format('Y-m-d'),
            'farm_id' => $record->farm_id,
            'farm_name' => $record->farm->name,
            'name' => $record->name,
            'quantity' => $record->quantity,
            'supplier_id' => $record->supplier_id,
            'supplier_name' => $record->supplier->name,
            'hangar_allocations' => $record->materialStockHangarAllocations->map(fn($a) => [
                'hangar_id' => $a->hangar_id,
                'hangar_name' => $a->hangar->name,
                'quantity' => $a->quantity,
                'remaining_quantity' => $a->remaining_quantity,
            ]),
            'created_by' => $record->created_by,
            'created_by_name' => $record->creator->name,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
