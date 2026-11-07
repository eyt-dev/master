<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\EggSale;
use App\Models\Flock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * @group Add2Farm Egg Sales
 * Egg sales (tray based) of layer flocks
 */
class EggSaleController extends BaseController
{
    /**
     * List egg sales
     *
     * Lists egg sales of the accessible farms, newest first. Optionally filter by farm or flock.
     *
     * `total_eggs` = tray × 30 and `box` = total_eggs ÷ 360 (both derived on the server).
     *
     * `egg_stock` shows, per flock, the eggs produced (daily records), the eggs sold, and the eggs remaining.
     * It follows the same `farm_id` / `flock_id` filters and is not paginated.
     *
     * @authenticated
     * @queryParam farm_id integer optional Only sales of this farm. Example: 3
     * @queryParam flock_id integer optional Only sales of this flock. Example: 7
     * @queryParam page integer optional Page number. Default: 1. Example: 1
     * @queryParam per_page integer optional Sales per page (max 100). Default: 20. Example: 20
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Egg sales retrieved successfully.",
     *   "egg_stock": [
     *     {
     *       "flock_id": 7,
     *       "flock_name": "Flock 7",
     *       "farm_id": 3,
     *       "farm_name": "Green Valley",
     *       "eggs_produced": 5400,
     *       "eggs_sold": 3000,
     *       "remaining_eggs": 2400,
     *       "remaining_trays": 80
     *     }
     *   ],
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 5,
     *         "sale_date": "08-11-2026",
     *         "farm_id": 3,
     *         "farm_name": "Green Valley",
     *         "flock_id": 7,
     *         "flock_name": "Flock 7",
     *         "egg_wholesaler_id": 2,
     *         "egg_wholesaler_name": "Al Noor Traders",
     *         "tray": 100,
     *         "total_eggs": 3000,
     *         "box": "8,33",
     *         "notes": null,
     *         "created_by": 1,
     *         "created_at": "2026-11-08T09:30:00.000000Z"
     *       }
     *     ],
     *     "last_page": 1,
     *     "per_page": 20,
     *     "total": 1
     *   }
     * }
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return $this->unauthenticated();
        }

        $perPage = min(max((int) $request->query('per_page', 20), 1), 100);

        $sales = $this->accessibleSalesQuery()
            ->with('farm', 'flock', 'wholesaler')
            ->when($request->query('farm_id'), fn ($q, $farmId) => $q->where('farm_id', $farmId))
            ->when($request->query('flock_id'), fn ($q, $flockId) => $q->where('flock_id', $flockId))
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(fn (EggSale $sale) => $this->saleItem($sale));

        return response()->json([
            'success' => true,
            'message' => 'Egg sales retrieved successfully.',
            'egg_stock' => $this->eggStock($request),
            'data' => $sales,
        ]);
    }

    /**
     * Create an egg sale
     *
     * Records an egg sale for a flock. `sale_date` uses the `d-m-Y` format.
     *
     * The flock must belong to the farm, the farm and the wholesaler must be accessible to the user,
     * and `tray` must not exceed the eggs still available for the flock
     * (eggs produced in daily records minus eggs already sold). Total eggs and boxes are calculated on the server.
     *
     * @authenticated
     * @bodyParam sale_date string required Sale date in d-m-Y format. Example: 08-11-2026
     * @bodyParam farm_id integer required Farm the sale belongs to. Example: 3
     * @bodyParam flock_id integer required Flock of that farm. Example: 7
     * @bodyParam egg_wholesaler_id integer required Egg wholesaler buying the eggs. Example: 2
     * @bodyParam tray integer required Number of trays sold (1 tray = 30 eggs). Example: 100
     * @bodyParam notes string optional Free text, max 1000 characters. Example: Morning collection
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Egg sale created successfully.",
     *   "data": {
     *     "id": 5,
     *     "sale_date": "08-11-2026",
     *     "farm_id": 3,
     *     "farm_name": "Green Valley",
     *     "flock_id": 7,
     *     "flock_name": "Flock 7",
     *     "egg_wholesaler_id": 2,
     *     "egg_wholesaler_name": "Al Noor Traders",
     *     "tray": 100,
     *     "total_eggs": 3000,
     *     "box": "8,33",
     *     "notes": "Morning collection",
     *     "created_by": 1,
     *     "created_at": "2026-11-08T09:30:00.000000Z"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "tray": ["Only 2400 eggs are available for this flock. Maximum is 80 tray(s) (2400 eggs)."]
     *   }
     * }
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return $this->unauthenticated();
        }

        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'sale_date' => 'required|date_format:d-m-Y',
            'farm_id' => 'required|integer|exists:farms,id',
            'flock_id' => [
                'required',
                'integer',
                Rule::exists('flocks', 'id')->where('farm_id', $request->input('farm_id')),
            ],
            'egg_wholesaler_id' => 'required|integer|exists:egg_wholesalers,id',
            'tray' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ], [
            'flock_id.exists' => 'The selected flock does not belong to the selected farm.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        if (!$this->getAccessibleFarmIds()->contains((int) $validated['farm_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create an egg sale for this farm.',
            ], 403);
        }

        $saleDate = Carbon::createFromFormat('d-m-Y', $validated['sale_date'])->format('Y-m-d');

        try {
            $sale = DB::transaction(function () use ($validated, $saleDate, $user) {
                // Lock the flock row so two concurrent sales cannot both claim the same eggs
                Flock::whereKey($validated['flock_id'])->lockForUpdate()->first();

                $tray = (int) $validated['tray'];
                $available = EggSale::availableEggsForFlock((int) $validated['flock_id']);

                $message = EggSale::trayLimitMessage($tray, $available);
                if ($message) {
                    throw new \DomainException($message);
                }

                $calc = EggSale::calculate($tray);

                return EggSale::create([
                    'sale_date' => $saleDate,
                    'farm_id' => $validated['farm_id'],
                    'flock_id' => $validated['flock_id'],
                    'egg_wholesaler_id' => $validated['egg_wholesaler_id'],
                    'tray' => $tray,
                    'total_eggs' => $calc['total_eggs'],
                    'box' => $calc['box'],
                    'notes' => $validated['notes'] ?? null,
                    'created_by' => $user->id,
                ]);
            });
        } catch (\DomainException $e) {
            return response()->json([
                'success' => false,
                'errors' => ['tray' => [$e->getMessage()]],
            ], 422);
        }

        $item = $this->saleItem($sale->load('farm', 'flock', 'wholesaler'));
        $item['flock_remaining_eggs'] = EggSale::availableEggsForFlock($sale->flock_id);

        return response()->json([
            'success' => true,
            'message' => 'Egg sale created successfully.',
            'data' => $item,
        ], 201);
    }

    /**
     * Remaining eggs for one flock.
     *
     * Total = eggs produced (daily records). Sold = eggs already sold from the flock.
     * Available = Total minus Sold (never negative); a sale's tray count must fit within it.
     *
     * @authenticated
     * @urlParam flock_id integer required Flock id. Example: 6
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Available eggs retrieved successfully.",
     *   "data": {
     *     "flock_id": 6,
     *     "flock_name": "(1) 05/2026",
     *     "farm_id": 5,
     *     "farm_name": "Green Valley",
     *     "total_eggs": 36068,
     *     "sold_eggs": 900,
     *     "available_eggs": 35168,
     *     "available_trays": 1172
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Flock not found."
     * }
     */
    public function availableEggs(int $flockId)
    {
        if (!auth()->check()) {
            return $this->unauthenticated();
        }

        $flock = Flock::with('farm:id,name')
            ->whereIn('farm_id', $this->getAccessibleFarmIds())
            ->find($flockId, ['id', 'name', 'farm_id']);

        if (!$flock) {
            return response()->json([
                'success' => false,
                'message' => 'Flock not found.',
            ], 404);
        }

        $stock = EggSale::stockByFlock([$flock->id])[$flock->id];

        return response()->json([
            'success' => true,
            'message' => 'Available eggs retrieved successfully.',
            'data' => [
                'flock_id' => $flock->id,
                'flock_name' => $flock->name,
                'farm_id' => $flock->farm_id,
                'farm_name' => $flock->farm->name ?? null,
                'total_eggs' => $stock['total'],
                'sold_eggs' => $stock['sold'],
                'available_eggs' => $stock['remaining'],
                'available_trays' => intdiv($stock['remaining'], EggSale::EGGS_PER_TRAY),
            ],
        ]);
    }

