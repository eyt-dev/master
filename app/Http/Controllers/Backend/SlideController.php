<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Slide;
use App\Models\StoreView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SlideController extends Controller
{
    public function index(Request $request, $siteUrl)
    {
        if ($request->ajax()) {
            $data = Slide::with('storeView')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();

            return datatables()->of($data)
                ->addColumn('store_view', function($row) {
                    return ($row->storeView->region . ' - ' . $row->storeView->language) ?? 'N/A';
                })
                ->addColumn('web_image', function($row) {
                    if ($row->web_image) {
                        $path = asset('storage/'.$row->web_image);
                        return '<img src="'.$path.'" alt="Web Image" class="img-thumbnail" width="100" />';
                    }
                })
                ->addColumn('creator', function($row) {
                    // dump($row->creator->name);
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('mobile_image', function($row) {
                    if ($row->mobile_image) {
                        $path = asset('storage/'.$row->mobile_image);
                        return '<img src="'.$path.'" alt="Mobile Image" class="img-thumbnail" width="100" />';
                    }
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-slide btn btn-sm btn-success" data-path="'.route('slide.edit', ['username' => request()->segment(1), 'slide' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-slide btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['web_image', 'mobile_image', 'action'])
                ->make(true);
        }

        return view('backend.slide.index');
    }

    public function create($siteUrl)
    {
        $store_views = StoreView::all();
        return view('backend.slide.create', compact('store_views'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'store_view_id' => 'required',
            'name' => 'required',
            'web_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mobile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_button_text' => 'nullable|string|max:255',
            'primary_button_link' => 'nullable|url',
            'secondary_button_text' => 'nullable|string|max:255',
            'secondary_button_link' => 'nullable|url',
        ]);

        $webImagePath = $request->file('web_image')->store('slides', 'public');
        $mobileImagePath = $request->file('mobile_image')->store('slides', 'public');

        $slideData = [
            'store_view_id' => $request->store_view_id,
            'name' => $request->name,
            'description' => $request->description,
            'web_image' => $webImagePath,
            'mobile_image' => $mobileImagePath,
            'primary_button_text' => $request->primary_button_text,
            'primary_button_link' => $request->primary_button_link,
            'secondary_button_text' => $request->secondary_button_text,
            'secondary_button_link' => $request->secondary_button_link,
            'created_by' => auth()->id()
        ];

        Slide::create($slideData);

        Session::flash('successMsg', 'Slide created successfully.');
        return redirect()->route('slide.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $slide = Slide::findOrFail($id);
        $store_views = StoreView::all();
        return view('backend.slide.create', compact('slide', 'store_views'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'store_view_id' => 'required',
            'name' => 'required',
            'web_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mobile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'primary_button_text' => 'nullable|string|max:255',
            'primary_button_link' => 'nullable|url',
            'secondary_button_text' => 'nullable|string|max:255',
            'secondary_button_link' => 'nullable|url',
        ]);

        $slide = Slide::findOrFail($id);

        if ($request->hasFile('web_image')) {
            $webImagePath = $request->file('web_image')->store('slides', 'public');
            if ($slide->web_image && \Storage::disk('public')->exists($slide->web_image)) {
                \Storage::disk('public')->delete($slide->web_image);
            }
            $slide->web_image = $webImagePath;
        }

        if ($request->hasFile('mobile_image')) {
            $mobileImagePath = $request->file('mobile_image')->store('slides', 'public');
            if ($slide->mobile_image && \Storage::disk('public')->exists($slide->mobile_image)) {
                \Storage::disk('public')->delete($slide->mobile_image);
            }
            $slide->mobile_image = $mobileImagePath;
        }

        $slide->update([
            'store_view_id' => $request->store_view_id,
            'name' => $request->name,
            'description' => $request->description,
            'primary_button_text' => $request->primary_button_text,
            'primary_button_link' => $request->primary_button_link,
            'secondary_button_text' => $request->secondary_button_text,
            'secondary_button_link' => $request->secondary_button_link,
        ]);

        Session::flash('successMsg', 'Slide updated successfully.');
        return redirect()->route('slide.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        $slide = Slide::findOrFail($id);
        $slide->delete();
        return response()->json(['msg' => 'Slide deleted successfully.']);
    }
}
