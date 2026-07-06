<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flock;
use App\Models\Farm;
use App\Models\ChicksSupplier;
use App\Models\Hangar;
use App\Models\FlockHangar;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;

class FlockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Flock::with('farm', 'chicksSupplier', 'creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('farm', function($row) {
                    return $row->farm->name ?? 'N/A';
                })
                ->addColumn('chicks_supplier', function($row) {
                    return $row->chicksSupplier->name ?? 'N/A';
                })
                ->addColumn('start_date', function($row) {
                    return date('Y-m-d', strtotime($row->start_date));
                })
                ->addColumn('created_by', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-flock btn btn-sm btn-success mr-1" data-path="'.route('flock.edit', ['username' => request()->segment(1), 'flock' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-flock btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])   
                ->make(true);
        }
        return view('backend.flock.index');
    }

    public function create()
    {
        $farms = Farm::where('created_by', auth()->id())->orWhere('created_by', function($query) {
            $query->select('id')->from('admins')->where('type', 0);
        })->get();
        
        if (auth()->user()->role === 'SuperAdmin') {
            $farms = Farm::all();
        }

        $chicksSuppliers = ChicksSupplier::all();
        return view('backend.flock.create', compact('farms', 'chicksSuppliers'));
    }

    public function getHangarsByFarm($siteUrl, $farmId)
    {        
        // Query hangars for the selected farm
        // Apply the same scoping as HangarController for non-SuperAdmins
        $hangars = Hangar::where('farm_id', $farmId)
            ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                // For non-SuperAdmin, show only hangars they created
                $query->where('created_by', auth()->id());
            })
            ->select('id', 'name')
            ->get();
        
        return response()->json($hangars);
    }

    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'chicks_supplier_id' => 'required|exists:chicks_suppliers,id',
            'breed' => 'required|string',
            'start_date' => 'required|date',
        ]);

        $exists = Flock::where('farm_id', $request->farm_id)
            ->where('chicks_supplier_id', $request->chicks_supplier_id)
            ->where('breed', $request->breed)
            ->where('start_date', $request->start_date)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'chicks_supplier_id' => 'required|exists:chicks_suppliers,id',
            'breed' => 'required|string',
            'start_date' => 'required|date',
            'hangar_quantities_json' => 'required|json',
        ]);

        // Check if a flock with the same farm, chicks_supplier, breed, and start_date already exists
        $existingFlock = Flock::where('farm_id', $request->farm_id)
            ->where('chicks_supplier_id', $request->chicks_supplier_id)
            ->where('breed', $request->breed)
            ->where('start_date', $request->start_date)
            ->first();

        if ($existingFlock) {
            return back()->withErrors(['unique_combination' => 'A flock with the same Farm, Chicks Supplier, Breed, and Start Date already exists.']);
        }

        $hangarQuantities = json_decode($request->hangar_quantities_json, true);

        if (empty($hangarQuantities)) {
            return back()->withErrors(['hangar_quantities_json' => 'Please select at least one hangar with quantity.']);
        }

        // Check for duplicate hangars in the submission
        $hangarIds = array_column($hangarQuantities, 'hangar_id');
        if (count($hangarIds) !== count(array_unique($hangarIds))) {
            return back()->withErrors(['hangar_quantities_json' => 'Duplicate hangars are not allowed. Each hangar can only be selected once.']);
        }

        // Calculate total quantity from hangar allocations
        $totalQuantity = collect($hangarQuantities)->sum('quantity');

        $flock = Flock::create([
            'farm_id' => $request->farm_id,
            'chicks_supplier_id' => $request->chicks_supplier_id,
            'breed' => $request->breed,
            'start_date' => $request->start_date,
            'total_quantity' => $totalQuantity,
            'created_by' => auth()->id()
        ]);

        // Save hangar allocations
        foreach ($hangarQuantities as $allocation) {
            FlockHangar::create([
                'flock_id' => $flock->id,
                'hangar_id' => $allocation['hangar_id'],
                'quantity' => $allocation['quantity']
            ]);
        }

        Session::flash('successMsg', 'Flock created successfully.');
        return redirect()->route('flock.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $flock = Flock::findOrFail($id);
        $farms = Farm::where('created_by', auth()->id())->orWhere('created_by', function($query) {
            $query->select('id')->from('admins')->where('type', 0);
        })->get();
        
        if (auth()->user()->role === 'SuperAdmin') {
            $farms = Farm::all();
        }

        $chicksSuppliers = ChicksSupplier::all();
        $flockHangars = FlockHangar::where('flock_id', $flock->id)->get();
        return view('backend.flock.create', compact('flock', 'farms', 'chicksSuppliers', 'flockHangars'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'chicks_supplier_id' => 'required|exists:chicks_suppliers,id',
            'breed' => 'required|string',
            'start_date' => 'required|date',
            'hangar_quantities_json' => 'required|json',
        ]);

        $flock = Flock::findOrFail($id);

        // Check if another flock with the same farm, chicks_supplier, breed, and start_date exists (exclude current flock)
        $existingFlock = Flock::where('farm_id', $request->farm_id)
            ->where('chicks_supplier_id', $request->chicks_supplier_id)
            ->where('breed', $request->breed)
            ->where('start_date', $request->start_date)
            ->where('id', '!=', $id)
            ->first();

        if ($existingFlock) {
            return back()->withErrors(['unique_combination' => 'A flock with the same Farm, Chicks Supplier, Breed, and Start Date already exists.']);
        }

        $hangarQuantities = json_decode($request->hangar_quantities_json, true);

        if (empty($hangarQuantities)) {
            return back()->withErrors(['hangar_quantities_json' => 'Please select at least one hangar with quantity.']);
        }

        // Check for duplicate hangars in the submission
        $hangarIds = array_column($hangarQuantities, 'hangar_id');
        if (count($hangarIds) !== count(array_unique($hangarIds))) {
            return back()->withErrors(['hangar_quantities_json' => 'Duplicate hangars are not allowed. Each hangar can only be selected once.']);
        }

        // Calculate total quantity from hangar allocations
        $totalQuantity = collect($hangarQuantities)->sum('quantity');

        $flock->update([
            'farm_id' => $request->farm_id,
            'chicks_supplier_id' => $request->chicks_supplier_id,
            'breed' => $request->breed,
            'start_date' => $request->start_date,
            'total_quantity' => $totalQuantity,
        ]);

        // Delete old allocations
        FlockHangar::where('flock_id', $flock->id)->delete();

        // Save new allocations
        foreach ($hangarQuantities as $allocation) {
            FlockHangar::create([
                'flock_id' => $flock->id,
                'hangar_id' => $allocation['hangar_id'],
                'quantity' => $allocation['quantity']
            ]);
        }

        Session::flash('successMsg', 'Flock updated successfully.');
        return redirect()->route('flock.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Flock::findOrFail($id)->delete();
        return response()->json(['msg' => 'Flock deleted successfully.']);
    }
}
