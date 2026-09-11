<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FlockEnd;
use App\Models\FlockEndDetail;
use App\Models\Farm;
use App\Models\Flock;
use App\Models\Hangar;
use App\Models\Slaughter;
use App\Models\Admin;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ChickenSalesController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $data = FlockEnd::with('flock.farm', 'hangar', 'slaughter', 'endedBy')
                ->when($user->role !== 'SuperAdmin', function ($query) use ($user) {
                    $query->whereHas('flock.farm', function ($subQuery) {
                        $subQuery->where('created_by', auth()->id())
                                 ->orWhere('assigned_to', auth()->id());
                    })
                    ->orWhere('ended_by', $user->id);
                })
                ->orderBy('created_at', 'desc')->get();
            return datatables()->of($data)
                ->addColumn('sale_date', function($row) {
                    return date('Y-m-d', strtotime($row->sale_date));
                })
                ->addColumn('farm', function($row) {
                    $farmName = $row->flock->farm->name ?? 'N/A';
                    if ($row->flock->farm && $row->flock->farm->assignedAdmin) {
                        $farmName .= '<br><small style="color: #666;">Assigned to: ' . $row->flock->farm->assignedAdmin->name . '</small>';
                    }
                    return $farmName;
                })
                ->addColumn('flock', function($row) {
                    if (!$row->flock) {
                        return 'N/A';
                    }
                    $breedType = $this->extractBreedType($row->flock->breed);
                    $breedName = $this->extractBreedName($row->flock->breed);
                    $badgeClass = ($breedType === 'Broiler') ? 'badge-danger' : 'badge-info';
                    return '<strong>' . $row->flock->name . '</strong><br><span class="badge ' . $badgeClass . '">' . $breedType . ' (' . $breedName . ')</span>';
                })
                ->addColumn('hangar', function($row) {
                    return $row->hangar->name ?? 'N/A';
                })
                ->addColumn('slaughter', function($row) {
                    return $row->slaughter->name ?? 'N/A';
                })
                ->addColumn('quantity', function($row) {
                    return $row->total_birds_harvested ?? 'N/A';
                })
                ->addColumn('net_weight', function($row) {
                    return $row->total_weight ?? 'N/A';
                })
                ->addColumn('avg_weight_per_bird', function($row) {
                    return round($row->avg_weight_per_bird, 2);
                })
                ->addColumn('creator', function($row) {
                    return $row->endedBy->name ?? 'N/A';
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row->created_at));
                })
                ->addColumn('action', function($row) {
                    return '<a class="edit-chicken-sale btn btn-sm btn-success mr-1" data-path="'.route('chicken-sale.edit', ['username' => request()->segment(1), 'chicken_sale' => $row->id]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-chicken-sale btn btn-sm btn-danger" data-id="'.$row->id.'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'flock', 'farm'])
                ->make(true);
        }
        return view('backend.chicken-sale.index');
    }

    public function create()
    {
        $user = auth()->user();

        if ($user->role === 'SuperAdmin') {
            $farms = Farm::all();
        } else {
            $farms = Farm::where('created_by', $user->id)
                         ->orWhere('assigned_to', $user->id)
                         ->get();
        }

        $slaughters = Slaughter::all();
        $siteSlug = request()->segment(1);
        return view('backend.chicken-sale.create', compact('farms', 'slaughters', 'siteSlug'));
    }

    public function getFlocksByFarm($siteUrl, $farmId)
    {
        $flocks = Flock::where('farm_id', $farmId)->get();
        return response()->json($flocks);
    }

    public function getHangarsByFlock($siteUrl, $flockId)
    {
        $hangars = Hangar::whereHas('flocks', function($query) use ($flockId) {
            $query->where('flock_id', $flockId);
        })->get();
        return response()->json($hangars);
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'sale_date' => 'required|date_format:Y-m-d',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_id' => 'required|exists:hangars,id',
            'slaughter_id' => 'nullable|exists:slaughters,id',
            'cages_weight' => 'required|numeric|min:0.1',
            'cages_count' => 'required|integer|min:1',
            'birds_per_cage' => 'required|integer|min:1|max:25',
            'gross_weight' => 'required|numeric|min:0',
            'batch_weights' => 'nullable|array',
            'batch_weights.*.weight' => 'numeric|min:0',
            'net_weight' => 'required|numeric|min:0',
            'avg_weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $flock = Flock::findOrFail($request->flock_id);
            $hangarAllocation = $flock->flockHangarAllocations()
                ->where('hangar_id', $request->hangar_id)
                ->first();

            if (!$hangarAllocation) {
                return redirect()->back()->withErrors('Hangar is not allocated to this flock.');
            }

            $previousHarvests = FlockEnd::where('flock_id', $flock->id)
                ->where('hangar_id', $request->hangar_id)
                ->sum('total_birds_harvested');

            $availableBirds = $hangarAllocation->quantity - $previousHarvests;
            $totalBirdsHarvested = $request->cages_count * $request->birds_per_cage;

            if ($totalBirdsHarvested > $availableBirds) {
                return redirect()->back()->withErrors("Cannot harvest {$totalBirdsHarvested} birds. Only {$availableBirds} birds available.");
            }

            $remainingBirds = $availableBirds - $totalBirdsHarvested;
            $saleDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->sale_date);

            $flockEnd = FlockEnd::create([
                'flock_id' => $request->flock_id,
                'slaughter_id' => $request->slaughter_id,
                'hangar_id' => $request->hangar_id,
                'sale_date' => $saleDate,
                'cages_count' => $request->cages_count,
                'cages_weight' => $request->cages_weight,
                'birds_per_cage' => $request->birds_per_cage,
                'total_birds_harvested' => $totalBirdsHarvested,
                'available_birds' => $availableBirds,
                'remaining_birds' => $remainingBirds,
                'total_weight' => $request->gross_weight,
                'avg_weight_per_bird' => $request->avg_weight,
                'notes' => $request->notes,
                'ended_by' => auth()->id()
            ]);

            if ($request->has('batch_weights')) {
                $batchWeights = array_filter($request->batch_weights, function($batch) {
                    return isset($batch['weight']) && $batch['weight'] > 0;
                });

                if (!empty($batchWeights)) {
                    // Extract just the weight values for storage (flat array)
                    $weightValues = array_map(function($batch) {
                        return $batch['weight'];
                    }, $batchWeights);

                    FlockEndDetail::create([
                        'flock_end_id' => $flockEnd->id,
                        'batch_number' => 1,
                        'gross_weight' => $request->gross_weight,
                        'batch_weights' => $weightValues,
                    ]);
                }
            }

            DB::commit();
            Session::flash('successMsg', 'Ending flock created successfully.');
            return redirect()->route('chicken-sale.index', ['username' => request()->segment(1)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Failed to create chicken sale: ' . $e->getMessage());
        }
    }

    public function edit($siteUrl, $id)
    {
        $user = auth()->user();
        $flockEnd = FlockEnd::with('batchWeights')->findOrFail($id);

        if ($user->role === 'SuperAdmin') {
            $farms = Farm::all();
        } else {
            $farms = Farm::where('created_by', $user->id)
                         ->orWhere('assigned_to', $user->id)
                         ->get();
        }

        $flocks = Flock::where('farm_id', $flockEnd->flock->farm_id)->get();
        $hangars = Hangar::whereHas('flocks', function($query) use ($flockEnd) {
            $query->where('flock_id', $flockEnd->flock_id);
        })->get();
        $slaughters = Slaughter::all();

        return view('backend.chicken-sale.create', [
            'chickenSale' => $flockEnd,
            'farms' => $farms,
            'flocks' => $flocks,
            'hangars' => $hangars,
            'slaughters' => $slaughters,
            'siteSlug' => $siteUrl,
        ]);
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $flockEnd = FlockEnd::findOrFail($id);

        $request->validate([
            'sale_date' => 'required|date_format:Y-m-d',
            'flock_id' => 'required|exists:flocks,id',
            'hangar_id' => 'required|exists:hangars,id',
            'slaughter_id' => 'nullable|exists:slaughters,id',
            'cages_weight' => 'required|numeric|min:0.1',
            'cages_count' => 'required|integer|min:1',
            'birds_per_cage' => 'required|integer|min:1|max:25',
            'gross_weight' => 'required|numeric|min:0',
            'batch_weights' => 'nullable|array',
            'batch_weights.*.weight' => 'numeric|min:0',
            'net_weight' => 'required|numeric|min:0',
            'avg_weight' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $flock = Flock::findOrFail($request->flock_id);
            $hangarAllocation = $flock->flockHangarAllocations()
                ->where('hangar_id', $request->hangar_id)
                ->first();

            if (!$hangarAllocation) {
                return redirect()->back()->withErrors('Hangar is not allocated to this flock.');
            }

            $previousHarvests = FlockEnd::where('flock_id', $flock->id)
                ->where('hangar_id', $request->hangar_id)
                ->where('id', '!=', $id)
                ->sum('total_birds_harvested');

            $availableBirds = $hangarAllocation->quantity - $previousHarvests;
            $totalBirdsHarvested = $request->cages_count * $request->birds_per_cage;

            if ($totalBirdsHarvested > $availableBirds) {
                return redirect()->back()->withErrors("Cannot harvest {$totalBirdsHarvested} birds. Only {$availableBirds} birds available.");
            }

            $remainingBirds = $availableBirds - $totalBirdsHarvested;
            $saleDate = \Carbon\Carbon::createFromFormat('Y-m-d', $request->sale_date);

            $flockEnd->update([
                'flock_id' => $request->flock_id,
                'slaughter_id' => $request->slaughter_id,
                'hangar_id' => $request->hangar_id,
                'sale_date' => $saleDate,
                'cages_count' => $request->cages_count,
                'cages_weight' => $request->cages_weight,
                'birds_per_cage' => $request->birds_per_cage,
                'total_birds_harvested' => $totalBirdsHarvested,
                'available_birds' => $availableBirds,
                'remaining_birds' => $remainingBirds,
                'total_weight' => $request->gross_weight,
                'avg_weight_per_bird' => $request->avg_weight,
                'notes' => $request->notes,
            ]);

            if ($request->has('batch_weights')) {
                $batchWeights = array_filter($request->batch_weights, function($batch) {
                    return isset($batch['weight']) && $batch['weight'] > 0;
                });

                $detail = FlockEndDetail::where('flock_end_id', $flockEnd->id)->first();

                if (!empty($batchWeights)) {
                    // Extract just the weight values for storage (flat array)
                    $weightValues = array_map(function($batch) {
                        return $batch['weight'];
                    }, $batchWeights);

                    if ($detail) {
                        $detail->update([
                            'gross_weight' => $request->gross_weight,
                            'batch_weights' => $weightValues,
                        ]);
                    } else {
                        FlockEndDetail::create([
                            'flock_end_id' => $flockEnd->id,
                            'batch_number' => 1,
                            'gross_weight' => $request->gross_weight,
                            'batch_weights' => $weightValues,
                        ]);
                    }
                } else if ($detail) {
                    $detail->delete();
                }
            }

            DB::commit();
            Session::flash('successMsg', 'Ending flock updated successfully.');
            return redirect()->route('chicken-sale.index', ['username' => request()->segment(1)]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors('Failed to update chicken sale: ' . $e->getMessage());
        }
    }

    public function destroy($siteUrl, $id)
    {
        try {
            DB::beginTransaction();
            FlockEnd::findOrFail($id)->delete();
            DB::commit();
            return response()->json(['msg' => 'Chicken sale deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to delete chicken sale: ' . $e->getMessage()], 500);
        }
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
}
