<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\StoreView;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Category::with('storeView')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('store_view', function($row) {
                    return ($row->storeView->region . ' - ' . $row->storeView->language) ?? 'N/A';
                })
                ->addColumn('image', function($row) {
                    if($row->image) {
                        $path = asset('storage/'.$row->image);
                        return '<img src="'.$path.'" alt="Category Image" class="img-thumbnail" width="100" />';
                    }
                })
                ->addColumn('creator', function($row) {
                    // dump($row->creator->name);
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-category btn btn-sm btn-success" data-path="'.route('category.edit', ['site' => $siteSlug,  'category' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-category btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['image', 'action'])   
                ->make(true);
        }
        return view('backend.category.index');
    }

    public function create()
    {
        $store_views = StoreView::get();
        return view('backend.category.create', compact('store_views'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'store_view' => 'required',
            'title' => 'required',
            'description' => 'required',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $createData=[
            'store_view_id' => $request->store_view,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath ?? "",
            'created_by' => auth()->id()
        ];
        Category::create($createData);

        Session::flash('successMsg', 'Category created successfully.');
        return redirect()->route('category.index', ['site' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $category = Category::findOrFail($id);
        $store_views = StoreView::get();
        return view('backend.category.create', compact('category', 'store_views'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'store_view' => 'required',
            'title' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $category = Category::findOrFail($id);
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            if ($category->image && \Storage::disk('public')->exists($category->image)) {
                \Storage::disk('public')->delete($category->image);
            }
            $category->image = $imagePath;
        }

        $category->update([
            'store_view_id' => $request->store_view,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        Session::flash('successMsg', 'Category updated successfully.');
        return redirect()->route('category.index', ['site' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['msg' => 'Category deleted successfully.']);
    }
}
