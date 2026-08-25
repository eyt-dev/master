<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Admin;
use App\Models\Country;
use Illuminate\Support\Facades\Session;

class FarmController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Farm::with('assignedAdmin', 'creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('assigned_admin', function($row) {
                    return $row->assignedAdmin->name ?? 'N/A';
                })
                ->addColumn('type', function($row) {
                    // Define the same label mappings
                    $typeData = [
                        'closed_system' => 'Closed System',
                        'open_system'   => 'Open System',
                        'cages'         => 'Cages',
                    ];

                    // Return the readable label if the key exists, otherwise fallback to the raw type or 'N/A'
                    return $typeData[$row->type] ?? $row->type ?? 'N/A';
                })
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-farm btn btn-sm btn-success mr-1" data-id="'.$row->id.'" data-path="'.route('farm.edit', ['username' => request()->segment(1),  'farm' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-farm btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])   
                ->make(true);
        }
        return view('backend.farm.index');
    }

    public function create()
    {
        $user = auth()->user();

        // Get supervisors based on logged-in user type
        $supervisorType = match($user->type) {
            2 => 4,  // type 2 users get type 4 supervisors
            1 => 3,  // type 1 users get type 3 supervisors
            default => 3,
        };

        $admins = Admin::where('type', $supervisorType)->orderBy('name')->get();
        $countries = Country::select('id', 'name', 'dial_code')->orderBy('name')->get()
            ->map(function ($country) {
                $country->dial_code_with_plus = '+' . $country->dial_code;
                return $country;
            });
        return view('backend.farm.create', compact('admins', 'countries'));
    }

    public function store(Request $request, $siteUrl)
    {
        $validated = $request->validate([
            'name' => 'required|unique:farms,name',
            'location' => 'required',
            'number_of_hangars' => 'required|numeric|min:1',
            'assigned_to' => 'nullable|exists:admins,id',
            'type' => 'required',
            'phone_code' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
        ]);

        $createData = [
            'name' => $request->name,
            'location' => $request->location,
            'number_of_hangars' => $request->number_of_hangars,
            'assigned_to' => $request->assigned_to,
            'type' => $request->type,
            'phone_code' => $request->phone_code,
            'mobile_number' => $request->mobile_number,
            'created_by' => auth()->id()
        ];
        $farm = Farm::create($createData);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Farm created successfully.',
                'farm' => $farm->load('assignedAdmin', 'creator')
            ]);
        }

        Session::flash('successMsg', 'Farm created successfully.');
        return redirect()->route('farm.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $farm = Farm::findOrFail($id);
        $user = auth()->user();

        // Get supervisors based on logged-in user type
        $supervisorType = match($user->type) {
            2 => 4,  // type 2 users get type 4 supervisors
            1 => 3,  // type 1 users get type 3 supervisors
            default => 3,
        };

        $admins = Admin::where('type', $supervisorType)->orderBy('name')->get();
        $countries = Country::select('id', 'name', 'dial_code')->orderBy('name')->get()
            ->map(function ($country) {
                $country->dial_code_with_plus = '+' . $country->dial_code;
                return $country;
            });
        return view('backend.farm.create', compact('farm', 'admins', 'countries'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $farm = Farm::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|unique:farms,name,' . $farm->id,
            'location' => 'required',
            'number_of_hangars' => 'required|numeric|min:1',
            'assigned_to' => 'nullable|exists:admins,id',
            'type' => 'required',
            'phone_code' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
        ]);

        $farm->update([
            'name' => $request->name,
            'location' => $request->location,
            'number_of_hangars' => $request->number_of_hangars,
            'assigned_to' => $request->assigned_to,
            'type' => $request->type,
            'phone_code' => $request->phone_code,
            'mobile_number' => $request->mobile_number,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Farm updated successfully.',
                'farm' => $farm->load('assignedAdmin', 'creator')
            ]);
        }

        Session::flash('successMsg', 'Farm updated successfully.');
        return redirect()->route('farm.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        Farm::findOrFail($id)->delete();
        return response()->json(['msg' => 'Farm deleted successfully.']);
    }
}
