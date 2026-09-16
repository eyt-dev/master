<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeedSupplier;
use Illuminate\Support\Facades\Session;

class FeedSupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = FeedSupplier::with('creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-feed-supplier btn btn-sm btn-success" data-path="'.route('feed-supplier.edit', ['username' => request()->segment(1),  'feed_supplier' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-feed-supplier btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.feed-supplier.index');
    }

    public function create()
    {
        return view('backend.feed-supplier.create');
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
        FeedSupplier::create($createData);

        Session::flash('successMsg', 'Feed Supplier created successfully.');
        return redirect()->route('feed-supplier.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $feed_supplier = FeedSupplier::findOrFail($id);
        return view('backend.feed-supplier.create', compact('feed_supplier'));
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

        $feed_supplier = FeedSupplier::findOrFail($id);
        $feed_supplier->update([
            'name' => $request->name,
            'location' => $request->location,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'mobile_number' => $request->mobile_number,
        ]);

        Session::flash('successMsg', 'Feed Supplier updated successfully.');
        return redirect()->route('feed-supplier.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        FeedSupplier::findOrFail($id)->delete();
        return response()->json(['msg' => 'Feed Supplier deleted successfully.']);
    }
}
