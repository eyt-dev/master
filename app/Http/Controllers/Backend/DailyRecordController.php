<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Helpers\FlockHelper;
use Illuminate\Http\Request;
use App\Models\DailyRecord;
use App\Models\Farm;
use App\Models\Hangar;
use App\Models\Flock;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;

class DailyRecordController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DailyRecord::with('farm', 'hangar', 'creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('record_date', function($row) {
                    return date('Y-m-d', strtotime($row->record_date));
                })
                ->addColumn('farm', function($row) {
                    return $row->farm->name ?? 'N/A';
                })
                ->addColumn('hangar', function($row) {
                    return $row->hangar->name ?? 'N/A';
                })
                ->addColumn('created_by', function($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-daily-record btn btn-sm btn-success mr-1" data-id="'.$row->id.'" data-path="'.route('daily-record.edit', ['username' => request()->segment(1), 'daily_record' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-daily-record btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action'])   
                ->make(true);
        }
        return view('backend.daily-record.index');
    }

    public function create()
    {
        $flocks = FlockHelper::getAllFlockOptions();
        return view('backend.daily-record.create', compact('flocks'));
    }

    public function getHangarsByFlock($siteUrl, $flockId)
    {        
        $flockId = (int) $flockId;
        $flock = Flock::findOrFail($flockId);
        
        $hangars = Hangar::where('farm_id', $flock->farm_id)
            ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                $query->where('created_by', auth()->id());
            })
            ->select('id', 'name')
            ->get();
        
        return response()->json($hangars);
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'record_date' => 'required|date',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_id' => 'required|exists:hangars,id',
            'feed_kg' => 'required|numeric|min:0',
            'eggs_tray_30' => 'required|integer|min:0',
            'eggs_count' => 'required|integer|min:0',
            'mortality' => 'required|integer|min:0',
        ]);

        // Get farm_id from the selected flock
        $flock = Flock::findOrFail($request->flock_id);

        DailyRecord::create([
            'record_date' => $request->record_date,
            'farm_id' => $flock->farm_id,
            'hangar_id' => $request->hangar_id,
            'flock_id' => $request->flock_id,
            'feed_kg' => $request->feed_kg,
            'eggs_tray_30' => $request->eggs_tray_30,
            'eggs_count' => $request->eggs_count,
            'mortality' => $request->mortality,
            'created_by' => auth()->id()
        ]);

        Session::flash('successMsg', 'Daily Record created successfully.');
        return redirect()->route('daily-record.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $dailyRecord = DailyRecord::findOrFail($id);
        $flocks = FlockHelper::getAllFlockOptions();
        $hangars = Hangar::where('farm_id', $dailyRecord->farm_id)->get();
        
        return view('backend.daily-record.create', compact('dailyRecord', 'flocks', 'hangars'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'record_date' => 'required|date',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_id' => 'required|exists:hangars,id',
            'feed_kg' => 'required|numeric|min:0',
            'eggs_tray_30' => 'required|integer|min:0',
            'eggs_count' => 'required|integer|min:0',
            'mortality' => 'required|integer|min:0',
        ]);

        // Get farm_id from the selected flock
        $flock = Flock::findOrFail($request->flock_id);

        $dailyRecord = DailyRecord::findOrFail($id);
        $dailyRecord->update([
            'record_date' => $request->record_date,
            'farm_id' => $flock->farm_id,
            'hangar_id' => $request->hangar_id,
            'flock_id' => $request->flock_id,
            'feed_kg' => $request->feed_kg,
            'eggs_tray_30' => $request->eggs_tray_30,
            'eggs_count' => $request->eggs_count,
            'mortality' => $request->mortality,
        ]);

        Session::flash('successMsg', 'Daily Record updated successfully.');
        return redirect()->route('daily-record.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        DailyRecord::findOrFail($id)->delete();
        return response()->json(['msg' => 'Daily Record deleted successfully.']);
    }
}
