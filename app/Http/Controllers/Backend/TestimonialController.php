<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimonial;
use App\Models\StoreView;
use Illuminate\Support\Facades\Session;

class TestimonialController extends Controller
{
    public function index(Request $request, $siteUrl)
    {
        if ($request->ajax()) {
            $data = Testimonial::with('storeView')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return datatables()->of($data)
                ->addColumn('store_view', function ($row) {
                    return ($row->storeView->region . ' - ' . $row->storeView->language) ?? 'N/A';
                })
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        $path = asset('storage/' . $row->image);
                        return '<img src="' . $path . '" alt="Image" class="img-thumbnail" width="100" />';
                    }
                    return '';
                })
                ->addColumn('creator', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-testimonial btn btn-sm btn-success" data-path="' . route('testimonial.edit', ['site' => request()->segment(1), 'testimonial' => $row->id]) . '" title="Edit"><i class="fa fa-edit"></i></a>'
                         . '<a class="delete-testimonial btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        return view('backend.testimonial.index');
    }

    public function create($siteUrl)
    {
        $store_views = StoreView::all();
        return view('backend.testimonial.create', compact('store_views'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'store_view' => 'required',
            'name' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('testimonials', 'public');
        }

        Testimonial::create([
            'store_view_id' => $request->store_view,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath ?? null,
            'created_by' => auth()->id(),
        ]);

        Session::flash('successMsg', 'Testimonial created successfully.');
        return redirect()->route('testimonial.index', ['site' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $store_views = StoreView::all();
        return view('backend.testimonial.create', compact('testimonial', 'store_views'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'store_view' => 'required',
            'name' => 'required',
            'description' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $testimonial = Testimonial::findOrFail($id);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('testimonials', 'public');

            if ($testimonial->image && \Storage::disk('public')->exists($testimonial->image)) {
                \Storage::disk('public')->delete($testimonial->image);
            }

            $testimonial->image = $imagePath;
        }

        $testimonial->update([
            'store_view_id' => $request->store_view,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        Session::flash('successMsg', 'Testimonial updated successfully.');
        return redirect()->route('testimonial.index', ['site' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Testimonial::findOrFail($id)->delete();
        return response()->json(['msg' => 'Testimonial deleted successfully.']);
    }
}
