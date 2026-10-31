<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Helpers\DecimalHelper;
use App\Helpers\FeedStockHelper;
use Illuminate\Http\Request;
use App\Models\MaterialStock;
use App\Models\Farm;
use App\Models\FeedSupplier;
use App\Models\ChicksSupplier;
use App\Models\Hangar;
use App\Models\MaterialStockHangar;
use App\Models\MaterialName;
use App\Models\DailyRecord;
use App\Models\BreedCategoryMaterialType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;

class MaterialStockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();

            $userFarms = Farm::where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            })->pluck('id');

            if ($userFarms->isEmpty() && $user->role !== 'SuperAdmin') {
                return datatables()->of(collect([]))->make(true);
            }

            $query = MaterialStock::with('farm.assignedAdmin', 'supplier', 'creator', 'materialName', 'materialStockHangarAllocations.hangar');

            if ($user->role !== 'SuperAdmin') {
                $query->where(function ($subQuery) use ($userFarms) {
                    $subQuery->whereIn('farm_id', $userFarms)
                             ->orWhere('created_by', auth()->id());
                });
            }

            $data = $query->orderBy('created_at', 'desc')->get();

            // Farm hangars in creation order, so column N always means the farm's Nth hangar
            $farmHangars = Hangar::whereIn('farm_id', $data->pluck('farm_id')->unique())
                ->orderBy('id')->get()->groupBy('farm_id');

            return datatables()->of($data)
                ->addColumn('stock_date', function($row) {
                    return date('Y-m-d', strtotime($row->stock_date));
                })
                ->addColumn('name', function($row) {
                    $name = $row->materialName?->name ?? $row->name ?? 'N/A';
                    $type = $row->materialName?->type ?? 'N/A';
                    return $name . '<br><small style="color: #666;">(' . $type . ')</small>';
                })
                ->addColumn('farm', function($row) {
                    $farmName = $row->farm?->name ?? 'N/A';
                    $assignmentStatus = '';
                    if ($row->farm) {
                        if ($row->farm->assignedAdmin) {
                            $assignmentStatus = '<br><small style="color: #666;">Assigned to: ' . $row->farm->assignedAdmin->name . '</small>';
                        }
                    }
                    return $farmName . $assignmentStatus;
                })
                ->addColumn('supplier', function($row) {
                    return $row->supplier?->name ?? 'N/A';
                })
                ->addColumn('created_by', function($row) {
                    return $row->creator?->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('quantity', function($row) {
                    // Remaining of this feed stock entry across its hangar allocations
                    $totalRemaining = 0;

                    foreach ($row->materialStockHangarAllocations as $allocation) {
                        $totalRemaining += $this->getAllocationRemainingFeed($allocation);
                    }

                    return 'Total: ' . DecimalHelper::formatInteger($row->quantity) . ' kg<br>' .
                           'Remaining: ' . DecimalHelper::formatInteger($totalRemaining) . ' kg';
                })
                ->addColumn('assignment_status', function($row) {
                    $user = auth()->user();
                    if ($row->created_by === $user->id) {
                        return '<span class="badge badge-success">Created by me</span>';
                    } elseif ($row->farm && $row->farm->assigned_to === $user->id) {
                        return '<span class="badge badge-info">Farm assigned to me</span>';
                    }
                    return 'N/A';
                })
                ->addColumn('hangar1', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 1, $farmHangars);
                })
                ->addColumn('hangar2', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 2, $farmHangars);
                })
                ->addColumn('hangar3', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 3, $farmHangars);
                })
                ->addColumn('hangar4', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 4, $farmHangars);
                })
                ->addColumn('hangar5', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 5, $farmHangars);
                })
                ->addColumn('hangar6', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 6, $farmHangars);
                })
                ->addColumn('hangar7', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 7, $farmHangars);
                })
                ->addColumn('hangar8', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 8, $farmHangars);
                })
                ->addColumn('hangar9', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 9, $farmHangars);
                })
                ->addColumn('hangar10', function($row) use ($farmHangars) {
                    return $this->hangarCell($row, 10, $farmHangars);
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-material-stock btn btn-sm btn-success mr-1" data-id="'.$row->id.'" data-path="'.route('material-stock.edit', ['username' => request()->segment(1), 'material_stock' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-material-stock btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['name','quantity','action', 'farm', 'assignment_status', 'hangar1', 'hangar2', 'hangar3', 'hangar4', 'hangar5', 'hangar6', 'hangar7', 'hangar8', 'hangar9', 'hangar10'])
                ->make(true);
        }
        return view('backend.material-stock.index');
    }

    public function create()
    {
        $user = auth()->user();

        $farms = Farm::where(function($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
        });

        if ($user->role === 'SuperAdmin') {
            $farms = Farm::query();
        }

        $suppliers = FeedSupplier::all();
        $materialNames = MaterialName::all();
        $farms = $farms->get();

        return view('backend.material-stock.create', compact('farms', 'suppliers', 'materialNames'));
    }

    public function getHangarsByFarm($siteUrl, $farmId)
    {
        $user = auth()->user();
        $farmId = (int) $farmId;

        // Verify user has access to the farm
        $farm = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->find($farmId);

        if (!$farm && $user->role !== 'SuperAdmin') {
            return response()->json([], 403);
        }

        $hangars = Hangar::where('farm_id', $farmId)
            ->select('id', 'name')
            ->get();

        $hangars = $hangars->map(function ($hangar) {
            $remaining = $this->getHangarRemainingFeed($hangar->id);
            return [
                'id' => $hangar->id,
                'name' => $hangar->name,
                'remaining' => DecimalHelper::formatInteger($remaining)
            ];
        });

        return response()->json($hangars);
    }

    public function getMaterialsByFarm($siteUrl, $farmId)
    {
        $user = auth()->user();
        $farmId = (int) $farmId;

        // Verify user has access to the farm
        $farm = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->find($farmId);

        if (!$farm && $user->role !== 'SuperAdmin') {
            return response()->json([], 403);
        }

        // Get farm's breed category from first flock
        $firstFlock = $farm->flocks()->oldest('created_at')->first();
        $breedCategory = $firstFlock ? $this->extractBreedType($firstFlock->breed) : null;

        // If farm has no flocks, return empty result
        if (!$breedCategory) {
            return response()->json([]);
        }

        // Get mapped material types for this breed category from the mapping table
        $mappedTypeIds = BreedCategoryMaterialType::getMaterialTypeIdsForBreed($breedCategory);

        // If no mappings exist, return empty result
        if (empty($mappedTypeIds)) {
            return response()->json([]);
        }

        // Filter materials based on mapped material types
        $query = MaterialName::with('materialType')->select('id', 'name', 'material_type_id');

       
        $query->whereIn('material_type_id', $mappedTypeIds);
        

        $materials = $query->orderBy('name')->get()->map(function ($material) {
            return [
                'id' => $material->id,
                'name' => $material->name,
                'type' => $material->materialType?->name ?? 'N/A',
                'hangar_allocation' => (bool) $material->materialType?->hangar_allocation,
            ];
        });

        return response()->json($materials);
    }

    public function store(Request $request, $siteUrl)
    {
        // Get material type to determine hangar allocation requirement
        $materialName = MaterialName::find($request->material_name_id);

        // Hangar allocation is required when the material type is flagged for it
        $hangarAllocationRequired = (bool) $materialName?->materialType?->hangar_allocation;

        $request->merge(['quantity' => DecimalHelper::parseEuropean($request->quantity)]);

        $rules = [
            'farm_id' => 'required|exists:farms,id',
            'supplier_id' => 'required|exists:feed_suppliers,id',
            'material_name_id' => 'required|exists:material_names,id',
            'stock_date' => 'required|date_format:Y-m-d',
            'quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => $hangarAllocationRequired ? 'required|json' : 'nullable|json',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator->errors());
        }

        $hangarQuantities = json_decode($request->hangar_quantities_json, true) ?? [];

        if ($hangarAllocationRequired && empty($hangarQuantities)) {
            $error = ['hangar_quantities_json' => 'Please select at least one hangar with quantity for this material type.'];
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $error], 422);
            }
            return back()->withErrors($error);
        }

        if (!empty($hangarQuantities)) {
            $totalAllocated = collect($hangarQuantities)->sum('quantity');
            if ((float)$totalAllocated != (float)$request->quantity) {
                $message = 'Hangar quantities must equal total quantity.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->withErrors($message);
            }
        }

        try {
            DB::beginTransaction();

            $materialName = MaterialName::findOrFail($request->material_name_id);
            $stockDate = Carbon::createFromFormat('Y-m-d', $request->stock_date);

            $materialStock = MaterialStock::create([
                'farm_id' => $request->farm_id,
                'supplier_id' => $request->supplier_id,
                'material_name_id' => $request->material_name_id,
                'name' => $materialName->name,
                'stock_date' => $stockDate,
                'quantity' => (float)$request->quantity,
                'created_by' => auth()->id()
            ]);

            // Save hangar allocations if provided
            if (!empty($hangarQuantities)) {
                foreach ($hangarQuantities as $allocation) {
                    MaterialStockHangar::create([
                        'material_stock_id' => $materialStock->id,
                        'hangar_id' => $allocation['hangar_id'],
                        'quantity' => (float)$allocation['quantity'],
                    ]);
                }
            } 
            // else {
            //     $farmHangars = Hangar::where('farm_id', $request->farm_id)->get();
            //     $quantityPerHangar = count($farmHangars) > 0
            //         ? (float)$request->quantity / count($farmHangars)
            //         : (float)$request->quantity;

            //     foreach ($farmHangars as $hangar) {
            //         MaterialStockHangar::create([
            //             'material_stock_id' => $materialStock->id,
            //             'hangar_id' => $hangar->id,
            //             'quantity' => $quantityPerHangar,
            //             'remaining_quantity' => $quantityPerHangar
            //         ]);
            //     }
            // }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Feed Stock created successfully.'], 201);
            }

            Session::flash('successMsg', 'Feed Stock created successfully.');
            return redirect()->route('material-stock.index', ['username' => request()->segment(1)]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Material stock creation error: ' . $e->getMessage());
            $message = 'Failed to create material stock: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return back()->withErrors($message);
        }
    }

    public function edit($siteUrl, $id)
    {
        $user = auth()->user();
        $materialStock = MaterialStock::findOrFail($id);

        // Verify user has access to the farm
        $farm = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->find($materialStock->farm_id);

        if (!$farm && $user->role !== 'SuperAdmin') {
            abort(403, 'Farm not found or access denied.');
        }

        $farms = Farm::where(function($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
        });

        if ($user->role === 'SuperAdmin') {
            $farms = Farm::query();
        }

        $suppliers = FeedSupplier::all();
        $materialNames = MaterialName::all();
        $materialStockHangars = MaterialStockHangar::where('material_stock_id', $materialStock->id)->get();
        $farms = $farms->get();

        return view('backend.material-stock.create', compact('materialStock', 'farms', 'suppliers', 'materialNames', 'materialStockHangars'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $user = auth()->user();

        // Get material type to determine hangar allocation requirement
        $materialName = MaterialName::find($request->material_name_id);
        $hangarAllocationRequired = (bool) $materialName?->materialType?->hangar_allocation;

        $request->merge(['quantity' => DecimalHelper::parseEuropean($request->quantity)]);

        $rules = [
            'farm_id' => 'required|exists:farms,id',
            'supplier_id' => 'required|exists:feed_suppliers,id',
            'material_name_id' => 'required|exists:material_names,id',
            'stock_date' => 'required|date_format:Y-m-d',
            'quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => $hangarAllocationRequired ? 'required|json' : 'nullable|json',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator->errors());
        }

        $materialStock = MaterialStock::findOrFail($id);

        // Verify user has access to the farm
        $farm = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->find($request->farm_id);

        if (!$farm && $user->role !== 'SuperAdmin') {
            $message = 'Farm not found or access denied.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            return back()->withErrors($message);
        }

        $hangarQuantities = json_decode($request->hangar_quantities_json, true) ?? [];

        if ($hangarAllocationRequired && empty($hangarQuantities)) {
            $error = ['hangar_quantities_json' => 'Please select at least one hangar with quantity for this material type.'];
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $error], 422);
            }
            return back()->withErrors($error);
        }

        // Validate hangar allocations only if they are provided
        if (!empty($hangarQuantities)) {
            // Validate all hangars belong to this farm
            $farmHangarIds = Hangar::where('farm_id', $request->farm_id)
                ->pluck('id')
                ->toArray();

            $requestHangarIds = array_column($hangarQuantities, 'hangar_id');
            $invalidHangars = array_diff($requestHangarIds, $farmHangarIds);

            if (!empty($invalidHangars)) {
                $message = 'One or more hangars do not belong to this farm or are not active.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->withErrors($message);
            }

            // Check for duplicate hangars
            if (count($requestHangarIds) !== count(array_unique($requestHangarIds))) {
                $message = 'Duplicate hangars are not allowed. Each hangar can only be selected once.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->withErrors($message);
            }

            // Validate total quantity matches allocations
            $totalAllocated = collect($hangarQuantities)->sum('quantity');
            if ((float)$totalAllocated != (float)$request->quantity) {
                $message = 'Hangar quantities must equal total quantity.';
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->withErrors($message);
            }
        }

        try {
            DB::beginTransaction();

            $materialName = MaterialName::findOrFail($request->material_name_id);
            $stockDate = Carbon::createFromFormat('Y-m-d', $request->stock_date);

            $materialStock->update([
                'farm_id' => $request->farm_id,
                'supplier_id' => $request->supplier_id,
                'material_name_id' => $request->material_name_id,
                'name' => $materialName->name,
                'stock_date' => $stockDate,
                'quantity' => (float)$request->quantity,
            ]);

            // Delete old allocations
            $materialStock->materialStockHangarAllocations()->delete();

            // Save new allocations
            if (!empty($hangarQuantities)) {
                foreach ($hangarQuantities as $allocation) {
                    MaterialStockHangar::create([
                        'material_stock_id' => $materialStock->id,
                        'hangar_id' => $allocation['hangar_id'],
                        'quantity' => (float)$allocation['quantity'],
                    ]);
                }
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Feed Stock updated successfully.'], 200);
            }

            Session::flash('successMsg', 'Feed Stock updated successfully.');
            return redirect()->route('material-stock.index', ['username' => request()->segment(1)]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Material stock update error: ' . $e->getMessage());
            $message = 'Failed to update material stock: ' . $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return back()->withErrors($message);
        }
    }

    public function destroy($siteUrl, $id)
    {
        $user = auth()->user();
        $materialStock = MaterialStock::with('farm')->findOrFail($id);

        // Verify user has access to the farm
        if ($user->role !== 'SuperAdmin') {
            $farm = $materialStock->farm;
            $hasAccess = $farm && (
                $farm->created_by === $user->id ||
                $farm->assigned_to === $user->id
            );

            if (!$hasAccess) {
                return response()->json(['error' => 'You do not have permission to delete this material stock.'], 403);
            }
        }

        try {
            DB::beginTransaction();
            $materialStock->materialStockHangarAllocations()->delete();
            $materialStock->delete();
            DB::commit();
            return response()->json(['msg' => 'Material Stock deleted successfully.']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Material stock deletion error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete material stock: ' . $e->getMessage()], 500);
        }
    }

    private function getHangarRemainingFeed($hangarId)
    {
        return FeedStockHelper::hangarRemaining($hangarId);
    }

    private function getAllocationRemainingFeed(MaterialStockHangar $allocation)
    {
        return FeedStockHelper::remainingByAllocation($allocation->hangar_id)[$allocation->id] ?? 0;
    }

    // Renders the allocation for the farm's Nth hangar, so each hangar stays in its own column
    private function hangarCell($row, int $position, $farmHangars)
    {
        $hangar = ($farmHangars[$row->farm_id] ?? collect())->values()->get($position - 1);
        $allocation = $hangar ? $row->materialStockHangarAllocations->firstWhere('hangar_id', $hangar->id) : null;

        if (!$allocation || !$allocation->hangar) {
            return 'N/A';
        }

        $remaining = $this->getAllocationRemainingFeed($allocation);
        return $allocation->hangar->name . '<br>Qty: ' . DecimalHelper::formatInteger($allocation->quantity) . '<br>Remaining: ' . DecimalHelper::formatInteger($remaining);
    }

    private function extractBreedType($breedString)
    {
        $breedType = 'Layer';

        if (!empty($breedString)) {
            // If breed contains comma, take first part (e.g., "Layer, Lohmann brown" → "Layer")
            if (strpos($breedString, ',') !== false) {
                $breedParts = explode(',', $breedString);
                $breedType = trim($breedParts[0]);
            } else {
                // Check for specific breed names
                if (stripos($breedString, 'cobb') !== false || stripos($breedString, 'ross') !== false) {
                    $breedType = 'Broiler';
                } elseif (stripos($breedString, 'lohmann') !== false || stripos($breedString, 'hy-line') !== false) {
                    $breedType = 'Layer';
                }
            }
        }

        return $breedType;
    }
}
