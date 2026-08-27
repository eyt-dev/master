<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slaughter;
use App\Models\Country;
use Illuminate\Support\Facades\Session;

class SlaughterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Slaughter::with('creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('phone_code', function($row) {
                    return $row->phone_code ?? 'N/A';
                })
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-slaughter btn btn-sm btn-success" data-path="'.route('slaughter.edit', ['username' => request()->segment(1),  'slaughter' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-slaughter btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('backend.slaughter.index');
    }

    public function create()
    {
        $countries = Country::select('id', 'name', 'dial_code')->orderBy('name')->get()
            ->map(function ($country) {
                $country->dial_code_with_plus = '+' . $country->dial_code;
                return $country;
            });
        return view('backend.slaughter.create', compact('countries'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required',
            'contact_person' => 'required',
            'mobile_number' => 'required',
            'phone_code' => 'nullable|string|max:10',
            'location' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $createData = [
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'mobile_number' => $request->mobile_number,
            'phone_code' => $request->phone_code,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'created_by' => auth()->id()
        ];
        Slaughter::create($createData);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Slaughter created successfully.'
            ]);
        }

        Session::flash('successMsg', 'Slaughter created successfully.');
        return redirect()->route('slaughter.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $slaughter = Slaughter::findOrFail($id);
        $countries = Country::select('id', 'name', 'dial_code')->orderBy('name')->get()
            ->map(function ($country) {
                $country->dial_code_with_plus = '+' . $country->dial_code;
                return $country;
            });
        return view('backend.slaughter.create', compact('slaughter', 'countries'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'name' => 'required',
            'contact_person' => 'required',
            'mobile_number' => 'required',
            'phone_code' => 'nullable|string|max:10',
            'location' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $slaughter = Slaughter::findOrFail($id);
        $slaughter->update([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'mobile_number' => $request->mobile_number,
            'phone_code' => $request->phone_code,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Slaughter updated successfully.'
            ]);
        }

        Session::flash('successMsg', 'Slaughter updated successfully.');
        return redirect()->route('slaughter.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Slaughter::findOrFail($id)->delete();
        return response()->json(['msg' => 'Slaughter deleted successfully.']);
    }
}
