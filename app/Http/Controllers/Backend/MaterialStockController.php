<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaterialStock;
use App\Models\Farm;
use App\Models\ChicksSupplier;
use App\Models\Hangar;
use App\Models\MaterialStockHangar;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            return datatables()->of($data)
                ->addColumn('stock_date', function($row) {
                    return date('Y-m-d', strtotime($row->stock_date));
                })
                ->addColumn('name', function($row) {
                    return $row->materialName?->name ?? $row->name ?? 'N/A';
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
                ->addColumn('assignment_status', function($row) {
                    $user = auth()->user();
                    if ($row->created_by === $user->id) {
                        return '<span class="badge badge-success">Created by me</span>';
                    } elseif ($row->farm && $row->farm->assigned_to === $user->id) {
                        return '<span class="badge badge-info">Farm assigned to me</span>';
                    }
                    return 'N/A';
                })
                ->addColumn('hangar1', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[0]) && $allocations[0]->hangar) {
                        return $allocations[0]->hangar->name . '<br>Qty: ' . $allocations[0]->quantity . '<br>Remaining: ' . $allocations[0]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar2', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[1]) && $allocations[1]->hangar) {
                        return $allocations[1]->hangar->name . '<br>Qty: ' . $allocations[1]->quantity . '<br>Remaining: ' . $allocations[1]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar3', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[2]) && $allocations[2]->hangar) {
                        return $allocations[2]->hangar->name . '<br>Qty: ' . $allocations[2]->quantity . '<br>Remaining: ' . $allocations[2]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar4', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[3]) && $allocations[3]->hangar) {
                        return $allocations[3]->hangar->name . '<br>Qty: ' . $allocations[3]->quantity . '<br>Remaining: ' . $allocations[3]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar5', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[4]) && $allocations[4]->hangar) {
                        return $allocations[4]->hangar->name . '<br>Qty: ' . $allocations[4]->quantity . '<br>Remaining: ' . $allocations[4]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar6', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[5]) && $allocations[5]->hangar) {
                        return $allocations[5]->hangar->name . '<br>Qty: ' . $allocations[5]->quantity . '<br>Remaining: ' . $allocations[5]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar7', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[6]) && $allocations[6]->hangar) {
                        return $allocations[6]->hangar->name . '<br>Qty: ' . $allocations[6]->quantity . '<br>Remaining: ' . $allocations[6]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar8', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[7]) && $allocations[7]->hangar) {
                        return $allocations[7]->hangar->name . '<br>Qty: ' . $allocations[7]->quantity . '<br>Remaining: ' . $allocations[7]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar9', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[8]) && $allocations[8]->hangar) {
                        return $allocations[8]->hangar->name . '<br>Qty: ' . $allocations[8]->quantity . '<br>Remaining: ' . $allocations[8]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar10', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[9]) && $allocations[9]->hangar) {
                        return $allocations[9]->hangar->name . '<br>Qty: ' . $allocations[9]->quantity . '<br>Remaining: ' . $allocations[9]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-material-stock btn btn-sm btn-success mr-1" data-id="'.$row->id.'" data-path="'.route('material-stock.edit', ['username' => request()->segment(1), 'material_stock' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-material-stock btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'farm', 'assignment_status', 'hangar1', 'hangar2', 'hangar3', 'hangar4', 'hangar5', 'hangar6', 'hangar7', 'hangar8', 'hangar9', 'hangar10'])
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

        $suppliers = ChicksSupplier::all();
        $materialNames = \App\Models\MaterialName::all();
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
            ->where('status', 'Active')
            ->select('id', 'name')
            ->get()
            ->map(function ($hangar) {
                $remaining = MaterialStockHangar::where('hangar_id', $hangar->id)
                    ->latest('created_at')
                    ->value('remaining_quantity') ?? 0;

                return [
                    'id' => $hangar->id,
                    'name' => $hangar->name,
                    'remaining' => number_format($remaining, 2, '.', ''),
                ];
            });

        return response()->json($hangars);
    }

    public function store(Request $request, $siteUrl)
    {
        $user = auth()->user();

        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'supplier_id' => 'required|exists:chicks_suppliers,id',
            'material_name_id' => 'required|exists:material_names,id',
            'stock_date' => 'required|date_format:Y-m-d',
            'quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => 'required|json',
        ]);

        $hangarQuantities = json_decode($request->hangar_quantities_json, true);

        if (empty($hangarQuantities)) {
            return back()->withErrors(['hangar_quantities_json' => 'Please select at least one hangar with quantity.']);
        }

        // Verify farm access before transaction
        $farm = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->find($request->farm_id);

        if (!$farm && $user->role !== 'SuperAdmin') {
            return back()->withErrors('Farm not found or access denied.');
        }

        // Validate all hangars belong to this farm and are active
        $farmHangarIds = Hangar::where('farm_id', $request->farm_id)
            ->where('status', 'Active')
            ->pluck('id')
            ->toArray();

        $requestHangarIds = array_column($hangarQuantities, 'hangar_id');
        $invalidHangars = array_diff($requestHangarIds, $farmHangarIds);

        if (!empty($invalidHangars)) {
            return back()->withErrors('One or more hangars do not belong to this farm or are not active.');
        }

        // Check for duplicate hangars
        if (count($requestHangarIds) !== count(array_unique($requestHangarIds))) {
            return back()->withErrors('Duplicate hangars are not allowed. Each hangar can only be selected once.');
        }

        // Validate total quantity matches allocations
        $totalAllocated = collect($hangarQuantities)->sum('quantity');
        if ((float)$totalAllocated != (float)$request->quantity) {
            return back()->withErrors('Hangar quantities must equal total quantity.');
        }

        try {
            DB::beginTransaction();

            $materialName = \App\Models\MaterialName::findOrFail($request->material_name_id);
            $stockDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->stock_date);

            $materialStock = MaterialStock::create([
                'farm_id' => $request->farm_id,
                'supplier_id' => $request->supplier_id,
                'material_name_id' => $request->material_name_id,
                'name' => $materialName->name,
                'stock_date' => $stockDate,
                'quantity' => (float)$request->quantity,
                'created_by' => auth()->id()
            ]);

            // Save hangar allocations with accumulated remaining quantity
            foreach ($hangarQuantities as $allocation) {
                // Get the current remaining quantity for this hangar
                $currentRemaining = MaterialStockHangar::where('hangar_id', $allocation['hangar_id'])
                    ->latest('created_at')
                    ->value('remaining_quantity') ?? 0;

                // Calculate new remaining: current_remaining + new_quantity
                $newRemaining = $currentRemaining + (float)$allocation['quantity'];

                MaterialStockHangar::create([
                    'material_stock_id' => $materialStock->id,
                    'hangar_id' => $allocation['hangar_id'],
                    'quantity' => (float)$allocation['quantity'],
                    'remaining_quantity' => $newRemaining
                ]);
            }

            DB::commit();
            Session::flash('successMsg', 'Feed Stock created successfully.');
            return redirect()->route('material-stock.index', ['username' => request()->segment(1)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Material stock creation error: ' . $e->getMessage());
            return back()->withErrors('Failed to create material stock: ' . $e->getMessage());
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

        $suppliers = ChicksSupplier::all();
        $materialNames = \App\Models\MaterialName::all();
        $materialStockHangars = MaterialStockHangar::where('material_stock_id', $materialStock->id)->get();
        $farms = $farms->get();

        return view('backend.material-stock.create', compact('materialStock', 'farms', 'suppliers', 'materialNames', 'materialStockHangars'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $user = auth()->user();

        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'supplier_id' => 'required|exists:chicks_suppliers,id',
            'material_name_id' => 'required|exists:material_names,id',
            'stock_date' => 'required|date_format:Y-m-d',
            'quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => 'required|json',
        ]);

        $materialStock = MaterialStock::findOrFail($id);

        // Verify user has access to the farm
        $farm = Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->find($request->farm_id);

        if (!$farm && $user->role !== 'SuperAdmin') {
            return back()->withErrors('Farm not found or access denied.');
        }

        $hangarQuantities = json_decode($request->hangar_quantities_json, true);

        if (empty($hangarQuantities)) {
            return back()->withErrors(['hangar_quantities_json' => 'Please select at least one hangar with quantity.']);
        }

        // Validate all hangars belong to this farm and are active
        $farmHangarIds = Hangar::where('farm_id', $request->farm_id)
            ->where('status', 'Active')
            ->pluck('id')
            ->toArray();

        $requestHangarIds = array_column($hangarQuantities, 'hangar_id');
        $invalidHangars = array_diff($requestHangarIds, $farmHangarIds);

        if (!empty($invalidHangars)) {
            return back()->withErrors('One or more hangars do not belong to this farm or are not active.');
        }

        // Check for duplicate hangars
        if (count($requestHangarIds) !== count(array_unique($requestHangarIds))) {
            return back()->withErrors('Duplicate hangars are not allowed. Each hangar can only be selected once.');
        }

        // Validate total quantity matches allocations
        $totalAllocated = collect($hangarQuantities)->sum('quantity');
        if ((float)$totalAllocated != (float)$request->quantity) {
            return back()->withErrors('Hangar quantities must equal total quantity.');
        }

        try {
            DB::beginTransaction();

            $materialName = \App\Models\MaterialName::findOrFail($request->material_name_id);
            $stockDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->stock_date);

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

            // Save new allocations with accumulated remaining quantity
            foreach ($hangarQuantities as $allocation) {
                // Get the current remaining quantity for this hangar
                $currentRemaining = MaterialStockHangar::where('hangar_id', $allocation['hangar_id'])
                    ->latest('created_at')
                    ->value('remaining_quantity') ?? 0;

                // Calculate new remaining: current_remaining + new_quantity
                $newRemaining = $currentRemaining + (float)$allocation['quantity'];

                MaterialStockHangar::create([
                    'material_stock_id' => $materialStock->id,
                    'hangar_id' => $allocation['hangar_id'],
                    'quantity' => (float)$allocation['quantity'],
                    'remaining_quantity' => $newRemaining
                ]);
            }

            DB::commit();
            Session::flash('successMsg', 'Feed Stock updated successfully.');
            return redirect()->route('material-stock.index', ['username' => request()->segment(1)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Material stock update error: ' . $e->getMessage());
            return back()->withErrors('Failed to update material stock: ' . $e->getMessage());
        }
    }

    public function destroy($siteUrl, $id)
    {
        try {
            DB::beginTransaction();
            $materialStock = MaterialStock::findOrFail($id);
            $materialStock->materialStockHangarAllocations()->delete();
            $materialStock->delete();
            DB::commit();
            return response()->json(['msg' => 'Material Stock deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Material stock deletion error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete material stock: ' . $e->getMessage()], 500);
        }
    }
}
