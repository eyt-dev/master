<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\StoreView;
use App\Models\CountryRegion;
use Illuminate\Support\Facades\Session;

class StoreViewController extends Controller
{
    public function index(Request $request, $siteUrl)
    {
        $regions = CountryRegion::all()->mapWithKeys(function ($country) {
            return [$country->id => "{$country->name} / {$country->region}"];
        });

        if ($request->ajax()) {
            $data = StoreView::when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            })->orderBy('created_at', 'desc')->get();
            return datatables()
                ->of($data)
                ->addColumn('creator', function($row) {
                    // dump($row->creator->name);
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', 'store_view.action')
                ->addColumn('action', function($row){
                    $btn = '';
                        $btn = $btn.'<a class="edit-store_view edit_form btn btn-sm btn-success btn-icon mr-1 white" data-path="'.route('store_view.edit', ['site' => request()->segment(1), 'store_view' => $row->id]).'" data-name="'.$row->name.'" data-id='.$row->id.' title="Edit"> <i class="fa fa-edit fa-1x"></i> </a>';
                        $btn = $btn.'<a class="btn btn-sm btn-icon btn-danger mr-1 white delete-store_view" data-id="'.$row->id.'" title="Delete"> <i class="fa fa-trash fa-1x"></i> </a>';
                    return $btn;
                })
                ->addIndexColumn()
                ->make(true);
        }

        return view('backend.store_view.index', ['regions' => $regions]);
    }

    public function create($siteUrl)
    {
        $regions = CountryRegion::all()->mapWithKeys(function ($country) {
            return [$country->id => "{$country->name} / {$country->region}"];
        });

        return view('backend.store_view.create', ['regions' => $regions]);
    }

    public function store(Request $request, $siteUrl)
    {      
        $request->validate([
            'region' => 'required',
            'language' => 'required',
            'slug' => 'required|unique:store_views,slug',
            'status' => 'required|in:active,inactive',
        ]);
    
        $storeView = StoreView::create([ 
            'region' => $request->region,
            'language' => $request->language,
            'slug' => $request->slug,
            'description' => $request->description,
            'meta_data' => $request->meta_data,
            'meta_keywords' => $request->meta_keywords,
            'status' => $request->status,
            'created_by' => auth()->user()->id,
        ]);

        if(!$storeView) {
            Session::flash('errorMsg', 'Something went wrong.');
            return redirect()->route('store_view.index', ['site' => request()->segment(1)]);
        }

        Session::flash('successMsg', 'Store View inserted successfully.');
        return redirect()->route('store_view.index', ['site' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $store_view = StoreView::find($id);

        $regions = CountryRegion::all()->mapWithKeys(function ($country) {
            return [$country->id => "{$country->name} / {$country->region}"];
        });

        if (empty($store_view)) {
            return redirect()->route('store_view.index', ['site' => request()->segment(1)]);
        }
       
        return view('backend.store_view.create', ['store_view' => $store_view, 'regions' => $regions]);
    }

    public function update(Request $request, $siteUrl, $id)
    {      
        $request->validate([
            'region' => 'required',
            'language' => 'required',
            'slug' => 'required|unique:store_views,slug,' . $id,
            'status' => 'required|in:active,inactive',
        ]);

        $storeView = StoreView::findOrFail($id);
        
        $storeView->update([
            'region' => $request->region,
            'language' => $request->language,
            'slug' => $request->slug,
            'description' => $request->description,
            'meta_data' => $request->meta_data,
            'meta_keywords' => $request->meta_keywords,
            'status' => $request->status,
            'updated_by' => auth()->user()->id,
        ]);

        Session::flash('successMsg', 'Store View updated successfully.');
        return redirect()->route('store_view.index', ['site' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        $storeView = StoreView::find($id)->delete();

        if($storeView)
            return response()->json(['msg' => 'Deleted successfully!']);

        return response()->json(['msg' => 'Something went wrong, Please try again'],500);
    }
}
