<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slaughter;
use Illuminate\Support\Facades\Session;

class SlaughterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Slaughter::with('creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-slaughter btn btn-sm btn-success" data-path="'.route('slaughter.edit', ['username' => request()->segment(1),  'slaughter' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-slaughter btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])   
                ->make(true);
        }
        return view('backend.slaughter.index');
    }

    public function create()
    {
        return view('backend.slaughter.create');
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'address' => 'required',
            'contact_person' => 'required',
            'mobile_number' => 'required',
        ]);

        $createData = [
            'name' => $request->name,
            'location' => $request->location,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'mobile_number' => $request->mobile_number,
            'created_by' => auth()->id()
        ];
        Slaughter::create($createData);

        Session::flash('successMsg', 'Slaughter created successfully.');
        return redirect()->route('slaughter.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $slaughter = Slaughter::findOrFail($id);
        return view('backend.slaughter.create', compact('slaughter'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'address' => 'required',
            'contact_person' => 'required',
            'mobile_number' => 'required',
        ]);

        $slaughter = Slaughter::findOrFail($id);
        $slaughter->update([
            'name' => $request->name,
            'location' => $request->location,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'mobile_number' => $request->mobile_number,
        ]);

        Session::flash('successMsg', 'Slaughter updated successfully.');
        return redirect()->route('slaughter.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Slaughter::findOrFail($id)->delete();
        return response()->json(['msg' => 'Slaughter deleted successfully.']);
    }
}
