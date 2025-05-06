<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreElementRequest;
use App\Http\Requests\UpdateElementRequest;
use App\Models\Element;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class ElementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Element::when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            })->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('eu_code', function ($row) {
                    return $row->eu_code ?: '-';
                })
                ->addColumn('synonym', function ($row) {
                    return $row->synonym ?: '-';
                })
                ->addColumn('attachment', function ($row) {
                    if ($row->attachment) {
                        $fileName = basename($row->attachment);
                        $path = asset('storage/' . $row->attachment);
                        return $fileName;
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    $buttons = '';

                    if ($row->attachment) {
                        $path = asset('storage/' . $row->attachment);
                        $buttons .= '<a href="' . $path . '" target="_blank" class="btn btn-sm btn-info" style="margin-right: 5px;">
                        <i class="fa fa-download"></i>
                      </a>';
                    }

                    $buttons .= '<a class="edit-element btn btn-sm btn-success" data-path="' . route('element.edit', $row->id) . '" title="Edit" style="margin-right: 5px;">
                    <i class="fa fa-edit"></i>
                  </a>';

                    $buttons .= '<a class="delete-element btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete" style="margin-right: 5px;">
                    <i class="fa fa-trash"></i>
                  </a>';

                    return $buttons;
                })
                ->addIndexColumn()
                ->rawColumns(['attachment', 'action'])
                ->make(true);
        }

        $countries = Element::orderBy('id', 'desc')->paginate(10);

        return view('element.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('element.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreElementRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            $data['attachment'] = $attachmentPath;
        }

        Element::create($data);

        session()->flash('successMsg', 'Element created successfully.');

        return redirect()->route('element.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Element $element)
    {
        return view('element.create', compact('element'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateElementRequest $request, $id)
    {
        $element = Element::findOrFail($id);

        $data = $request->validated();

        if ($request->hasFile('attachment')) {
            if ($element->attachment && Storage::disk('public')->exists($element->attachment)) {
                Storage::disk('public')->delete($element->attachment);
            }

            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
            $data['attachment'] = $attachmentPath;
        }

        $element->update($data);

        Session::flash('successMsg', 'Element updated successfully.');

        return redirect()->route('element.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Element::findOrFail($id)->delete();

        return response()->json(['msg' => 'Element deleted successfully.']);
    }
}
