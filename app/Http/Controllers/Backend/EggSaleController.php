<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use App\Models\Admin;
use App\Models\EggSale;
use App\Models\EggWholesaler;
use App\Models\Farm;
use App\Models\Flock;

class EggSaleController extends Controller
{
    private const NOT_LAYER_MESSAGE = 'Only farms whose first flock is a layer can be used for egg sales.';

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $data = EggSale::with('farm', 'flock.farm', 'wholesaler', 'creator')
                ->when($user->role !== 'SuperAdmin', function ($query) use ($user) {
                    $query->where('created_by', $user->id)
                          ->orWhereHas('farm', function ($farmQuery) use ($user) {
                              $farmQuery->where('created_by', $user->id)
                                        ->orWhere('assigned_to', $user->id);
                          });
                })
                ->orderBy('sale_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $stock = EggSale::stockByFlock($data->pluck('flock_id'));

            return datatables()->of($data)
                ->addColumn('sale_date', function ($row) {
                    return $row->sale_date->format('Y-m-d');
                })
                ->addColumn('farm', function ($row) {
                    return $row->farm->name ?? 'N/A';
                })
                ->addColumn('flock', function ($row) {
                    return $row->flock->name ?? 'N/A';
                })
                ->addColumn('wholesaler', function ($row) {
                    return $row->wholesaler->name ?? 'N/A';
                })
                ->addColumn('tray', function ($row) {
                    return $row->tray;
                })
                ->addColumn('total_eggs', function ($row) use ($stock) {
                    $remaining = $stock[$row->flock_id]['remaining'] ?? 0;

                    return 'Total: ' . number_format($row->total_eggs) . '<br>Remaining: ' . number_format($remaining);
                })
                ->addColumn('box', function ($row) {
                    return number_format($row->box, 2);
                })
                ->addColumn('creator', function ($row) {
                    return $row->creator->name ?? 'N/A';
                })
                ->addColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d');
                })
                ->addColumn('action', function ($row) {
                    return '<a class="edit-egg-sale btn btn-sm btn-success mr-1" data-path="' . route('egg-sale.edit', ['username' => request()->segment(1), 'egg_sale' => $row->id]) . '" title="Edit"><i class="fa fa-edit"></i></a>'
                         . '<a class="delete-egg-sale btn btn-sm btn-danger" data-id="' . $row->id . '" title="Delete"><i class="fa fa-trash"></i></a>';
                })
                ->addIndexColumn()
                ->rawColumns(['action', 'total_eggs'])
                ->make(true);
        }

        return view('backend.egg-sale.index');
    }

    public function create()
    {
        $user = auth()->user();

        return view('backend.egg-sale.create', [
            'farms' => $this->accessibleFarms($user),
            'wholesalers' => $this->accessibleWholesalers(),
            'siteSlug' => request()->segment(1),
        ]);
    }

    public function getFlocksByFarm($siteUrl, $farmId)
    {
        $user = auth()->user();
        $farm = Farm::find($farmId);

        if (!$farm) {
            return response()->json([], 404);
        }

        if (!$this->userCanAccessFarm($user, $farm)) {
            return response()->json([], 403);
        }

        $flocks = Flock::where('farm_id', $farm->id)->get();
        $flocks->each(function ($flock) use ($farm) {
            $flock->display_name = ($farm->name ?? 'N/A') . '-' . $flock->name;
        });

        return response()->json($flocks);
    }

    public function store(Request $request, $siteUrl)
    {
        $validated = $this->validateSale($request);

        $farm = Farm::findOrFail($validated['farm_id']);
        if (!$this->userCanAccessFarm(auth()->user(), $farm)) {
            return redirect()->back()->withErrors('You do not have permission to create an egg sale for this farm.')->withInput();
        }

        if (!in_array($farm->id, $this->layerFarmIds(), true)) {
            return redirect()->back()->withErrors(['farm_id' => self::NOT_LAYER_MESSAGE])->withInput();
        }

        return DB::transaction(function () use ($validated) {
            // Lock the flock row so two concurrent sales cannot both claim the same eggs
            Flock::whereKey($validated['flock_id'])->lockForUpdate()->first();

            $message = $this->availabilityIssue($validated);
            if ($message) {
                return redirect()->back()->withErrors(['tray' => $message])->withInput();
            }

            EggSale::create(array_merge($this->payload($validated), [
                'created_by' => auth()->id(),
            ]));

            Session::flash('successMsg', 'Egg sale created successfully.');
            return redirect()->route('egg-sale.index', ['username' => request()->segment(1)]);
        });
    }

    /**
     * Available eggs for a flock, used by the form to show and validate the tray count.
     */
    public function getAvailableEggs(Request $request, $siteUrl, $flockId)
    {
        $flock = Flock::with('farm')->find($flockId);

        if (!$flock) {
            return response()->json(['error' => 'Flock not found.'], 404);
        }

        if (!$this->userCanAccessFarm(auth()->user(), $flock->farm)) {
            return response()->json(['error' => 'You do not have permission to access this flock.'], 403);
        }

        $exceptId = $request->integer('exclude') ?: null;

        return response()->json([
            'total_eggs' => EggSale::stockByFlock([$flock->id])[$flock->id]['total'],
            'available_eggs' => EggSale::availableEggsForFlock($flock->id, $exceptId),
        ]);
    }

    public function edit($siteUrl, $id)
    {
        $user = auth()->user();
        $eggSale = EggSale::with('farm', 'flock')->findOrFail($id);

        if (!$this->userCanAccessFarm($user, $eggSale->farm)) {
            abort(403, 'You do not have permission to edit this egg sale.');
        }

        return view('backend.egg-sale.create', [
            'eggSale' => $eggSale,
            'farms' => $this->accessibleFarms($user, $eggSale->farm_id),
            'flocks' => Flock::where('farm_id', $eggSale->farm_id)->get(),
            'wholesalers' => $this->accessibleWholesalers(),
            'siteSlug' => $siteUrl,
        ]);
    }

    public function update(Request $request, $siteUrl, $id)
    {
        $user = auth()->user();
        $eggSale = EggSale::with('farm')->findOrFail($id);

        if (!$this->userCanAccessFarm($user, $eggSale->farm)) {
            return redirect()->back()->withErrors('You do not have permission to update this egg sale.');
        }

        $validated = $this->validateSale($request);

        $farm = Farm::findOrFail($validated['farm_id']);
        if (!$this->userCanAccessFarm($user, $farm)) {
            return redirect()->back()->withErrors('You do not have permission to use this farm.')->withInput();
        }

        // Only re-check the layer rule when the farm is being changed
        if ($farm->id !== $eggSale->farm_id && !in_array($farm->id, $this->layerFarmIds(), true)) {
            return redirect()->back()->withErrors(['farm_id' => self::NOT_LAYER_MESSAGE])->withInput();
        }

        return DB::transaction(function () use ($eggSale, $validated) {
            Flock::whereKey($validated['flock_id'])->lockForUpdate()->first();

            $message = $this->availabilityIssue($validated, $eggSale->id);
            if ($message) {
                return redirect()->back()->withErrors(['tray' => $message])->withInput();
            }

            $eggSale->update($this->payload($validated));

            Session::flash('successMsg', 'Egg sale updated successfully.');
            return redirect()->route('egg-sale.index', ['username' => request()->segment(1)]);
        });
    }

    /**
     * Error message when the tray count needs more eggs than the flock has left; null when it fits.
     */
    private function availabilityIssue(array $validated, ?int $exceptSaleId = null): ?string
    {
        $available = EggSale::availableEggsForFlock((int) $validated['flock_id'], $exceptSaleId);

        return EggSale::trayLimitMessage((int) $validated['tray'], $available);
    }

    public function destroy($siteUrl, $id)
    {
        $user = auth()->user();
        $eggSale = EggSale::with('farm')->findOrFail($id);

        if (!$this->userCanAccessFarm($user, $eggSale->farm)) {
            return response()->json(['error' => 'You do not have permission to delete this egg sale.'], 403);
        }

        $eggSale->delete();

        return response()->json(['msg' => 'Egg sale deleted successfully.']);
    }

    private function validateSale(Request $request): array
    {
        return $request->validate([
            'sale_date' => 'required|date_format:Y-m-d',
            'farm_id' => 'required|exists:farms,id',
            'flock_id' => [
                'required',
                Rule::exists('flocks', 'id')->where('farm_id', $request->farm_id),
            ],
            'egg_wholesaler_id' => 'required|exists:egg_wholesalers,id',
            'tray' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ], [
            'flock_id.exists' => 'The selected flock does not belong to the selected farm.',
        ]);
    }

    /**
     * Total eggs and boxes are always derived on the server, never trusted from the form.
     */
    private function payload(array $validated): array
    {
        $calc = EggSale::calculate((int) $validated['tray']);

        return [
            'sale_date' => $validated['sale_date'],
            'farm_id' => $validated['farm_id'],
            'flock_id' => $validated['flock_id'],
            'egg_wholesaler_id' => $validated['egg_wholesaler_id'],
            'tray' => (int) $validated['tray'],
            'total_eggs' => $calc['total_eggs'],
            'box' => $calc['box'],
            'notes' => $validated['notes'] ?? null,
        ];
    }

    /**
     * Farms offered in the dropdown: only farms whose first flock is a layer.
     * $includeFarmId keeps the sale's current farm visible when editing.
     */
    private function accessibleFarms(Admin $user, ?int $includeFarmId = null)
    {
        $farms = $user->role === 'SuperAdmin'
            ? Farm::all()
            : Farm::where('created_by', $user->id)
                  ->orWhere('assigned_to', $user->id)
                  ->get();

        $layerFarmIds = $this->layerFarmIds();

        return $farms
            ->filter(fn ($farm) => in_array($farm->id, $layerFarmIds, true) || $farm->id === $includeFarmId)
            ->values();
    }

    /**
     * IDs of farms whose first flock (lowest id) has a Layer breed.
     */
    private function layerFarmIds(): array
    {
        $firstFlockIds = Flock::selectRaw('MIN(id) as id')
            ->groupBy('farm_id')
            ->pluck('id');

        return Flock::whereIn('id', $firstFlockIds)
            ->get(['farm_id', 'breed'])
            ->filter(fn ($flock) => $this->breedType($flock->breed) === 'Layer')
            ->pluck('farm_id')
            ->all();
    }

    /**
     * Same rule as the chicken sale: "Type,BreedName" uses Type; a bare name is matched to known breeds; default Layer.
     */
    private function breedType(?string $breed): string
    {
        if (empty($breed)) {
            return 'Layer';
        }

        if (strpos($breed, ',') !== false) {
            return trim(explode(',', $breed)[0]);
        }

        if (stripos($breed, 'cobb') !== false || stripos($breed, 'ross') !== false) {
            return 'Broiler';
        }

        return 'Layer';
    }

    /**
     * All wholesalers are offered: they are shared reference data, and a sale may be entered by any user
     * with access to the farm, not only by the user who created the wholesaler.
     */
    private function accessibleWholesalers()
    {
        return EggWholesaler::orderBy('name')->get();
    }

    private function userCanAccessFarm(Admin $user, ?Farm $farm): bool
    {
        if ($user->role === 'SuperAdmin') {
            return true;
        }

        return $farm !== null && (
            $farm->created_by === $user->id ||
            $farm->assigned_to === $user->id
        );
    }
}
