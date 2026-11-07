<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Helpers\DecimalHelper;
use Illuminate\Http\Request;
use App\Models\EggWholesaler;
use App\Models\Country;
use Illuminate\Support\Facades\Session;

class EggWholesalerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = EggWholesaler::with('creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
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
                    return '<a class="edit-egg-wholesaler btn btn-sm btn-success" data-path="'.route('egg-wholesaler.edit', ['username' => request()->segment(1),  'egg_wholesaler' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-egg-wholesaler btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.egg-wholesaler.index');
    }

    public function create()
    {
        $countries = Country::select('id', 'name', 'dial_code')->orderBy('name')->get()
            ->map(function ($country) {
                $country->dial_code_with_plus = '+' . $country->dial_code;
                return $country;
            });
        return view('backend.egg-wholesaler.create', compact('countries'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'contact_person' => 'required',
            'phone_code' => 'required|string|max:10',
            'mobile_number' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $createData = [
            'name' => $request->name,
            'location' => $request->location,
            'contact_person' => $request->contact_person,
            'phone_code' => $request->phone_code,
            'mobile_number' => $request->mobile_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_by' => auth()->id()
        ];
        EggWholesaler::create($createData);

        Session::flash('successMsg', 'Egg Wholesaler created successfully.');
        return redirect()->route('egg-wholesaler.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $egg_wholesaler = EggWholesaler::findOrFail($id);
        $countries = Country::select('id', 'name', 'dial_code')->orderBy('name')->get()
            ->map(function ($country) {
                $country->dial_code_with_plus = '+' . $country->dial_code;
                return $country;
            });
        return view('backend.egg-wholesaler.create', compact('egg_wholesaler', 'countries'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->merge([
            'latitude' => DecimalHelper::parseEuropean($request->latitude),
            'longitude' => DecimalHelper::parseEuropean($request->longitude),
        ]);

        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'contact_person' => 'required',
            'phone_code' => 'required|string|max:10',
            'mobile_number' => 'required',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $egg_wholesaler = EggWholesaler::findOrFail($id);
        $egg_wholesaler->update([
            'name' => $request->name,
            'location' => $request->location,
            'contact_person' => $request->contact_person,
            'phone_code' => $request->phone_code,
            'mobile_number' => $request->mobile_number,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        Session::flash('successMsg', 'Egg Wholesaler updated successfully.');
        return redirect()->route('egg-wholesaler.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        $wholesaler = EggWholesaler::findOrFail($id);
        $wholesaler->delete();

        return response()->json(['msg' => 'Egg Wholesaler deleted successfully.']);
    }
}
