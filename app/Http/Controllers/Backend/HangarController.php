<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Helpers\DecimalHelper;
use Illuminate\Http\Request;
use App\Models\Hangar;
use App\Models\Farm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Models\DailyRecord;
use App\Models\MaterialStockHangar;

class HangarController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Hangar::with('farm', 'creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->whereHas('farm', function ($subQuery) {
                        $subQuery->where('created_by', auth()->id())
                                 ->orWhere('assigned_to', auth()->id());
                    })
                    ->orWhere('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('farm_name', function($row) {
                    $farmName = $row->farm->name ?? 'N/A';
                    if ($row->farm && $row->farm->assignedAdmin) {
                        $farmName .= '<br><small style="color: #666;">Assigned to: ' . $row->farm->assignedAdmin->name . '</small>';
                    }
                    return $farmName;
                })
                ->addColumn('name', function($row) {
                    return $row->name ?? 'N/A';
                })
                ->addColumn('status', function($row) {
                    $status = trim($row->status);
                    $statusClass = $status === 'Active' ? 'badge-success' : 'badge-danger';
                    return '<span class="badge ' . $statusClass . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('creator', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('remaining_feed', function($row) {
                    $totalStock = \App\Models\MaterialStockHangar::where('hangar_id', $row->id)
                        ->byMaterialType()
                        ->sum('quantity') ?? 0;

                    $totalConsumed = \App\Models\DailyRecord::where('hangar_id', $row->id)
                        ->sum('feed_kg') ?? 0;

                    $remaining = max(0, $totalStock - $totalConsumed);
                    return DecimalHelper::formatEuropean($remaining, 2) . ' kg';
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-hangar btn btn-sm btn-success" data-path="'.route('hangar.edit', ['username' => request()->segment(1),  'hangar' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-hangar btn btn-sm btn-danger" data-path="'.route('hangar.destroy', ['username' => request()->segment(1), 'hangar' => $row->id]).'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'status', 'farm_name'])
                ->make(true);
        }
        return view('backend.hangar.index');
    }

    public function create()
    {
        $user = auth()->user();
        $siteSlug = request()->segment(1);
        $farms = Farm::when($user->role !== 'SuperAdmin', function ($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
        })->get();
        return view('backend.hangar.create', compact('farms', 'siteSlug'));
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'farm_id' => 'required',
            'name' => 'required|string',
            'area_sqm' => 'required|integer|min:0',
            'layer_hens' => 'required|integer',
            'broiler_hens' => 'required|integer',
        ]);

        $createData = [
            'farm_id' => $request->farm_id,
            'name' => $request->name,
            'area_sqm' => (int) $request->area_sqm,
            'layer_hens' => $request->layer_hens,
            'broiler_hens' => $request->broiler_hens,
            'created_by' => auth()->id()
        ];
        Hangar::create($createData);

        Session::flash('successMsg', 'Hangar created successfully.');
        return redirect()->route('hangar.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, Hangar $hangar)
    {
        $user = auth()->user();
        $siteSlug = $siteUrl;

        // Verify user has access to this hangar
        if ($user->role !== 'SuperAdmin') {
            $isOwnHangar = ($hangar->created_by === $user->id);
            $hasFarmAccess = $hangar->farm &&
                ($hangar->farm->created_by === $user->id ||
                 $hangar->farm->assigned_to === $user->id);

            if (!$isOwnHangar && !$hasFarmAccess) {
                abort(403, 'Unauthorized');
            }
        }

        $hangar->load('farm');

        $farms = Farm::when($user->role !== 'SuperAdmin', function ($query) use ($user) {
            $query->where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
        })->get();

        return view('backend.hangar.create', compact('hangar', 'farms', 'siteSlug'));
    }

    public function update(Request $request, $siteUrl, Hangar $hangar)
    {
        $user = auth()->user();

        // Verify user has access to this hangar
        if ($user->role !== 'SuperAdmin') {
            $isOwnHangar = ($hangar->created_by === $user->id);
            $hasFarmAccess = $hangar->farm &&
                ($hangar->farm->created_by === $user->id ||
                 $hangar->farm->assigned_to === $user->id);

            if (!$isOwnHangar && !$hasFarmAccess) {
                abort(403, 'Unauthorized');
            }
        }

        $request->validate([
            'farm_id' => 'required',
            'name' => 'required|string',
            'area_sqm' => 'required|integer|min:0',
            'layer_hens' => 'required|integer',
            'broiler_hens' => 'required|integer',
        ]);

        $hangar->update([
            'farm_id' => $request->farm_id,
            'name' => $request->name,
            'area_sqm' => (int) $request->area_sqm,
            'layer_hens' => $request->layer_hens,
            'broiler_hens' => $request->broiler_hens,
        ]);

        Session::flash('successMsg', 'Hangar updated successfully.');
        return redirect()->route('hangar.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, Hangar $hangar)
    {
        $user = auth()->user();

        // Verify user has access to this hangar
        if ($user->role !== 'SuperAdmin') {
            $isOwnHangar = ($hangar->created_by === $user->id);
            $hasFarmAccess = $hangar->farm &&
                ($hangar->farm->created_by === $user->id ||
                 $hangar->farm->assigned_to === $user->id);

            if (!$isOwnHangar && !$hasFarmAccess) {
                abort(403, 'Unauthorized');
            }
        }

        // Check for dependent records before deletion
        $dependentRecords = [];

        $dailyRecordsCount = \App\Models\DailyRecord::where('hangar_id', $hangar->id)->count();
        if ($dailyRecordsCount > 0) {
            $dependentRecords['Daily Records'] = $dailyRecordsCount;
        }

        $materialStockCount = \App\Models\MaterialStockHangar::where('hangar_id', $hangar->id)->count();
        if ($materialStockCount > 0) {
            $dependentRecords['Material Stock Allocations'] = $materialStockCount;
        }

        if (!empty($dependentRecords)) {
            $message = 'Cannot delete hangar "' . $hangar->name . '". The following related data exists: ';
            $details = [];
            foreach ($dependentRecords as $type => $count) {
                $details[] = $count . ' ' . $type;
            }
            $message .= implode(', ', $details) . '. Please remove all related data before deleting this hangar.';

            return response()->json([
                'msg' => $message,
                'error' => true,
                'dependentRecords' => $dependentRecords
            ], 422);
        }

        // Auto-remove flock allocations when deleting hangar
        \App\Models\FlockHangar::where('hangar_id', $hangar->id)->delete();

        $hangar->delete();
        return response()->json(['msg' => 'Hangar deleted successfully.']);
    }
}
