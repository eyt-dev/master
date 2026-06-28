<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FeedMill;
use Illuminate\Support\Facades\Session;

class FeedMillController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = FeedMill::with('creator')
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
                    return '<a class="edit-feed-mill btn btn-sm btn-success" data-path="'.route('feed-mill.edit', ['username' => request()->segment(1),  'feed_mill' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-feed-mill btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])   
                ->make(true);
        }
        return view('backend.feed-mill.index');
    }

    public function create()
    {
        return view('backend.feed-mill.create');
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
        FeedMill::create($createData);

        Session::flash('successMsg', 'Feed Mill created successfully.');
        return redirect()->route('feed-mill.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $feed_mill = FeedMill::findOrFail($id);
        return view('backend.feed-mill.create', compact('feed_mill'));
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

        $feed_mill = FeedMill::findOrFail($id);
        $feed_mill->update([
            'name' => $request->name,
            'location' => $request->location,
            'address' => $request->address,
            'contact_person' => $request->contact_person,
            'mobile_number' => $request->mobile_number,
        ]);

        Session::flash('successMsg', 'Feed Mill updated successfully.');
        return redirect()->route('feed-mill.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        FeedMill::findOrFail($id)->delete();
        return response()->json(['msg' => 'Feed Mill deleted successfully.']);
    }
}
