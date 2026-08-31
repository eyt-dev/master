<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Helpers\FlockHelper;
use Illuminate\Http\Request;
use App\Models\DailyRecord;
use App\Models\Farm;
use App\Models\Hangar;
use App\Models\Flock;
use App\Models\FlockHangar;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;

class DailyRecordController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Get unique flock/date combinations
            $data = DailyRecord::with('farm', 'flock', 'creator')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->where('created_by', auth()->id());
                })
                ->orderBy('record_date', 'desc')
                ->orderBy('flock_id', 'desc')
                ->get()
                ->groupBy(function($record) {
                    return $record->flock_id . '_' . $record->record_date;
                })
                ->map(function($group) {
                    $firstRecord = $group->first();
                    $flockLabel = $firstRecord->flock ? \App\Helpers\FlockHelper::getFlockLabel($firstRecord->flock) : 'N/A';
                    
                    return [
                        'id' => $firstRecord->id,
                        'record_date' => $firstRecord->record_date,
                        'flock_id' => $firstRecord->flock_id,
                        'flock_label' => $flockLabel,
                        'farm' => $firstRecord->farm->name ?? 'N/A',
                        'created_by' => $firstRecord->creator->name ?? 'N/A',
                        'created_at' => $firstRecord->created_at,
                        'hangars' => $group->map(function($record) {
                            return [
                                'hangar_id' => $record->hangar_id,
                                'hangar_name' => $record->hangar->name ?? 'N/A',
                                'feed_kg' => $record->feed_kg,
                                'eggs_tray_30' => $record->eggs_tray_30,
                                'eggs_count' => $record->eggs_count,
                                'eggs_weight' => $record->eggs_weight,
                                'chicks_weight' => $record->chicks_weight,
                                'mortality' => $record->mortality
                            ];
                        })->sortBy('hangar_name')->values()
                    ];
                })
                ->values();

            return datatables()->of($data)
                ->addColumn('record_date', function($row) {
                    return date('Y-m-d', strtotime($row['record_date']));
                })
                ->addColumn('flock', function($row) {
                    return $row['flock_label'];
                })
                ->addColumn('farm', function($row) {
                    return $row['farm'];
                })
                ->addColumn('created_by', function($row) {
                    return $row['created_by'];
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row['created_at']));
                })
                ->addColumn('hangar1', function($row) {
                    return $this->formatHangarData($row, 0);
                })
                ->addColumn('hangar2', function($row) {
                    return $this->formatHangarData($row, 1);
                })
                ->addColumn('hangar3', function($row) {
                    return $this->formatHangarData($row, 2);
                })
                ->addColumn('hangar4', function($row) {
                    return $this->formatHangarData($row, 3);
                })
                ->addColumn('hangar5', function($row) {
                    return $this->formatHangarData($row, 4);
                })
                ->addColumn('hangar6', function($row) {
                    return $this->formatHangarData($row, 5);
                })
                ->addColumn('hangar7', function($row) {
                    return $this->formatHangarData($row, 6);
                })
                ->addColumn('hangar8', function($row) {
                    return $this->formatHangarData($row, 7);
                })
                ->addColumn('hangar9', function($row) {
                    return $this->formatHangarData($row, 8);
                })
                ->addColumn('hangar10', function($row) {
                    return $this->formatHangarData($row, 9);
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-daily-record btn btn-sm btn-success mr-1" data-id="'.$row['id'].'" data-path="'.route('daily-record.edit', ['username' => request()->segment(1), 'daily_record' => $row['id']]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-daily-record btn btn-sm btn-danger" data-id="'.$row['id'].'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'hangar1', 'hangar2', 'hangar3', 'hangar4', 'hangar5', 'hangar6', 'hangar7', 'hangar8', 'hangar9', 'hangar10'])   
                ->make(true);
        }
        return view('backend.daily-record.index');
    }

    private function formatHangarData($row, $index)
    {
        if (!isset($row['hangars'][$index])) {
            return 'N/A';
        }
        
        $hangar = $row['hangars'][$index];
        $feedKg = number_format((float) $hangar['feed_kg'], 2, ',', '.');
        $eggsWeight = number_format((float) $hangar['eggs_weight'], 2, ',', '.');
        $chicksWeight = number_format((float) $hangar['chicks_weight'], 2, ',', '.');
        
        return '<strong>' . $hangar['hangar_name'] . '</strong><br>' .
               'Feed: ' . $feedKg . ' kg<br>' .
               'Eggs(T): ' . $hangar['eggs_tray_30'] . '<br>' .
               'Eggs(C): ' . $hangar['eggs_count'] . '<br>' .
               'Eggs Weight: ' . $eggsWeight . ' kg<br>' .
               'Chicks Weight: ' . $chicksWeight . ' kg<br>' .
               'Mortality: ' . $hangar['mortality'];
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

        // Get hangars allocated to this flock via FlockHangar (only Active)
        $flockHangars = \App\Models\FlockHangar::where('flock_id', $flockId)
            ->with('hangar')
            ->whereHas('hangar', function ($q) {
                $q->where('status', 'Active');
            })
            ->get()
            ->map(function($allocation) {
                return [
                    'id' => $allocation->hangar->id,
                    'name' => $allocation->hangar->name,
                    'quantity' => $allocation->quantity
                ];
            });
        
        return response()->json($flockHangars);
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'record_date' => 'required|date',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_records' => 'required|json',
        ]);

        // Get farm_id from the selected flock
        $flock = Flock::findOrFail($request->flock_id);
        
        $hangarRecords = json_decode($request->hangar_records, true);
        
        if (empty($hangarRecords)) {
            return back()->withErrors(['hangar_records' => 'Please add at least one hangar record.']);
        }

        // Create daily records for each hangar
        foreach ($hangarRecords as $record) {
            DailyRecord::create([
                'record_date' => $request->record_date,
                'farm_id' => $flock->farm_id,
                'hangar_id' => $record['hangar_id'],
                'flock_id' => $request->flock_id,
                'feed_kg' => $record['feed_kg'] ?? 0,
                'eggs_tray_30' => $record['eggs_tray_30'] ?? 0,
                'eggs_count' => $record['eggs_count'] ?? 0,
                'eggs_weight' => $record['eggs_weight'] ?? 0,
                'chicks_weight' => $record['chicks_weight'] ?? 0,
                'mortality' => $record['mortality'] ?? 0,
                'created_by' => auth()->id()
            ]);
        }

        Session::flash('successMsg', 'Daily Records created successfully.');
        return redirect()->route('daily-record.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $dailyRecord = DailyRecord::findOrFail($id);
        $flocks = FlockHelper::getAllFlockOptions();
        
        // Get all hangars for the selected flock
        $flockHangars = \App\Models\FlockHangar::where('flock_id', $dailyRecord->flock_id)
            ->with('hangar')
            ->get()
            ->map(function($allocation) {
                return [
                    'id' => $allocation->hangar->id,
                    'name' => $allocation->hangar->name,
                    'quantity' => $allocation->quantity
                ];
            });
        
        // Get all existing daily records for this flock on this date to populate form
        $existingRecords = DailyRecord::where('flock_id', $dailyRecord->flock_id)
            ->where('record_date', $dailyRecord->record_date)
            ->get()
            ->keyBy('hangar_id');
        
        return view('backend.daily-record.create', compact('dailyRecord', 'flocks', 'flockHangars', 'existingRecords'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'record_date' => 'required|date',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_records' => 'required|json',
        ]);

        // Get farm_id from the selected flock
        $flock = Flock::findOrFail($request->flock_id);
        
        $hangarRecords = json_decode($request->hangar_records, true);
        
        if (empty($hangarRecords)) {
            return back()->withErrors(['hangar_records' => 'Please add at least one hangar record.']);
        }

        $dailyRecord = DailyRecord::findOrFail($id);
        
        // Delete old records for this flock on this date
        DailyRecord::where('flock_id', $request->flock_id)
            ->where('record_date', $request->record_date)
            ->where('id', '!=', $id)
            ->delete();

        // Update or create records for each hangar
        foreach ($hangarRecords as $index => $record) {
            if ($index === 0) {
                // Update the first (main) record
                $dailyRecord->update([
                    'record_date' => $request->record_date,
                    'farm_id' => $flock->farm_id,
                    'hangar_id' => $record['hangar_id'],
                    'flock_id' => $request->flock_id,
                    'feed_kg' => $record['feed_kg'] ?? 0,
                    'eggs_tray_30' => $record['eggs_tray_30'] ?? 0,
                    'eggs_count' => $record['eggs_count'] ?? 0,
                    'eggs_weight' => $record['eggs_weight'] ?? 0,
                    'chicks_weight' => $record['chicks_weight'] ?? 0,
                    'mortality' => $record['mortality'] ?? 0,
                ]);
            } else {
                // Create additional records
                DailyRecord::create([
                    'record_date' => $request->record_date,
                    'farm_id' => $flock->farm_id,
                    'hangar_id' => $record['hangar_id'],
                    'flock_id' => $request->flock_id,
                    'feed_kg' => $record['feed_kg'] ?? 0,
                    'eggs_tray_30' => $record['eggs_tray_30'] ?? 0,
                    'eggs_count' => $record['eggs_count'] ?? 0,
                    'eggs_weight' => $record['eggs_weight'] ?? 0,
                    'chicks_weight' => $record['chicks_weight'] ?? 0,
                    'mortality' => $record['mortality'] ?? 0,
                    'created_by' => auth()->id()
                ]);
            }
        }

        Session::flash('successMsg', 'Daily Record updated successfully.');
        return redirect()->route('daily-record.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        DailyRecord::findOrFail($id)->delete();
        return response()->json(['msg' => 'Daily Record deleted successfully.']);
    }
}
