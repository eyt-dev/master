<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Models\Country;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $data = Country::when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('image', function ($row) {
                    if ($row->image) {
                        $path = asset('storage/' . $row->image);
                        return '<img src="' . $path . '" alt="Country Image" width="150" height="100" />';
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-country btn btn-sm btn-success" data-path="' . route('country.edit', $row->id) . '" title="Edit" style="margin-right: 5px;">
                    <i class="fa fa-edit"></i></a>'
                        . '<a class="delete-country btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete" style="margin-right: 5px;">
                    <i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['image', 'action'])
                ->make(true);
        }

        $countries = Country::orderBy('id', 'desc')->paginate(10);

        return view('country.index', compact('countries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('country.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCountryRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('countries', 'public');
            $data['image'] = $imagePath;
        }

        Country::create($data);

        Session::flash('successMsg', 'Country created successfully.');

        return redirect()->route('country.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Country $country)
    {
        return view('country.create', compact('country'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCountryRequest $request, $id)
    {
        $country = Country::findOrFail($id);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($country->image && Storage::disk('public')->exists('countries/' . $country->image)) {
                Storage::disk('public')->delete($country->image);
            }

            $imagePath = $request->file('image')->store('countries', 'public');
            $data['image'] = $imagePath;
        }

        $country->update($data);

        Session::flash('successMsg', 'Country updated successfully.');

        return redirect()->route('country.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Country::findOrFail($id)->delete();

        return response()->json(['msg' => 'Country deleted successfully.']);
    }
}
