<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChicksSupplier;
use Illuminate\Support\Facades\Session;

class ChicksSupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ChicksSupplier::with('creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-chicks-supplier btn btn-sm btn-success" data-path="'.route('chicks-supplier.edit', ['username' => request()->segment(1),  'chicks_supplier' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-chicks-supplier btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])   
                ->make(true);
        }
        return view('backend.chicks-supplier.index');
    }

    public function create()
    {
        $breeds = ['Ross', 'Cobb', 'Lohmann White', 'Lohmann Brown'];
        return view('backend.chicks-supplier.create', compact('breeds'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required',
            'breed' => 'required',
            'location' => 'required',
            'address' => 'required',
            'contact_person' => 'required',
            'mobile_number' => 'required',
        ]);

        $createData = [
            'name' => $request->name,
            'breed' => $request->breed,
            'location' => $request->location,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'mobile_number' => $request->mobile_number,
            'created_by' => auth()->id()
        ];
        ChicksSupplier::create($createData);

        Session::flash('successMsg', 'Chicks Supplier created successfully.');
        return redirect()->route('chicks-supplier.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $chicks_supplier = ChicksSupplier::findOrFail($id);
        $breeds = ['Ross', 'Cobb', 'Lohmann White', 'Lohmann Brown'];
        return view('backend.chicks-supplier.create', compact('chicks_supplier', 'breeds'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'name' => 'required',
            'breed' => 'required',
            'location' => 'required',
            'address' => 'required',
            'contact_person' => 'required',
            'mobile_number' => 'required',
        ]);

        $chicks_supplier = ChicksSupplier::findOrFail($id);
        $chicks_supplier->update([
            'name' => $request->name,
            'breed' => $request->breed,
            'location' => $request->location,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'mobile_number' => $request->mobile_number,
        ]);

        Session::flash('successMsg', 'Chicks Supplier updated successfully.');
        return redirect()->route('chicks-supplier.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        ChicksSupplier::findOrFail($id)->delete();
        return response()->json(['msg' => 'Chicks Supplier deleted successfully.']);
    }
}