    /**
     * Egg sales of the farms the user can access.
     */
    private function accessibleSalesQuery()
    {
        return EggSale::whereIn('farm_id', $this->getAccessibleFarmIds());
    }

    /**
     * Per flock: eggs produced (daily records), eggs sold, and eggs remaining.
     * Only flocks with produced or sold eggs are listed. Remaining is never negative.
     */
    private function eggStock(Request $request)
    {
        $flocks = Flock::whereIn('farm_id', $this->getAccessibleFarmIds())
            ->with('farm:id,name')
            ->when($request->query('farm_id'), fn ($q, $farmId) => $q->where('farm_id', $farmId))
            ->when($request->query('flock_id'), fn ($q, $flockId) => $q->where('id', $flockId))
            ->get(['id', 'name', 'farm_id']);

        $stock = EggSale::stockByFlock($flocks->pluck('id'));

        return $flocks
            ->map(function (Flock $flock) use ($stock) {
                $row = $stock[$flock->id];

                return [
                    'flock_id' => $flock->id,
                    'flock_name' => $flock->name,
                    'farm_id' => $flock->farm_id,
                    'farm_name' => $flock->farm->name ?? null,
                    'eggs_produced' => $row['total'],
                    'eggs_sold' => $row['sold'],
                    'remaining_eggs' => $row['remaining'],
                    'remaining_trays' => intdiv($row['remaining'], EggSale::EGGS_PER_TRAY),
                ];
            })
            ->filter(fn (array $row) => $row['eggs_produced'] > 0 || $row['eggs_sold'] > 0)
            ->values();
    }

    private function saleItem(EggSale $sale): array
    {
        return [
            'id' => $sale->id,
            'sale_date' => $sale->sale_date->format('d-m-Y'),
            'farm_id' => $sale->farm_id,
            'farm_name' => $sale->farm->name ?? null,
            'flock_id' => $sale->flock_id,
            'flock_name' => $sale->flock->name ?? null,
            'egg_wholesaler_id' => $sale->egg_wholesaler_id,
            'egg_wholesaler_name' => $sale->wholesaler->name ?? null,
            'tray' => $sale->tray,
            'total_eggs' => $sale->total_eggs,
            'box' => $this->formatDecimal($sale->box),
            'notes' => $sale->notes,
            'created_by' => $sale->created_by,
            'created_at' => $sale->created_at?->toISOString(),
        ];
    }

    private function unauthenticated()
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated. Please provide a valid authentication token.',
        ], 401);
    }
}
