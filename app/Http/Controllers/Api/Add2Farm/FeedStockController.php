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
     * Get remaining quantities for hangars in a farm
     *
     * Fetch current remaining quantities for each active hangar in a farm.
     * Used to show available stock levels before adding new material.
     *
     * @authenticated
     * @urlParam farm_id integer required Farm ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "hangar_id": 1,
     *       "hangar_name": "Hangar A",
     *       "remaining_quantity": "250,00"
     *     }
     *   ]
     * }
     */
    public function getHangarRemaining($farm_id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = auth()->user();

        // Verify farm access
        $farm = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->find($farm_id);

        if (!$farm) {
            return response()->json(['success' => false, 'message' => 'Farm not found or access denied.'], 403);
        }

        // Get all active hangars and their remaining quantities
        $hangars = Hangar::where('farm_id', $farm_id)
            ->where('status', 'Active')
            ->get()
            ->map(function ($hangar) {
                $remaining = MaterialStockHangar::where('hangar_id', $hangar->id)
                    ->latest('created_at')
                    ->value('remaining_quantity') ?? 0;

                return [
                    'hangar_id' => $hangar->id,
                    'hangar_name' => $hangar->name,
                    'remaining_quantity' => $this->formatDecimal($remaining),
                ];
            });

        return response()->json(['success' => true, 'data' => $hangars]);
    }

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
            ->with('farm', 'supplier', 'creator', 'materialName', 'materialStockHangarAllocations.hangar')
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
            ->with('farm', 'supplier', 'creator', 'materialName', 'materialStockHangarAllocations.hangar')
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
     * Note: remaining_quantity is calculated automatically by backend (current_remaining + new_quantity)
     *
     * @authenticated
     * @bodyParam stock_date date required Stock date (format: d-m-Y). Example: 28-08-2026
     * @bodyParam farm_id integer required Farm ID. Example: 1
     * @bodyParam material_name_id integer required Material Name ID from material_names table. Example: 1
     * @bodyParam quantity integer required Total quantity (use comma for decimal: 5000,50). Example: 5000,00
     * @bodyParam supplier_id integer required Supplier ID. Example: 1
     * @bodyParam hangar_allocations array required Array of hangar allocations with quantity only (remaining calculated by backend). Example: [{"hangar_id": 1, "quantity": "1250,00"}]
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Material stock created.",
     *   "data": {
     *     "id": 1,
     *     "material_name_id": 1,
     *     "hangar_allocations": [
     *       {
     *         "hangar_id": 1,
     *         "hangar_name": "Hangar A",
     *         "quantity": "1250,00",
     *         "remaining_quantity": "1250,00"
     *       }
     *     ]
     *   }
     * }
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $user = auth()->user();

        // Convert comma-formatted decimals to period format for validation
        $data = $request->all();

        if (isset($data['quantity'])) {
            $data['quantity'] = str_replace(',', '.', $data['quantity']);
        }
        if (isset($data['hangar_allocations']) && is_array($data['hangar_allocations'])) {
            foreach ($data['hangar_allocations'] as $key => $alloc) {
                $data['hangar_allocations'][$key]['quantity'] = str_replace(',', '.', $alloc['quantity']);
            }
        }

        $validator = Validator::make($data, [
            'stock_date' => 'required|date_format:d-m-Y',
            'farm_id' => 'required|integer|exists:farms,id',
            'material_name_id' => 'required|integer|exists:material_names,id',
            'quantity' => 'required|numeric|min:1',
            'supplier_id' => 'required|integer|exists:chicks_suppliers,id',
            'hangar_allocations' => 'required|array|min:1',
            'hangar_allocations.*.hangar_id' => 'required|integer|exists:hangars,id',
            'hangar_allocations.*.quantity' => 'required|numeric|min:0',
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
            })->find($data['farm_id']);

            if (!$farm) {
                return response()->json(['success' => false, 'message' => 'Farm not found or access denied.'], 403);
            }

            // Validate all hangars belong to this farm
            $farmHangarIds = Hangar::where('farm_id', $data['farm_id'])->where('status', 'Active')->pluck('id')->toArray();
            $requestHangarIds = array_column($data['hangar_allocations'], 'hangar_id');
            $invalidHangars = array_diff($requestHangarIds, $farmHangarIds);

            if (!empty($invalidHangars)) {
                return response()->json(['success' => false, 'message' => 'One or more hangars do not belong to this farm.'], 422);
            }

            // Validate total quantity matches allocations
            $totalAllocated = collect($data['hangar_allocations'])->sum('quantity');
            if ($totalAllocated != $data['quantity']) {
                return response()->json(['success' => false, 'message' => 'Hangar quantities must equal total quantity.'], 422);
            }

            $stockDate = \Carbon\Carbon::createFromFormat('d-m-Y', $data['stock_date']);

            // Get the material name
            $materialName = \App\Models\MaterialName::findOrFail($data['material_name_id']);

            $record = MaterialStock::create([
                'stock_date' => $stockDate,
                'farm_id' => $data['farm_id'],
                'material_name_id' => $data['material_name_id'],
                'name' => $materialName->name,
                'quantity' => (float)$data['quantity'],
                'supplier_id' => $data['supplier_id'],
                'created_by' => auth()->id(),
            ]);

            foreach ($data['hangar_allocations'] as $alloc) {
                // Get the current remaining quantity for this hangar
                $currentRemaining = MaterialStockHangar::where('hangar_id', $alloc['hangar_id'])
                    ->latest('created_at')
                    ->value('remaining_quantity') ?? 0;

                // Calculate new remaining: current_remaining + new_quantity
                $newRemaining = $currentRemaining + (float)$alloc['quantity'];

                MaterialStockHangar::create([
                    'material_stock_id' => $record->id,
                    'hangar_id' => $alloc['hangar_id'],
                    'quantity' => (float)$alloc['quantity'],
                    'remaining_quantity' => $newRemaining,
                ]);
            }

            DB::commit();
            $record->load('farm', 'supplier', 'creator', 'materialName', 'materialStockHangarAllocations.hangar');

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
     * Note: remaining_quantity is recalculated automatically by backend (current_remaining + new_quantity)
     *
     * @authenticated
     * @urlParam id integer required The material stock ID. Example: 1
     * @bodyParam stock_date date required Stock date (format: d-m-Y). Example: 28-08-2026
     * @bodyParam material_name_id integer required Material Name ID from material_names table. Example: 1
     * @bodyParam quantity integer required Total quantity (use comma for decimal: 5000,50). Example: 5000,00
     * @bodyParam supplier_id integer required Supplier ID. Example: 1
     * @bodyParam hangar_allocations array required Array of hangar allocations with quantity only (remaining recalculated by backend).
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Material stock updated.",
     *   "data": {...}
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

        // Convert comma-formatted decimals to period format for validation
        $data = $request->all();
        if (isset($data['quantity'])) {
            $data['quantity'] = str_replace(',', '.', $data['quantity']);
        }
        if (isset($data['hangar_allocations']) && is_array($data['hangar_allocations'])) {
            foreach ($data['hangar_allocations'] as $key => $alloc) {
                $data['hangar_allocations'][$key]['quantity'] = str_replace(',', '.', $alloc['quantity']);
            }
        }

        $validator = Validator::make($data, [
            'stock_date' => 'required|date_format:d-m-Y',
            'material_name_id' => 'required|integer|exists:material_names,id',
            'quantity' => 'required|numeric|min:1',
            'supplier_id' => 'required|integer|exists:chicks_suppliers,id',
            'hangar_allocations' => 'required|array|min:1',
            'hangar_allocations.*.hangar_id' => 'required|integer|exists:hangars,id',
            'hangar_allocations.*.quantity' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            // Validate all hangars belong to this farm
            $farmHangarIds = Hangar::where('farm_id', $record->farm_id)->where('status', 'Active')->pluck('id')->toArray();
            $requestHangarIds = array_column($data['hangar_allocations'], 'hangar_id');
            $invalidHangars = array_diff($requestHangarIds, $farmHangarIds);

            if (!empty($invalidHangars)) {
                return response()->json(['success' => false, 'message' => 'One or more hangars do not belong to this farm.'], 422);
            }

            // Validate total quantity matches allocations
            $totalAllocated = collect($data['hangar_allocations'])->sum('quantity');
            if ($totalAllocated != $data['quantity']) {
                return response()->json(['success' => false, 'message' => 'Hangar quantities must equal total quantity.'], 422);
            }

            $stockDate = \Carbon\Carbon::createFromFormat('d-m-Y', $data['stock_date']);
            $materialName = \App\Models\MaterialName::findOrFail($data['material_name_id']);

            $record->update([
                'stock_date' => $stockDate,
                'material_name_id' => $data['material_name_id'],
                'name' => $materialName->name,
                'quantity' => (float)$data['quantity'],
                'supplier_id' => $data['supplier_id'],
            ]);

            $record->materialStockHangarAllocations()->delete();

            foreach ($data['hangar_allocations'] as $alloc) {
                // Get the current remaining quantity for this hangar
                $currentRemaining = MaterialStockHangar::where('hangar_id', $alloc['hangar_id'])
                    ->latest('created_at')
                    ->value('remaining_quantity') ?? 0;

                // Calculate new remaining: current_remaining + new_quantity
                $newRemaining = $currentRemaining + (float)$alloc['quantity'];

                MaterialStockHangar::create([
                    'material_stock_id' => $record->id,
                    'hangar_id' => $alloc['hangar_id'],
                    'quantity' => (float)$alloc['quantity'],
                    'remaining_quantity' => $newRemaining,
                ]);
            }

            DB::commit();
            $record->load('farm', 'supplier', 'creator', 'materialName', 'materialStockHangarAllocations.hangar');

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
            'material_name_id' => $record->material_name_id,
            'material_name' => $record->materialName?->name ?? $record->name,
            'name' => $record->name,
            'quantity' => $this->formatDecimal($record->quantity),
            'supplier_id' => $record->supplier_id,
            'supplier_name' => $record->supplier->name,
            'hangar_allocations' => $record->materialStockHangarAllocations->map(fn($a) => [
                'hangar_id' => $a->hangar_id,
                'hangar_name' => $a->hangar->name,
                'quantity' => $this->formatDecimal($a->quantity),
                'remaining_quantity' => $this->formatDecimal($a->remaining_quantity),
            ]),
            'created_by' => $record->created_by,
            'created_by_name' => $record->creator->name,
            'created_at' => $record->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
