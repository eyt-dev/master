<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnitRequest;
use App\Http\Requests\UpdateUnitRequest;
use App\Models\Unit;
use Illuminate\Support\Facades\Session;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Unit::when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('action', function ($row) {
                    return '<a class="edit-unit btn btn-sm btn-success" data-path="' . route('unit.edit', $row->id) . '" title="Edit" style="margin-right: 5px;">
                        <i class="fa fa-edit"></i></a>'
                        . '<a class="delete-unit btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete" style="margin-right: 5px;">
                        <i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }

        $units = Unit::orderBy('id', 'desc')->paginate(10);

        return view('unit.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('unit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnitRequest $request)
    {
        $data = $request->validated();

        Unit::create($data);

        Session::flash('successMsg', 'Unit created successfully.');

        return redirect()->route('unit.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('unit.create', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnitRequest $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $data = $request->validated();

        $unit->update($data);

        Session::flash('successMsg', 'Unit updated successfully.');

        return redirect()->route('unit.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Unit::findOrFail($id)->delete();

        return response()->json(['msg' => 'Unit deleted successfully.']);
    }
}
