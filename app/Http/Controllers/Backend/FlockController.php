<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flock;
use App\Models\Farm;
use App\Models\ChicksSupplier;
use App\Models\Hangar;
use App\Models\FlockHangar;
use App\Models\FlockEnd;
use App\Models\Admin;
use App\Helpers\FlockNamingHelper;
use Illuminate\Support\Facades\Session;

class FlockController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $flocks = Flock::with('farm', 'chicksSupplier', 'creator', 'flockHangarAllocations.hangar')
                ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                    $query->whereHas('farm', function ($subQuery) {
                        $subQuery->where('created_by', auth()->id())
                                 ->orWhere('assigned_to', auth()->id());
                    })
                    ->orWhere('created_by', auth()->id());
                })
                ->orderBy('farm_id', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            // Group flocks by farm
            $farmGroups = [];
            foreach ($flocks as $flock) {
                $farmId = $flock->farm_id;

                if (!isset($farmGroups[$farmId])) {
                    $farmGroups[$farmId] = [
                        'farm' => $flock->farm,
                        'flocks' => []
                    ];
                }

                // Add flock to this farm's group
                $farmGroups[$farmId]['flocks'][] = $flock;
            }

            // Transform into flat array for DataTables with fixed hangar1-10 columns
            $data = [];
            foreach ($farmGroups as $farmId => $group) {
                foreach ($group['flocks'] as $flock) {
                    // Initialize hangar columns 1-10
                    $hangarData = [];
                    for ($i = 1; $i <= 10; $i++) {
                        $hangarData["hangar{$i}"] = '-';
                    }

                    // Map flock's hangar allocations to the fixed columns based on hangar name
                    foreach ($flock->flockHangarAllocations as $allocation) {
                        $hangarName = $allocation->hangar?->name;
                        
                        if ($hangarName) {
                            // Extract hangar number from name (e.g., "Hangar 1" -> 1)
                            preg_match('/(\d+)/', $hangarName, $matches);
                            
                            if (!empty($matches[1])) {
                                $hangarNumber = intval($matches[1]);
                                
                                // Map to hangar1-10 columns
                                if ($hangarNumber >= 1 && $hangarNumber <= 10) {
                                    $hangarData["hangar{$hangarNumber}"] = $allocation->quantity;
                                }
                            }
                        }
                    }

                    $farmDisplay = $group['farm']->name ?? 'N/A';
                    if ($group['farm']->assignedAdmin) {
                        $farmDisplay .= ' (Assigned to: ' . $group['farm']->assignedAdmin->name . ')';
                    }

                    // Calculate total remaining birds by getting the latest remaining_birds for each hangar
                    $remainingBirds = 0;
                    foreach ($flock->flockHangarAllocations as $allocation) {
                        // Get the latest FlockEnd record for this hangar
                        $lastHarvest = FlockEnd::where('flock_id', $flock->id)
                            ->where('hangar_id', $allocation->hangar_id)
                            ->latest()
                            ->first();

                        // If there's a harvest, use remaining_birds from that, otherwise use allocation quantity
                        if ($lastHarvest) {
                            $remainingBirds += $lastHarvest->remaining_birds;
                        } else {
                            $remainingBirds += $allocation->quantity;
                        }
                    }

                    $rowData = [
                        'id' => $flock->id,
                        'farm_id' => $flock->farm_id,
                        'farm_name' => $farmDisplay,
                        'name' => $flock->name,
                        'chicks_supplier' => $flock->chicksSupplier->name ?? 'N/A',
                        'breed' => $flock->breed,
                        'start_date' => $flock->start_date,
                        'birds' => $flock->total_quantity,
                        'remaining_birds' => $remainingBirds,
                        'created_by' => $flock->creator->name ?? 'N/A',
                        'created_at' => $flock->created_at,
                    ];

                    // Merge hangar data into row
                    $rowData = array_merge($rowData, $hangarData);
                    $data[] = $rowData;
                }
            }

            $datatableBuilder = datatables()->of($data)
                ->addColumn('farm', function($row) {
                    return $row['farm_name'];
                })
                ->addColumn('name', function($row) {
                    return $row['name'];
                })
                ->addColumn('chicks_supplier', function($row) {
                    return $row['chicks_supplier'];
                })
                ->addColumn('breed', function($row) {
                    $breedType = $this->extractBreedType($row['breed']);
                    $breedName = $this->extractBreedName($row['breed']);
                    $badgeClass = ($breedType === 'Broiler') ? 'badge-danger' : 'badge-info';
                    return '<span class="badge ' . $badgeClass . '">' . $breedType . ' (' . $breedName . ')</span>';
                })
                ->addColumn('start_date', function($row) {
                    return date('Y-m-d', strtotime($row['start_date']));
                })
                ->addColumn('birds_summary', function($row) {
                    $total = $row['birds'] ?? 0;
                    $remaining = $row['remaining_birds'] ?? 0;
                    return '<div style="font-size: 0.9rem; line-height: 1.6;">
                            <strong>Total:</strong> ' . $total . '<br>
                            <strong>Remaining:</strong> ' . $remaining . '
                            </div>';
                })
                ->addColumn('created_by', function($row) {
                    return $row['created_by'];
                })
                ->addColumn('created_at', function($row) {
                    return date('Y-m-d', strtotime($row['created_at']));
                });

            // Add fixed 10 hangar columns
            for ($i = 1; $i <= 10; $i++) {
                $datatableBuilder->addColumn('hangar' . $i, function($row) use ($i) {
                    return $row['hangar' . $i];
                });
            }

            return $datatableBuilder
                ->addColumn('action', function($row) {
                    return '<a class="edit-flock btn btn-sm btn-success mr-1" data-path="'.route('flock.edit', ['username' => request()->segment(1), 'flock' => $row['id']]).'" title="Edit"><i class="fa fa-edit"></i></a>'
                         .'<a class="delete-flock btn btn-sm btn-danger" data-id="'.$row['id'].'" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'breed', 'birds_summary'])
                ->make(true);
        }

        // No need to fetch all hangar IDs for view since we're using fixed columns
        return view('backend.flock.index');
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

        $chicksSuppliers = ChicksSupplier::all();
        return view('backend.flock.create', compact('farms', 'chicksSuppliers'));
    }

    public function getFirstFlockBreedCategory($siteUrl, $farmId)
    {
        $user = auth()->user();

        // Verify user has access to this farm
        if ($user->role !== 'SuperAdmin') {
            $farm = Farm::where('id', $farmId)
                ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                          ->orWhere('assigned_to', $user->id);
                })
                ->first();

            if (!$farm) {
                return response()->json(['category' => null]);
            }
        }

        // Get the first flock for this farm (oldest by created_at)
        $firstFlock = Flock::where('farm_id', $farmId)
            ->oldest('created_at')
            ->select('breed')
            ->first();

        if (!$firstFlock) {
            return response()->json(['category' => null]);
        }

        $category = $this->extractBreedType($firstFlock->breed);
        return response()->json(['category' => $category]);
    }

    public function getHangarsByFarm($siteUrl, $farmId)
    {
        $user = auth()->user();

        // Verify user has access to this farm
        if ($user->role !== 'SuperAdmin') {
            $farm = Farm::where('id', $farmId)
                ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                          ->orWhere('assigned_to', $user->id);
                })
                ->first();

            if (!$farm) {
                return response()->json([]);
            }
        }

        // Query hangars for the selected farm
        $hangars = Hangar::where('farm_id', $farmId)
            ->get(['id', 'name']);

        // Get flock ID from query parameter if editing
        $flockId = request()->query('flock_id');

        // Get hangars that already have flocks allocated (excluding current flock if editing)
        // Include the flock breed info for display
        $allocatedHangars = FlockHangar::when($flockId, function ($query) use ($flockId) {
            return $query->where('flock_id', '!=', $flockId);
        })
            ->with('flock:id,breed')
            ->get()
            ->keyBy('hangar_id');

        // Add disabled flag and breed info to each hangar
        $hangars = $hangars->map(function ($hangar) use ($allocatedHangars) {
            $allocation = $allocatedHangars->get($hangar->id);
            // Only consider a hangar allocated if both allocation and flock exist
            $hasValidAllocation = $allocation && $allocation->flock;
            $breed = $hasValidAllocation ? $this->extractBreedName($allocation->flock->breed) : null;
            $allocatedQty = $hasValidAllocation ? $allocation->quantity : null;

            return [
                'id' => $hangar->id,
                'name' => $hangar->name,
                'disabled' => $hasValidAllocation ? true : false,
                'allocated' => $hasValidAllocation ? true : false,
                'breed' => $breed,
                'allocated_quantity' => $allocatedQty,
            ];
        });

        return response()->json($hangars);
    }

    public function checkDuplicate(Request $request)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'chicks_supplier_id' => 'required|exists:chicks_suppliers,id',
            'breed' => 'required|string',
            'start_date' => 'required|date',
        ]);

        $exists = Flock::where('farm_id', $request->farm_id)
            ->where('chicks_supplier_id', $request->chicks_supplier_id)
            ->where('breed', $request->breed)
            ->where('start_date', $request->start_date)
            ->exists();

        return response()->json(['exists' => $exists]);
    }

    public function getSequenceNumber(Request $request)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id'
        ]);

        $sequence = FlockNamingHelper::getNextSequenceNumber($request->farm_id);
        return response()->json(['sequence' => $sequence]);
    }

    public function store(Request $request, $siteUrl)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'farm_id' => 'required|exists:farms,id',
            'chicks_supplier_id' => 'required|exists:chicks_suppliers,id',
            'breed' => 'required|string',
            'start_date' => 'required|date|before_or_equal:today',
            'total_quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => 'required|json',
        ]);

        // Validate breed category matches farm's first flock
        $this->validateBreedCategory($request->farm_id, $request->breed);

        // Check if a flock with the same farm, chicks_supplier, breed, and start_date already exists
        $existingFlock = Flock::where('farm_id', $request->farm_id)
            ->where('chicks_supplier_id', $request->chicks_supplier_id)
            ->where('breed', $request->breed)
            ->where('start_date', $request->start_date)
            ->first();

        if ($existingFlock) {
            return back()->withErrors(['unique_combination' => 'A flock with the same Farm, Chicks Supplier, Breed, and Start Date already exists.']);
        }

        $hangarQuantities = json_decode($request->hangar_quantities_json, true);

        if (empty($hangarQuantities)) {
            return back()->withErrors(['hangar_quantities_json' => 'Please select at least one hangar with quantity.']);
        }

        // Check for duplicate hangars in the submission
        $hangarIds = array_column($hangarQuantities, 'hangar_id');
        if (count($hangarIds) !== count(array_unique($hangarIds))) {
            return back()->withErrors(['hangar_quantities_json' => 'Duplicate hangars are not allowed. Each hangar can only be selected once.']);
        }

        // Validate that hangars aren't already allocated to other flocks in the same farm
        $allocatedHangars = FlockHangar::whereHas('flock', function ($q) use ($request) {
            $q->where('farm_id', $request->farm_id);
        })->pluck('hangar_id')->toArray();

        $requestHangarIds = array_column($hangarQuantities, 'hangar_id');
        $doubleAllocated = array_intersect($requestHangarIds, $allocatedHangars);

        if (!empty($doubleAllocated)) {
            return back()->withErrors(['hangar_quantities_json' => 'One or more hangars are already allocated to another flock in this farm. Hangars: ' . implode(', ', $doubleAllocated)]);
        }

        $flock = Flock::create([
            'name' => $request->name,
            'farm_id' => $request->farm_id,
            'chicks_supplier_id' => $request->chicks_supplier_id,
            'breed' => $request->breed,
            'start_date' => $request->start_date,
            'total_quantity' => $request->total_quantity,
            'created_by' => auth()->id()
        ]);

        // Save hangar allocations
        foreach ($hangarQuantities as $allocation) {
            FlockHangar::create([
                'flock_id' => $flock->id,
                'hangar_id' => $allocation['hangar_id'],
                'quantity' => $allocation['quantity']
            ]);
        }

        Session::flash('successMsg', 'Flock created successfully.');
        return redirect()->route('flock.index', ['username' => request()->segment(1)]);
    }

    public function edit($siteUrl, $id)
    {
        $user = auth()->user();
        $flock = Flock::with('farm')->findOrFail($id);

        // Verify access to the farm
        if ($user->role !== 'SuperAdmin') {
            $farm = $flock->farm;
            $hasAccess = $farm && (
                $farm->created_by === $user->id ||
                $farm->assigned_to === $user->id
            );

            if (!$hasAccess) {
                abort(403, 'You do not have permission to edit this flock.');
            }
        }

        if ($user->role === 'SuperAdmin') {
            $farms = Farm::all();
        } else {
            $farms = Farm::where('created_by', $user->id)
                         ->orWhere('assigned_to', $user->id)
                         ->get();
        }

        $chicksSuppliers = ChicksSupplier::all();
        $flockHangars = FlockHangar::where('flock_id', $flock->id)->get();
        return view('backend.flock.create', compact('flock', 'farms', 'chicksSuppliers', 'flockHangars'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'farm_id' => 'required|exists:farms,id',
            'chicks_supplier_id' => 'required|exists:chicks_suppliers,id',
            'breed' => 'required|string',
            'start_date' => 'required|date|before_or_equal:today',
            'total_quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => 'required|json',
        ]);

        $user = auth()->user();
        $flock = Flock::with('farm')->findOrFail($id);

        // Verify access to the farm
        if ($user->role !== 'SuperAdmin') {
            $farm = $flock->farm;
            $hasAccess = $farm && (
                $farm->created_by === $user->id ||
                $farm->assigned_to === $user->id
            );

            if (!$hasAccess) {
                return redirect()->back()->withErrors('You do not have permission to update this flock.');
            }
        }

        // Validate breed category matches farm's first flock (skip if this is the first flock)
        $this->validateBreedCategory($request->farm_id, $request->breed, $id);

        // Check if another flock with the same farm, chicks_supplier, breed, and start_date exists (exclude current flock)
        $existingFlock = Flock::where('farm_id', $request->farm_id)
            ->where('chicks_supplier_id', $request->chicks_supplier_id)
            ->where('breed', $request->breed)
            ->where('start_date', $request->start_date)
            ->where('id', '!=', $id)
            ->first();

        if ($existingFlock) {
            return back()->withErrors(['unique_combination' => 'A flock with the same Farm, Chicks Supplier, Breed, and Start Date already exists.']);
        }

        $hangarQuantities = json_decode($request->hangar_quantities_json, true);

        if (empty($hangarQuantities)) {
            return back()->withErrors(['hangar_quantities_json' => 'Please select at least one hangar with quantity.']);
        }

        // Check for duplicate hangars in the submission
        $hangarIds = array_column($hangarQuantities, 'hangar_id');
        if (count($hangarIds) !== count(array_unique($hangarIds))) {
            return back()->withErrors(['hangar_quantities_json' => 'Duplicate hangars are not allowed. Each hangar can only be selected once.']);
        }

        // Validate that hangars aren't already allocated to other flocks in the same farm
        // (excluding the current flock being updated)
        $allocatedHangars = FlockHangar::whereHas('flock', function ($q) use ($request, $id) {
            $q->where('farm_id', $request->farm_id)
              ->where('id', '!=', $id);
        })->pluck('hangar_id')->toArray();

        $requestHangarIds = array_column($hangarQuantities, 'hangar_id');
        $doubleAllocated = array_intersect($requestHangarIds, $allocatedHangars);

        if (!empty($doubleAllocated)) {
            return back()->withErrors(['hangar_quantities_json' => 'One or more hangars are already allocated to another flock in this farm. Hangars: ' . implode(', ', $doubleAllocated)]);
        }

        // Update flock with user-provided name
        $flock->update([
            'name' => $request->name,
            'farm_id' => $request->farm_id,
            'chicks_supplier_id' => $request->chicks_supplier_id,
            'breed' => $request->breed,
            'start_date' => $request->start_date,
            'total_quantity' => $request->total_quantity,
        ]);

        // Delete old allocations
        FlockHangar::where('flock_id', $flock->id)->delete();

        // Save new allocations
        foreach ($hangarQuantities as $allocation) {
            FlockHangar::create([
                'flock_id' => $flock->id,
                'hangar_id' => $allocation['hangar_id'],
                'quantity' => $allocation['quantity']
            ]);
        }

        Session::flash('successMsg', 'Flock updated successfully.');
        return redirect()->route('flock.index', ['username' => request()->segment(1)]);
    }

    public function destroy($siteUrl, $id)
    {
        $user = auth()->user();
        $flock = Flock::with('farm')->findOrFail($id);

        // Verify access to the farm
        if ($user->role !== 'SuperAdmin') {
            $farm = $flock->farm;
            $hasAccess = $farm && (
                $farm->created_by === $user->id ||
                $farm->assigned_to === $user->id
            );

            if (!$hasAccess) {
                return response()->json(['error' => 'You do not have permission to delete this flock.'], 403);
            }
        }

        // Check for dependent records before deletion
        $dependentRecords = [];

        $dailyRecordsCount = \App\Models\DailyRecord::where('flock_id', $flock->id)->count();
        if ($dailyRecordsCount > 0) {
            $dependentRecords['Daily Records'] = $dailyRecordsCount;
        }

        $flockEndsCount = \App\Models\FlockEnd::where('flock_id', $flock->id)->count();
        if ($flockEndsCount > 0) {
            $dependentRecords['Flock End Records'] = $flockEndsCount;
        }

        if (!empty($dependentRecords)) {
            $message = 'Cannot delete flock "' . $flock->name . '". The following related data exists: ';
            $details = [];
            foreach ($dependentRecords as $type => $count) {
                $details[] = $count . ' ' . $type;
            }
            $message .= implode(', ', $details) . '. Please remove all related data before deleting this flock.';

            return response()->json([
                'msg' => $message,
                'error' => true,
                'dependentRecords' => $dependentRecords
            ], 422);
        }

        $flock->delete();
        return response()->json(['msg' => 'Flock deleted successfully.']);
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

    private function validateBreedCategory($farmId, $selectedBreed, $editingFlockId = null)
    {
        // Get the first flock for this farm (ordered by creation date)
        $firstFlock = Flock::where('farm_id', $farmId)
            ->oldest('created_at')
            ->select('id', 'breed')
            ->first();

        // If there's no existing flock, the selected breed establishes the farm's category (allowed)
        if (!$firstFlock) {
            return;
        }

        // If editing and this is the first flock, don't enforce category (it sets the category)
        if ($editingFlockId && $firstFlock->id === $editingFlockId) {
            return;
        }

        // Validate that the selected breed matches the first flock's category
        $firstFlockCategory = $this->extractBreedType($firstFlock->breed);
        $selectedCategory = $this->extractBreedType($selectedBreed);

        if ($firstFlockCategory !== $selectedCategory) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'breed' => "The breed category must match the Farm's first flock category ({$firstFlockCategory}). Selected breed category: {$selectedCategory}."
            ]);
        }
    }
}
