<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChicksSupplier;
use App\Models\Country;
use Illuminate\Support\Facades\Session;

class ChicksSupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ChicksSupplier::with('creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('breed', function($row) {
                    $breedType = $this->extractBreedType($row->breed);
                    $badgeClass = ($breedType === 'Broiler') ? 'badge-danger' : 'badge-info';
                    return '<span class="badge ' . $badgeClass . '">' . $breedType . ' (' . $row->breed . ')</span>';
                })
                ->addColumn('mobile_number', function($row) {
                    if (!$row->mobile_number) {
                        return 'N/A';
                    }
                    if ($row->phone_code) {
                        return $row->phone_code . $row->mobile_number;
                    }
                    return $row->mobile_number;
                })
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-chicks-supplier btn btn-sm btn-success" data-path="'.route('chicks-supplier.edit', ['username' => request()->segment(1),  'chicks_supplier' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-chicks-supplier btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'breed'])
                ->make(true);
        }
        return view('backend.chicks-supplier.index');
    }

    public function create()
    {
        $breeds = [
            'Broiler' => ['Ross 308', 'Cobb 500'],
            'Layer' => ['Lohmann Brown', 'Lohmann White']
        ];
        $countries = Country::select('id', 'name', 'dial_code')->orderBy('name')->get()
            ->map(function ($country) {
                $country->dial_code_with_plus = '+' . $country->dial_code;
                return $country;
            });
        return view('backend.chicks-supplier.create', compact('breeds', 'countries'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required',
            'breed' => 'required',
            'location' => 'required',
            'contact_person' => 'required',
            'phone_code' => 'required|string|max:10',
            'mobile_number' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $createData = [
            'name' => $request->name,
            'breed' => $request->breed,
            'location' => $request->location,
            'contact_person' => $request->contact_person,
            'phone_code' => $request->phone_code,
            'mobile_number' => $request->mobile_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_by' => auth()->id()
        ];
        ChicksSupplier::create($createData);

        Session::flash('successMsg', 'Chicks Supplier created successfully.');
        return redirect()->route('chicks-supplier.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $chicks_supplier = ChicksSupplier::findOrFail($id);
        $breeds = [
            'Broiler' => ['Ross 308', 'Cobb 500'],
            'Layer' => ['Lohmann Brown', 'Lohmann White']
        ];
        $countries = Country::select('id', 'name', 'dial_code')->orderBy('name')->get()
            ->map(function ($country) {
                $country->dial_code_with_plus = '+' . $country->dial_code;
                return $country;
            });
        return view('backend.chicks-supplier.create', compact('chicks_supplier', 'breeds', 'countries'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'name' => 'required',
            'breed' => 'required',
            'location' => 'required',
            'contact_person' => 'required',
            'phone_code' => 'required|string|max:10',
            'mobile_number' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $chicks_supplier = ChicksSupplier::findOrFail($id);
        $chicks_supplier->update([
            'name' => $request->name,
            'breed' => $request->breed,
            'location' => $request->location,
            'contact_person' => $request->contact_person,
            'phone_code' => $request->phone_code,
            'mobile_number' => $request->mobile_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        Session::flash('successMsg', 'Chicks Supplier updated successfully.');
        return redirect()->route('chicks-supplier.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        ChicksSupplier::findOrFail($id)->delete();
        return response()->json(['msg' => 'Chicks Supplier deleted successfully.']);
    }

    private function extractBreedType($breedName)
    {
        $broilerBreeds = ['Ross 308', 'Cobb 500'];
        $layerBreeds = ['Lohmann Brown', 'Lohmann White'];

        if (in_array($breedName, $broilerBreeds)) {
            return 'Broiler';
        } elseif (in_array($breedName, $layerBreeds)) {
            return 'Layer';
        }

        return 'Layer';
    }
}
