<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;

class FarmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Farm::with('assignedAdmin', 'creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('assigned_admin', function($row) {
                    return $row->assignedAdmin->name ?? 'N/A';
                })
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-farm btn btn-sm btn-success mr-1" data-path="'.route('farm.edit', ['username' => request()->segment(1),  'farm' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-farm btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])   
                ->make(true);
        }
        return view('backend.farm.index');
    }

    public function create()
    {
        $admins = Admin::where('type', 1)->get();
        return view('backend.farm.create', compact('admins'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'number_of_hangars' => 'required|numeric|min:1',
            'assigned_to' => 'required',
        ]);

        $createData = [
            'name' => $request->name,
            'location' => $request->location,
            'number_of_hangars' => $request->number_of_hangars,
            'assigned_to' => $request->assigned_to,
            'created_by' => auth()->id()
        ];
        Farm::create($createData);

        Session::flash('successMsg', 'Farm created successfully.');
        return redirect()->route('farm.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $farm = Farm::findOrFail($id);
        $admins = Admin::where('type', 1)->get();
        return view('backend.farm.create', compact('farm', 'admins'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'number_of_hangars' => 'required|numeric|min:1',
            'assigned_to' => 'required',
        ]);

        $farm = Farm::findOrFail($id);
        $farm->update([
            'name' => $request->name,
            'location' => $request->location,
            'number_of_hangars' => $request->number_of_hangars,
            'assigned_to' => $request->assigned_to,
        ]);

        Session::flash('successMsg', 'Farm updated successfully.');
        return redirect()->route('farm.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Farm::findOrFail($id)->delete();
        return response()->json(['msg' => 'Farm deleted successfully.']);
    }
}
