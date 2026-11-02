<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\Flock;
use App\Models\FlockEnd;
use Illuminate\Http\Request;

/**
 * @group Add2Farm Sales
 * Broiler slaughter sales (bird harvests) of the last 12 months
 */
class SaleController extends BaseController
{
    /**
     * List slaughter sales
     *
     * Lists bird sales of Broiler flocks, newest first. Totals are computed over all sales of the last 12 months, not only the current page.
     *
     * A sale is one slaughter on one date per flock: the flock total counts its slaughter + date rows,
     * so `total_sales` is the sum of those rows across flocks.
     *
     * `sale_flocks` lists only the Broiler flocks that have at least one sale in the last 12 months, in the same shape as the flocks list.
     *
     * @authenticated
     * @queryParam type string optional Only `birds` (Broiler flocks) is supported for now. Default: birds. Example: birds
     * @queryParam page integer optional Page number. Default: 1. Example: 1
     * @queryParam per_page integer optional Sales per page (max 100). Default: 20. Example: 20
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Sales retrieved successfully",
     *   "total_sales": 26,
     *   "total_birds_sold": 41250,
     *   "total_net_weight": 82340.5,
     *   "avg_weight": 1.996,
     *   "sale_flocks": [
     *     {
     *       "id": 12,
     *       "name": "Flock 12",
     *       "farm_id": 3,
     *       "farm_name": "Green Valley",
     *       "breed": "Ross 308",
     *       "flock-condition": "broiler-harvesting",
     *       "status": "Harvesting",
     *       "start_date": "2026-09-06",
     *       "total_birds": 10000,
     *       "hangar_allocations": [
     *         { "hangar_id": 1, "hangar_name": "Hangar 1", "quantity": 5000, "alive_birds": 4870 }
     *       ]
     *     }
     *   ],
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 41,
     *         "sale_type": "birds",
     *         "flock_id": 12,
     *         "flock_name": "Flock 12",
     *         "flock_type": "Broiler",
     *         "farm_name": "Green Valley",
     *         "hangar_id": 1,
     *         "hangar_name": "Hangar 1",
     *         "slaughter_id": 5,
     *         "slaughter_name": "Al Watania",
     *         "sale_date": "2026-10-09",
     *         "cages_count": 120,
     *         "birds_per_cage": 10,
     *         "total_birds_harvested": 1200,
     *         "cages_weight": 2.0,
     *         "gross_weight": 2580.0,
     *         "net_weight": 2340.0,
     *         "avg_weight": 1.95,
     *         "notes": null,
     *         "created_at": "2026-10-09T10:15:00.000000Z"
     *       }
     *     ],
     *     "last_page": 2,
     *     "per_page": 20,
     *     "total": 26
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "Type must be birds"
     * }
     */
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        if ($request->query('type', 'birds') !== 'birds') {
            return response()->json([
                'success' => false,
                'message' => 'Type must be birds',
            ], 422);
        }

        $accessibleFarmIds = $this->getAccessibleFarmIds();
        $perPage = min(max((int) $request->query('per_page', 20), 1), 100);

        $totals = $this->salesQuery($accessibleFarmIds)
            ->selectRaw('COUNT(DISTINCT CONCAT_WS(\'|\', flock_id, COALESCE(slaughter_id, 0), sale_date)) as total_sales,
                COALESCE(SUM(total_birds_harvested), 0) as total_birds,
                COALESCE(SUM(net_weight), 0) as total_net_weight')
            ->toBase()
            ->first();

        $totalBirds = (int) $totals->total_birds;
        $totalNetWeight = (float) $totals->total_net_weight;

        $data = $this->salesQuery($accessibleFarmIds)
            ->with('flock.farm', 'hangar', 'slaughter')
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->through(fn (FlockEnd $harvest) => $this->saleItem($harvest));

        $saleFlocks = Flock::whereIn('id', $this->salesQuery($accessibleFarmIds)->select('flock_id'))
            ->with('farm', 'chicksSupplier', 'creator', 'flockHangarAllocations.hangar', 'flockEnds')
            ->get()
            ->map(fn (Flock $flock) => app(FlockController::class)->formatFlock($flock))
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'Sales retrieved successfully',
            'total_sales' => (int) $totals->total_sales,
            'total_birds_sold' => $totalBirds,
            'total_net_weight' => round($totalNetWeight, 2),
            'avg_weight' => $totalBirds > 0 ? round($totalNetWeight / $totalBirds, 3) : 0,
            'sale_flocks' => $saleFlocks,
            'data' => $data,
        ]);
    }

    /**
     * Broiler harvests of the accessible farms in the last 12 months.
     * Built fresh on each call so the totals query cannot change the data query.
     */
    private function salesQuery($farmIds)
    {
        return FlockEnd::whereHas('flock', function ($q) use ($farmIds) {
                $q->whereIn('farm_id', $farmIds)
                  ->where(fn ($q) => $this->applyBroilerBreed($q));
            })
            ->whereDate('sale_date', '>=', today()->subMonths(12)->toDateString());
    }

    /**
     * SQL form of extractBreedType() === 'Broiler':
     * "Broiler,Cobb 500" style (text before the comma is Broiler), or a name without a comma containing cobb or ross.
     */
    private function applyBroilerBreed($query): void
    {
        $query->where(function ($q) {
            $q->whereRaw("breed LIKE '%,%' AND TRIM(SUBSTRING_INDEX(breed, ',', 1)) = 'Broiler'")
              ->orWhere(function ($q) {
                  $q->where('breed', 'not like', '%,%')
                    ->where(fn ($q) => $q->where('breed', 'like', '%cobb%')->orWhere('breed', 'like', '%ross%'));
              });
        });
    }

    /**
     * One harvest record as a sale item
     */
    private function saleItem(FlockEnd $harvest): array
    {
        $birds = (int) $harvest->total_birds_harvested;
        $netWeight = (float) $harvest->net_weight;

        return [
            'id' => $harvest->id,
            'sale_type' => 'birds',
            'flock_id' => $harvest->flock_id,
            'flock_name' => $harvest->flock->name,
            'flock_type' => 'Broiler',
            'farm_name' => $harvest->flock->farm?->name,
            'hangar_id' => $harvest->hangar_id,
            'hangar_name' => $harvest->hangar?->name,
            'slaughter_id' => $harvest->slaughter_id,
            'slaughter_name' => $harvest->slaughter?->name,
            'sale_date' => $harvest->sale_date->format('Y-m-d'),
            'cages_count' => $harvest->cages_count,
            'birds_per_cage' => $harvest->birds_per_cage,
            'total_birds_harvested' => $birds,
            'cages_weight' => (float) $harvest->cages_weight,
            'gross_weight' => (float) $harvest->total_weight,
            'net_weight' => $netWeight,
            'avg_weight' => $birds > 0 ? round($netWeight / $birds, 2) : 0,
            'notes' => $harvest->notes,
            'created_at' => $harvest->created_at,
        ];
    }
}
