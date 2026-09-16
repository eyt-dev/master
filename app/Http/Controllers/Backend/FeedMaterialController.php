<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaterialName;
use Illuminate\Support\Facades\Session;

class FeedMaterialController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MaterialName::with('creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-feed-material btn btn-sm btn-success" data-path="'.route('feedmaterial.edit', ['username' => request()->segment(1),  'feedmaterial' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-feed-material btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.feedmaterial.index');
    }

    public function create()
    {
        return view('backend.feedmaterial.create');
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:material_names',
            'type' => 'required|in:Pelleted feed,Feedstuff',
        ]);

        $createData = [
            'name' => $request->name,
            'type' => $request->type,
            'created_by' => auth()->id()
        ];
        MaterialName::create($createData);

        Session::flash('successMsg', 'Feed Material created successfully.');
        return redirect()->route('feedmaterial.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $feedmaterial = MaterialName::findOrFail($id);
        return view('backend.feedmaterial.create', compact('feedmaterial'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $feedmaterial = MaterialName::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|unique:material_names,name,' . $id,
            'type' => 'required|in:Pelleted feed,Feedstuff',
        ]);
        $feedmaterial->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        Session::flash('successMsg', 'Feed Material updated successfully.');
        return redirect()->route('feedmaterial.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        MaterialName::findOrFail($id)->delete();
        return response()->json(['msg' => 'Feed Material deleted successfully.']);
    }
}
