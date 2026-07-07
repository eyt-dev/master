<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChickenSale;
use App\Models\Farm;
use App\Models\Flock;
use App\Models\Hangar;
use App\Models\Slaughter;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;

class ChickenSalesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ChickenSale::with('farm', 'flock', 'hangar', 'slaughter', 'creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('sale_date', function($row) {
                    return date('Y-m-d', strtotime($row->sale_date));
                })
                ->addColumn('farm', function($row) {
                    return $row->farm->name ?? 'N/A';
                })
                ->addColumn('flock', function($row) {
                    return $row->flock->breed ?? 'N/A';
                })
                ->addColumn('hangar', function($row) {
                    return $row->hangar->name ?? 'N/A';
                })
                ->addColumn('slaughter', function($row) {
                    return $row->slaughter->name ?? 'N/A';
                })
                ->addColumn('net_weight', function($row) {
                    return $row->net_weight;
                })
                ->addColumn('avg_weight_per_bird', function($row) {
                    return round($row->avg_weight_per_bird, 2);
                })
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-chicken-sale btn btn-sm btn-success mr-1" data-path="'.route('chicken-sale.edit', ['username' => request()->segment(1), 'chicken_sale' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-chicken-sale btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])   
                ->make(true);
        }
        return view('backend.chicken-sale.index');
    }

    public function create()
    {
        $farms = Farm::where('created_by', auth()->id())->orWhere('created_by', function($query) {
            $query->select('id')->from('admins')->where('type', 0);
        })->get();
        
        if (auth()->user()->role === 'SuperAdmin') {
            $farms = Farm::all();
        }

        $slaughters = Slaughter::all();
        return view('backend.chicken-sale.create', compact('farms', 'slaughters'));
    }

    public function getFlocksByFarm($siteUrl, $farmId)
    {
        $flocks = Flock::where('farm_id', $farmId)->get();
        return response()->json($flocks);
    }

    public function getHangarsByFlock($siteUrl, $flockId)
    {
        $hangars = Hangar::whereHas('flocks', function($query) use ($flockId) {
            $query->where('flock_id', $flockId);
        })->get();
        return response()->json($hangars);
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'sale_date' => 'required|date',
            'farm_id' => 'required|exists:farms,id',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_id' => 'required|exists:hangars,id',
            'slaughter_id' => 'required|exists:slaughters,id',
            'quantity' => 'required|numeric|min:1',
            'total_weight' => 'required|numeric|min:0',
            'gross_weight' => 'required|numeric|min:0',
            'no_of_cages' => 'required|integer|min:1',
            'no_of_birds' => 'required|numeric|min:1|unique:chicken_sales',
            'net_weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $avgWeightPerBird = $request->net_weight / $request->no_of_birds;

        ChickenSale::create([
            'sale_date' => $request->sale_date,
            'farm_id' => $request->farm_id,
            'flock_id' => $request->flock_id,
            'hangar_id' => $request->hangar_id,
            'slaughter_id' => $request->slaughter_id,
            'quantity' => $request->quantity,
            'total_weight' => $request->total_weight,
            'gross_weight' => $request->gross_weight,
            'no_of_cages' => $request->no_of_cages,
            'no_of_birds' => $request->no_of_birds,
            'net_weight' => $request->net_weight,
            'avg_weight_per_bird' => $avgWeightPerBird,
            'notes' => $request->notes,
            'created_by' => auth()->id()
        ]);

        Session::flash('successMsg', 'Chicken sale created successfully.');
        return redirect()->route('chicken-sale.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $chickenSale = ChickenSale::findOrFail($id);
        $farms = Farm::where('created_by', auth()->id())->orWhere('created_by', function($query) {
            $query->select('id')->from('admins')->where('type', 0);
        })->get();
        
        if (auth()->user()->role === 'SuperAdmin') {
            $farms = Farm::all();
        }

        $flocks = Flock::where('farm_id', $chickenSale->farm_id)->get();
        $hangars = Hangar::whereHas('flocks', function($query) use ($chickenSale) {
            $query->where('flock_id', $chickenSale->flock_id);
        })->get();
        $slaughters = Slaughter::all();

        return view('backend.chicken-sale.create', compact('chickenSale', 'farms', 'flocks', 'hangars', 'slaughters'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $chickenSale = ChickenSale::findOrFail($id);

        $request->validate([
            'sale_date' => 'required|date',
            'farm_id' => 'required|exists:farms,id',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_id' => 'required|exists:hangars,id',
            'slaughter_id' => 'required|exists:slaughters,id',
            'quantity' => 'required|numeric|min:1',
            'total_weight' => 'required|numeric|min:0',
            'gross_weight' => 'required|numeric|min:0',
            'no_of_cages' => 'required|integer|min:1',
            'no_of_birds' => 'required|numeric|min:1|unique:chicken_sales,no_of_birds,' . $id,
            'net_weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $avgWeightPerBird = $request->net_weight / $request->no_of_birds;

        $chickenSale->update([
            'sale_date' => $request->sale_date,
            'farm_id' => $request->farm_id,
            'flock_id' => $request->flock_id,
            'hangar_id' => $request->hangar_id,
            'slaughter_id' => $request->slaughter_id,
            'quantity' => $request->quantity,
            'total_weight' => $request->total_weight,
            'gross_weight' => $request->gross_weight,
            'no_of_cages' => $request->no_of_cages,
            'no_of_birds' => $request->no_of_birds,
            'net_weight' => $request->net_weight,
            'avg_weight_per_bird' => $avgWeightPerBird,
            'notes' => $request->notes,
        ]);

        Session::flash('successMsg', 'Chicken sale updated successfully.');
        return redirect()->route('chicken-sale.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        ChickenSale::findOrFail($id)->delete();
        return response()->json(['msg' => 'Chicken sale deleted successfully.']);
    }
}
