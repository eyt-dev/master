<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Flock;
use App\Models\Farm;
use App\Models\ChicksSupplier;
use App\Models\Hangar;
use App\Models\FlockHangar;
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
                    $query->where('created_by', auth()->id());
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

                    $rowData = [
                        'id' => $flock->id,
                        'farm_id' => $flock->farm_id,
                        'farm_name' => $group['farm']->name ?? 'N/A',
                        'name' => $flock->name,
                        'chicks_supplier' => $flock->chicksSupplier->name ?? 'N/A',
                        'breed' => $flock->breed,
                        'start_date' => $flock->start_date,
                        'total_quantity' => $flock->total_quantity,
                        'created_by' => $flock->creator->name ?? 'N/A',
                        'created_at' => $flock->created_at,
                    ];

                    // Merge hangar data into row
                    $rowData = array_merge($rowData, $hangarData);
                    $data[] = $rowData;
                }
            }

            $totalQty = collect($data)->sum('total_quantity');
            $totalFarms = collect($data)->pluck('farm_id')->unique()->count();

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
                ->addColumn('total_qty', function() use ($totalQty) {
                    return $totalQty;
                })
                ->addColumn('total_farm', function() use ($totalFarms) {
                    return $totalFarms;
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
                ->rawColumns(['action', 'breed'])
                ->make(true);
        }

        // No need to fetch all hangar IDs for view since we're using fixed columns
        return view('backend.flock.index');
    }

    public function create()
    {
        $farms = Farm::where('created_by', auth()->id())->orWhere('created_by', function($query) {
            $query->select('id')->from('admins')->where('type', 0);
        })->get();
        
        if (auth()->user()->role === 'SuperAdmin') {
            $farms = Farm::all();
        }

        $chicksSuppliers = ChicksSupplier::all();
        return view('backend.flock.create', compact('farms', 'chicksSuppliers'));
    }

    public function getHangarsByFarm($siteUrl, $farmId)
    {
        // Query hangars for the selected farm
        // Apply the same scoping as HangarController for non-SuperAdmins
        $hangars = Hangar::where('farm_id', $farmId)
            ->where('status', 'Active')
            ->when(auth()->user()->role !== 'SuperAdmin', function ($query) {
                // For non-SuperAdmin, show only hangars they created
                $query->where('created_by', auth()->id());
            })
            ->select('id', 'name')
            ->get();
        
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
            'farm_id' => 'required|exists:farms,id',
            'chicks_supplier_id' => 'required|exists:chicks_suppliers,id',
            'breed' => 'required|string',
            'start_date' => 'required|date',
            'total_quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => 'required|json',
        ]);

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

        $flock = Flock::create([
            'name' => FlockNamingHelper::generateFlockName($request->farm_id),
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
        $flock = Flock::when($user->role !== 'SuperAdmin', function ($query) use ($user) {
            $query->where('created_by', $user->id);
        })->findOrFail($id);

        $farms = Farm::where('created_by', auth()->id())->orWhere('created_by', function($query) {
            $query->select('id')->from('admins')->where('type', 0);
        })->get();

        if (auth()->user()->role === 'SuperAdmin') {
            $farms = Farm::all();
        }

        $chicksSuppliers = ChicksSupplier::all();
        $flockHangars = FlockHangar::where('flock_id', $flock->id)->get();
        // Store original farm_id and name in session for comparison during update
        return view('backend.flock.create', compact('flock', 'farms', 'chicksSuppliers', 'flockHangars'));
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'chicks_supplier_id' => 'required|exists:chicks_suppliers,id',
            'breed' => 'required|string',
            'start_date' => 'required|date',
            'total_quantity' => 'required|numeric|min:1',
            'hangar_quantities_json' => 'required|json',
        ]);

        $user = auth()->user();
        $flock = Flock::when($user->role !== 'SuperAdmin', function ($query) use ($user) {
            $query->where('created_by', $user->id);
        })->findOrFail($id);

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

        // Determine flock name based on farm change
        $newName = $flock->name;
        
        if ($request->farm_id !== $flock->farm_id) {
            // Farm has changed - generate new name for the new farm
            $newName = FlockNamingHelper::generateFlockName($request->farm_id, $flock->id);
        }

        $flock->update([
            'name' => $newName,
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
        $flock = Flock::when($user->role !== 'SuperAdmin', function ($query) use ($user) {
            $query->where('created_by', $user->id);
        })->findOrFail($id);

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
}
