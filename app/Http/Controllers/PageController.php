<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Page;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class PageController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Page::with('category')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            // dd($data);
            return datatables()->of($data)
                ->addColumn('category', function($row) {
                    // dump($row->category->title);
                    return $row->category->title ?? 'N/A';
                })
                ->addColumn('creator', function($row) {
                    // dump($row->creator->name);
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-page btn btn-sm btn-success" data-path="'.route('page.edit', $row->id).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-page btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->make(true);
        }
        
        return view('page.index', ['categories' => Category::all()]);
    }

    public function create()
    {
        return view('page.create', ['categories' => Category::all()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'title' => 'required',
            'path' => 'required|url|unique:pages,path',
            'meta_data' => 'nullable',
            'meta_keyword' => 'nullable'
        ]);
    
        Page::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'meta_data' => $request->meta_data,
            'meta_keyword' => $request->meta_keyword,
            'path' => $request->path,
            'created_by' => auth()->id()
        ]);

        Session::flash('successMsg', 'Page created successfully.');
        return redirect()->route('page.index');
    }

    public function edit($id)
    {
        $page = Page::findOrFail($id);
        return view('page.create', ['page' => $page, 'categories' => Category::all()]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'title' => 'required',
            'path' => 'required|url|unique:pages,path,'.$id,
            'meta_data' => 'nullable',
            'meta_keyword' => 'nullable'
        ]);

        $page = Page::findOrFail($id);
        $page->update($request->all());

        Session::flash('successMsg', 'Page updated successfully.');
        return redirect()->route('page.index');
    }

    public function destroy($id)
    {
        Page::findOrFail($id)->delete();
        return response()->json(['msg' => 'Deleted successfully!']);
    }
}
