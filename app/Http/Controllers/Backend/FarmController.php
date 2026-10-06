<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Admin;
use App\Models\Country;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\DailyRecord;
use App\Models\Hangar;

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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'number_of_hangars' => 'required|numeric|min:1',
            'assigned_to' => 'nullable|integer|exists:admins,id',
            'type' => 'required',
            'phone_code' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
        ]);

        // Type 2 (Farm Owner) can create maximum 3 farms
        $user = auth()->user();
        if ($user->type == 2) {
            $farmCount = Farm::where('created_by', $user->id)->count();
            if ($farmCount >= 3) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have reached the maximum limit of 3 farms.'
                    ], 422);
                }
                Session::flash('errorMsg', 'You have reached the maximum limit of 3 farms.');
                return redirect()->back()->withInput();
            }
        }

        $createData = [
            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'number_of_hangars' => $request->number_of_hangars,
            'type' => $request->type,
            'phone_code' => $request->phone_code,
            'mobile_number' => $request->mobile_number,
            'created_by' => auth()->id(),
            'assigned_to' => $request->assigned_to ?? null,
        ];

        $farm = Farm::create($createData);

        // Assign to single admin
        if ($request->filled('assigned_to')) {
            $farm->assignedAdmins()->attach($request->assigned_to);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Farm created successfully.',
                'farm' => $farm->load('assignedAdmin', 'assignedAdmins', 'creator')
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
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'number_of_hangars' => 'required|numeric|min:1',
            'assigned_to' => 'nullable|integer|exists:admins,id',
            'type' => 'required',
            'phone_code' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20',
        ]);

        $updateData = [
            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'number_of_hangars' => $request->number_of_hangars,
            'type' => $request->type,
            'phone_code' => $request->phone_code,
            'mobile_number' => $request->mobile_number,
            'assigned_to' => $request->assigned_to ?? null,
        ];

        $farm->update($updateData);

        // Assign to single admin
        if ($request->filled('assigned_to')) {
            $farm->assignedAdmins()->sync([$request->assigned_to]);
        } else {
            $farm->assignedAdmins()->detach();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Farm updated successfully.',
                'farm' => $farm->load('assignedAdmin', 'assignedAdmins', 'creator')
            ]);
        }

        Session::flash('successMsg', 'Farm updated successfully.');
        return redirect()->route('farm.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        $farm = Farm::with('flocks', 'hangars')->findOrFail($id);

        $dependentRecords = [];

        $flocksCount = \App\Models\Flock::where('farm_id', $farm->id)->count();
        if ($flocksCount > 0) {
            $dependentRecords['Flocks'] = $flocksCount;
        }

        $hangarsCount = \App\Models\Hangar::where('farm_id', $farm->id)->count();
        if ($hangarsCount > 0) {
            $dependentRecords['Hangars'] = $hangarsCount;
        }

        $dailyRecordsCount = \App\Models\DailyRecord::where('farm_id', $farm->id)->count();
        if ($dailyRecordsCount > 0) {
            $dependentRecords['Daily Records'] = $dailyRecordsCount;
        }

        if (!empty($dependentRecords)) {
            $message = 'Cannot delete farm "' . $farm->name . '". The following related data exists: ';
            $details = [];
            foreach ($dependentRecords as $type => $count) {
                $details[] = $count . ' ' . $type;
            }
            $message .= implode(', ', $details) . '. Please remove all related data before deleting this farm.';

            return response()->json([
                'msg' => $message,
                'error' => true,
                'dependentRecords' => $dependentRecords
            ], 422);
        }

        $farm->delete();
        return response()->json(['msg' => 'Farm deleted successfully.']);
    }
}
