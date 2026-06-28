<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hangar;
use App\Models\Farm;
use Illuminate\Support\Facades\Session;

class HangarController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Hangar::with('farm', 'creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('farm_name', function($row) {
                    return $row->farm->name ?? 'N/A';
                })
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-hangar btn btn-sm btn-success" data-path="'.route('hangar.edit', ['username' => request()->segment(1),  'hangar' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-hangar btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])   
                ->make(true);
        }
        return view('backend.hangar.index');
    }

    public function create()
    {
        $farms = Farm::get();
        return view('backend.hangar.create', compact('farms'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'farm_id' => 'required',
            'area_sqm' => 'required|numeric',
            'layer_hens' => 'required|integer',
            'broiler_hens' => 'required|integer',
        ]);

        $createData = [
            'farm_id' => $request->farm_id,
            'area_sqm' => $request->area_sqm,
            'layer_hens' => $request->layer_hens,
            'broiler_hens' => $request->broiler_hens,
            'created_by' => auth()->id()
        ];
        Hangar::create($createData);

        Session::flash('successMsg', 'Hangar created successfully.');
        return redirect()->route('hangar.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $hangar = Hangar::findOrFail($id);
        $farms = Farm::get();
        return view('backend.hangar.create', compact('hangar', 'farms'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'farm_id' => 'required',
            'area_sqm' => 'required|numeric',
            'layer_hens' => 'required|integer',
            'broiler_hens' => 'required|integer',
        ]);

        $hangar = Hangar::findOrFail($id);
        $hangar->update([
            'farm_id' => $request->farm_id,
            'area_sqm' => $request->area_sqm,
            'layer_hens' => $request->layer_hens,
            'broiler_hens' => $request->broiler_hens,
        ]);

        Session::flash('successMsg', 'Hangar updated successfully.');
        return redirect()->route('hangar.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Hangar::findOrFail($id)->delete();
        return response()->json(['msg' => 'Hangar deleted successfully.']);
    }
}
