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

class MaterialStockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MaterialStock::with('farm', 'supplier', 'creator', 'materialStockHangarAllocations.hangar')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('stock_date', function($row) {
                    return date('Y-m-d', strtotime($row->stock_date));
                })
                ->addColumn('farm', function($row) {
                    return $row->farm->name ?? 'N/A';
                })
                ->addColumn('supplier', function($row) {
                    return $row->supplier->name ?? 'N/A';
                })
                ->addColumn('created_by', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('hangar1', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[0])) {
                        return $allocations[0]->hangar->name . '<br>Qty: ' . $allocations[0]->quantity . '<br>Remaining: ' . $allocations[0]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar2', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[1])) {
                        return $allocations[1]->hangar->name . '<br>Qty: ' . $allocations[1]->quantity . '<br>Remaining: ' . $allocations[1]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar3', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[2])) {
                        return $allocations[2]->hangar->name . '<br>Qty: ' . $allocations[2]->quantity . '<br>Remaining: ' . $allocations[2]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar4', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[3])) {
                        return $allocations[3]->hangar->name . '<br>Qty: ' . $allocations[3]->quantity . '<br>Remaining: ' . $allocations[3]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar5', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[4])) {
                        return $allocations[4]->hangar->name . '<br>Qty: ' . $allocations[4]->quantity . '<br>Remaining: ' . $allocations[4]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar6', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[5])) {
                        return $allocations[5]->hangar->name . '<br>Qty: ' . $allocations[5]->quantity . '<br>Remaining: ' . $allocations[5]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar7', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[6])) {
                        return $allocations[6]->hangar->name . '<br>Qty: ' . $allocations[6]->quantity . '<br>Remaining: ' . $allocations[6]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar8', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[7])) {
                        return $allocations[7]->hangar->name . '<br>Qty: ' . $allocations[7]->quantity . '<br>Remaining: ' . $allocations[7]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar9', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[8])) {
                        return $allocations[8]->hangar->name . '<br>Qty: ' . $allocations[8]->quantity . '<br>Remaining: ' . $allocations[8]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('hangar10', function($row) {
                    $allocations = $row->materialStockHangarAllocations;
                    if (isset($allocations[9])) {
                        return $allocations[9]->hangar->name . '<br>Qty: ' . $allocations[9]->quantity . '<br>Remaining: ' . $allocations[9]->remaining_quantity;
                    }
                    return 'N/A';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-material-stock btn btn-sm btn-success mr-1" data-id="'.$row->id.'" data-path="'.route('material-stock.edit', ['username' => request()->segment(1), 'material_stock' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-material-stock btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'hangar1', 'hangar2', 'hangar3', 'hangar4', 'hangar5', 'hangar6', 'hangar7', 'hangar8', 'hangar9', 'hangar10'])   
                ->make(true);
        }
        return view('backend.material-stock.index');
    }

    public function create()
    {
        $farms = Farm::where('created_by', auth()->id())->orWhere('created_by', function($query) {
            $query->select('id')->from('admins')->where('type', 0);
        })->get();
        
        if (auth()->user()->role === 'SuperAdmin') {
            $farms = Farm::all();
        }

        $suppliers = ChicksSupplier::all();
        return view('backend.material-stock.create', compact('farms', 'suppliers'));
    }

    public function getHangarsByFarm($siteUrl, $farmId)
    {        
        $farmId = (int) $farmId;
        $hangars = Hangar::where('farm_id', $farmId)
            ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->select('id', 'name')
            ->get();
        
        return response()->json($hangars);
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'supplier_id' => 'required|exists:chicks_suppliers,id',
            'name' => 'required|string',
            'stock_date' => 'required|date',
            'quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => 'required|json',
        ]);

        $hangarQuantities = json_decode($request->hangar_quantities_json, true);

        if (empty($hangarQuantities)) {
            return back()->withErrors(['hangar_quantities_json' => 'Please select at least one hangar with quantity.']);
        }

        // Check for duplicate hangars in the submission
        $hangarIds = array_column($hangarQuantities, 'hangar_id');
        if (count($hangarIds) !== count(array_unique($hangarIds))) {
            return back()->withErrors(['hangar_quantities_json' => 'Duplicate hangars are not allowed. Each hangar can only be selected once.']);
        }

        $materialStock = MaterialStock::create([
            'farm_id' => $request->farm_id,
            'supplier_id' => $request->supplier_id,
            'name' => $request->name,
            'stock_date' => $request->stock_date,
            'quantity' => $request->quantity,
            'created_by' => auth()->id()
        ]);

        // Save hangar allocations
        foreach ($hangarQuantities as $allocation) {
            MaterialStockHangar::create([
                'material_stock_id' => $materialStock->id,
                'hangar_id' => $allocation['hangar_id'],
                'quantity' => $allocation['quantity'],
                'remaining_quantity' => $allocation['remaining_quantity'] ?? $allocation['quantity']
            ]);
        }

        Session::flash('successMsg', 'Material Stock created successfully.');
        return redirect()->route('material-stock.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $materialStock = MaterialStock::findOrFail($id);
        $farms = Farm::where('created_by', auth()->id())->orWhere('created_by', function($query) {
            $query->select('id')->from('admins')->where('type', 0);
        })->get();
        
        if (auth()->user()->role === 'SuperAdmin') {
            $farms = Farm::all();
        }

        $suppliers = ChicksSupplier::all();
        $materialStockHangars = MaterialStockHangar::where('material_stock_id', $materialStock->id)->get();
        return view('backend.material-stock.create', compact('materialStock', 'farms', 'suppliers', 'materialStockHangars'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'supplier_id' => 'required|exists:chicks_suppliers,id',
            'name' => 'required|string',
            'stock_date' => 'required|date',
            'quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => 'required|json',
        ]);

        $hangarQuantities = json_decode($request->hangar_quantities_json, true);

        if (empty($hangarQuantities)) {
            return back()->withErrors(['hangar_quantities_json' => 'Please select at least one hangar with quantity.']);
        }

        // Check for duplicate hangars in the submission
        $hangarIds = array_column($hangarQuantities, 'hangar_id');
        if (count($hangarIds) !== count(array_unique($hangarIds))) {
            return back()->withErrors(['hangar_quantities_json' => 'Duplicate hangars are not allowed. Each hangar can only be selected once.']);
        }

        $materialStock = MaterialStock::findOrFail($id);
        $materialStock->update([
            'farm_id' => $request->farm_id,
            'supplier_id' => $request->supplier_id,
            'name' => $request->name,
            'stock_date' => $request->stock_date,
            'quantity' => $request->quantity,
        ]);

        // Delete old allocations
        MaterialStockHangar::where('material_stock_id', $materialStock->id)->delete();

        // Save new allocations
        foreach ($hangarQuantities as $allocation) {
            MaterialStockHangar::create([
                'material_stock_id' => $materialStock->id,
                'hangar_id' => $allocation['hangar_id'],
                'quantity' => $allocation['quantity'],
                'remaining_quantity' => $allocation['remaining_quantity'] ?? $allocation['quantity']
            ]);
        }

        Session::flash('successMsg', 'Material Stock updated successfully.');
        return redirect()->route('material-stock.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        MaterialStock::findOrFail($id)->delete();
        return response()->json(['msg' => 'Material Stock deleted successfully.']);
    }
}
