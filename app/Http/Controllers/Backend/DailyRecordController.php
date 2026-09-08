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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                    $breedType = $firstRecord->flock ? $this->extractBreedType($firstRecord->flock->breed) : 'Layer';
                    $breedName = $firstRecord->flock ? $this->extractBreedName($firstRecord->flock->breed) : 'N/A';

                    return [
                        'id' => $firstRecord->id,
                        'record_date' => $firstRecord->record_date,
                        'flock_id' => $firstRecord->flock_id,
                        'flock_label' => $flockLabel,
                        'breed_type' => $breedType,
                        'breed_name' => $breedName,
                        'farm' => $firstRecord->farm->name ?? 'N/A',
                        'created_by' => $firstRecord->creator->name ?? 'N/A',
                        'created_at' => $firstRecord->created_at,
                        'hangars' => $group->map(function($record) {
                            // Get allocated quantity from FlockHangar
                            $flockHangar = FlockHangar::where('flock_id', $record->flock_id)
                                ->where('hangar_id', $record->hangar_id)
                                ->first();

                            return [
                                'hangar_id' => $record->hangar_id,
                                'hangar_name' => $record->hangar->name ?? 'N/A',
                                'allocated_quantity' => $flockHangar->quantity ?? 'N/A',
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
                    return $row['flock_label'] . '<br><small style="color: #666;">' . $row['breed_type'] . ' (' . $row['breed_name'] . ')</small>';
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
                    return $this->formatHangarData($row, 1);
                })
                ->addColumn('hangar2', function($row) {
                    return $this->formatHangarData($row, 2);
                })
                ->addColumn('hangar3', function($row) {
                    return $this->formatHangarData($row, 3);
                })
                ->addColumn('hangar4', function($row) {
                    return $this->formatHangarData($row, 4);
                })
                ->addColumn('hangar5', function($row) {
                    return $this->formatHangarData($row, 5);
                })
                ->addColumn('hangar6', function($row) {
                    return $this->formatHangarData($row, 6);
                })
                ->addColumn('hangar7', function($row) {
                    return $this->formatHangarData($row, 7);
                })
                ->addColumn('hangar8', function($row) {
                    return $this->formatHangarData($row, 8);
                })
                ->addColumn('hangar9', function($row) {
                    return $this->formatHangarData($row, 9);
                })
                ->addColumn('hangar10', function($row) {
                    return $this->formatHangarData($row, 10);
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-daily-record btn btn-sm btn-success mr-1" data-id="'.$row['id'].'" data-path="'.route('daily-record.edit', ['username' => request()->segment(1), 'daily_record' => $row['id']]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-daily-record btn btn-sm btn-danger" data-id="'.$row['id'].'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['flock', 'action', 'hangar1', 'hangar2', 'hangar3', 'hangar4', 'hangar5', 'hangar6', 'hangar7', 'hangar8', 'hangar9', 'hangar10'])
                ->make(true);
        }
        return view('backend.daily-record.index');
    }

    private function extractBreedType($breedString)
    {
        $breedType = 'Layer'; // Default

        if (!empty($breedString)) {
            if (strpos($breedString, ',') !== false) {
                // Format: "Type,BreedName"
                $breedParts = explode(',', $breedString);
                $breedType = trim($breedParts[0]);
            } else {
                // Format: "BreedName" only - infer type from known breed names
                if (stripos($breedString, 'cobb') !== false || stripos($breedString, 'ross') !== false) {
                    $breedType = 'Broiler';
                } elseif (stripos($breedString, 'lohmann') !== false || stripos($breedString, 'hy-line') !== false) {
                    $breedType = 'Layer';
                }
            }
        }

        return $breedType;
    }

    private function extractBreedName($breedString)
    {
        if (empty($breedString)) {
            return 'N/A';
        }

        if (strpos($breedString, ',') !== false) {
            // Format: "Type,BreedName"
            $breedParts = explode(',', $breedString);
            return trim($breedParts[1] ?? 'N/A');
        }

        // Format: "BreedName" only
        return trim($breedString);
    }

    private function formatHangarData($row, $columnNumber)
    {
        // Find hangar by matching hangar number from name
        $hangar = null;
        foreach ($row['hangars'] as $h) {
            preg_match('/(\d+)/', $h['hangar_name'], $matches);
            if (!empty($matches[1]) && intval($matches[1]) === $columnNumber) {
                $hangar = $h;
                break;
            }
        }

        if (!$hangar) {
            return 'N/A';
        }

        $breedType = $row['breed_type'] ?? 'Layer';
        $feedKg = number_format((float) $hangar['feed_kg'], 2, ',', '.');
        $eggsWeight = number_format((float) $hangar['eggs_weight'], 2, ',', '.');
        $chicksWeight = number_format((float) $hangar['chicks_weight'], 2, ',', '.');

        $html = 'Qty: ' . $hangar['allocated_quantity'] . '<br>' .
                'Feed: ' . $feedKg . ' kg<br>' .
                'Mortality: ' . $hangar['mortality'];

        // Show breed-specific fields
        if ($breedType === 'Layer') {
            $html .= '<br>Eggs(T): ' . $hangar['eggs_tray_30'] .
                     '<br>Eggs(C): ' . $hangar['eggs_count'] .
                     '<br>Eggs Weight: ' . $eggsWeight . ' kg';
        } else {
            $html .= '<br>Chicks Weight: ' . $chicksWeight . ' kg';
        }

        return $html;
    }

    public function create()
    {
        $flocks = FlockHelper::getAllFlockOptions();
        return view('backend.daily-record.create', compact('flocks'));
    }

    public function getFlockDetails($siteUrl, $flockId)
    {
        $flockId = (int) $flockId;
        $flock = Flock::findOrFail($flockId);

        return response()->json([
            'id' => $flock->id,
            'name' => FlockHelper::getFlockLabel($flock),
            'breed_name' => $this->extractBreedName($flock->breed),
            'breed_type' => $this->extractBreedType($flock->breed),
            'total_birds' => $flock->total_birds ?? 0,
            'farm_id' => $flock->farm_id
        ]);
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

        return response()->json([
            'hangars' => $flockHangars,
            'breed_type' => $this->extractBreedType($flock->breed)
        ]);
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'record_date' => 'required|date_format:Y-m-d',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_records' => 'nullable|json',
        ]);

        if (!$request->has('hangar_records') || !$request->hangar_records) {
            return back()->withErrors(['hangar_records' => 'Please add at least one hangar record.']);
        }

        $flock = Flock::findOrFail($request->flock_id);
        $breedType = $this->extractBreedType($flock->breed);
        $hangarRecords = json_decode($request->hangar_records, true);

        if (empty($hangarRecords)) {
            return back()->withErrors(['hangar_records' => 'Please add at least one hangar record.']);
        }

        // Validate based on breed type before transaction
        foreach ($hangarRecords as $record) {
            if (!isset($record['feed_kg']) || $record['feed_kg'] === '' || $record['feed_kg'] === null) {
                return back()->withErrors(['hangar_records' => 'Feed (kg) is required.']);
            }
            if (!isset($record['mortality']) || $record['mortality'] === '' || $record['mortality'] === null) {
                return back()->withErrors(['hangar_records' => 'Mortality is required.']);
            }

            if ($breedType === 'Layer') {
                if (!isset($record['eggs_weight']) || $record['eggs_weight'] === '' || $record['eggs_weight'] === null) {
                    return back()->withErrors(['hangar_records' => 'Eggs Weight is required for Layer breeds.']);
                }
            }
        }

        try {
            DB::beginTransaction();

            $recordDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->record_date);

            foreach ($hangarRecords as $record) {
                DailyRecord::create([
                    'record_date' => $recordDate,
                    'farm_id' => $flock->farm_id,
                    'hangar_id' => $record['hangar_id'],
                    'flock_id' => $request->flock_id,
                    'feed_kg' => (float)($record['feed_kg'] ?? 0),
                    'eggs_tray_30' => (int)($record['eggs_tray_30'] ?? 0),
                    'eggs_count' => (int)($record['eggs_count'] ?? 0),
                    'eggs_weight' => (float)($record['eggs_weight'] ?? 0),
                    'chicks_weight' => (float)($record['chicks_weight'] ?? 0),
                    'mortality' => (int)($record['mortality'] ?? 0),
                    'created_by' => auth()->id()
                ]);
            }

            DB::commit();
            Session::flash('successMsg', 'Daily Records created successfully.');
            return redirect()->route('daily-record.index', ['username' => request()->segment(1)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Daily record creation error: ' . $e->getMessage());
            return back()->withErrors('Failed to create daily records: ' . $e->getMessage());
        }
    }

    public function edit($siteUrl, $id)
    {
        $user = auth()->user();
        $dailyRecord = DailyRecord::findOrFail($id);

        // Verify access: user must be SuperAdmin or have created the record
        if ($user->role !== 'SuperAdmin' && $dailyRecord->created_by !== $user->id) {
            abort(403, 'You do not have permission to edit this record.');
        }

        $flocks = FlockHelper::getAllFlockOptions();

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

        $existingRecords = DailyRecord::where('flock_id', $dailyRecord->flock_id)
            ->where('record_date', $dailyRecord->record_date)
            ->get()
            ->keyBy('hangar_id');

        return view('backend.daily-record.create', compact('dailyRecord', 'flocks', 'flockHangars', 'existingRecords'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $user = auth()->user();

        $request->validate([
            'record_date' => 'required|date_format:Y-m-d',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_records' => 'nullable|json',
        ]);

        $dailyRecord = DailyRecord::findOrFail($id);

        // Verify access: user must be SuperAdmin or have created the record
        if ($user->role !== 'SuperAdmin' && $dailyRecord->created_by !== $user->id) {
            abort(403, 'You do not have permission to update this record.');
        }

        if (!$request->has('hangar_records') || !$request->hangar_records) {
            return back()->withErrors(['hangar_records' => 'Please add at least one hangar record.']);
        }

        $flock = Flock::findOrFail($request->flock_id);
        $breedType = $this->extractBreedType($flock->breed);
        $hangarRecords = json_decode($request->hangar_records, true);

        if (empty($hangarRecords)) {
            return back()->withErrors(['hangar_records' => 'Please add at least one hangar record.']);
        }

        // Validate based on breed type before transaction
        foreach ($hangarRecords as $record) {
            if (!isset($record['feed_kg']) || $record['feed_kg'] === '' || $record['feed_kg'] === null) {
                return back()->withErrors(['hangar_records' => 'Feed (kg) is required.']);
            }
            if (!isset($record['mortality']) || $record['mortality'] === '' || $record['mortality'] === null) {
                return back()->withErrors(['hangar_records' => 'Mortality is required.']);
            }

            if ($breedType === 'Layer') {
                if (!isset($record['eggs_weight']) || $record['eggs_weight'] === '' || $record['eggs_weight'] === null) {
                    return back()->withErrors(['hangar_records' => 'Eggs Weight is required for Layer breeds.']);
                }
            }
        }

        try {
            DB::beginTransaction();

            $recordDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->record_date);

            // Delete old records for this flock on this date
            DailyRecord::where('flock_id', $request->flock_id)
                ->where('record_date', $recordDate)
                ->where('id', '!=', $id)
                ->delete();

            // Update or create records for each hangar
            foreach ($hangarRecords as $index => $record) {
                if ($index === 0) {
                    $dailyRecord->update([
                        'record_date' => $recordDate,
                        'farm_id' => $flock->farm_id,
                        'hangar_id' => $record['hangar_id'],
                        'flock_id' => $request->flock_id,
                        'feed_kg' => (float)($record['feed_kg'] ?? 0),
                        'eggs_tray_30' => (int)($record['eggs_tray_30'] ?? 0),
                        'eggs_count' => (int)($record['eggs_count'] ?? 0),
                        'eggs_weight' => (float)($record['eggs_weight'] ?? 0),
                        'chicks_weight' => (float)($record['chicks_weight'] ?? 0),
                        'mortality' => (int)($record['mortality'] ?? 0),
                    ]);
                } else {
                    DailyRecord::create([
                        'record_date' => $recordDate,
                        'farm_id' => $flock->farm_id,
                        'hangar_id' => $record['hangar_id'],
                        'flock_id' => $request->flock_id,
                        'feed_kg' => (float)($record['feed_kg'] ?? 0),
                        'eggs_tray_30' => (int)($record['eggs_tray_30'] ?? 0),
                        'eggs_count' => (int)($record['eggs_count'] ?? 0),
                        'eggs_weight' => (float)($record['eggs_weight'] ?? 0),
                        'chicks_weight' => (float)($record['chicks_weight'] ?? 0),
                        'mortality' => (int)($record['mortality'] ?? 0),
                        'created_by' => auth()->id()
                    ]);
                }
            }

            DB::commit();
            Session::flash('successMsg', 'Daily Record updated successfully.');
            return redirect()->route('daily-record.index', ['username' => request()->segment(1)]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Daily record update error: ' . $e->getMessage());
            return back()->withErrors('Failed to update daily records: ' . $e->getMessage());
        }
    }

    public function destroy($siteUrl, $id)
    {
        $user = auth()->user();
        $dailyRecord = DailyRecord::findOrFail($id);

        // Verify access: user must be SuperAdmin or have created the record
        if ($user->role !== 'SuperAdmin' && $dailyRecord->created_by !== $user->id) {
            return response()->json(['error' => 'You do not have permission to delete this record.'], 403);
        }

        try {
            DB::beginTransaction();
            $dailyRecord->delete();
            DB::commit();
            return response()->json(['msg' => 'Daily Record deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Daily record deletion error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete daily record: ' . $e->getMessage()], 500);
        }
    }
}
