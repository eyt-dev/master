<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Helpers\DecimalHelper;
use App\Http\Controllers\Controller;
use App\Models\DailyRecord;
use App\Models\Farm;
use App\Models\Flock;
use Illuminate\Support\Carbon;

/**
 * @group Add2Farm Chart Calculation
 * Detailed calculation behind each flock chart, for checking the chart values. Temporary: remove after testing.
 */
class ChartCalculationController extends Controller
{
    /**
     * Chart calculation detail for a flock
     *
     * Returns every chart (Layer: avg production, feed intake, mortality. Broiler: avg weight, trend FCR, mortality)
     * with each period's duration, type, formula, value and the calculation details behind it.
     *
     * @authenticated
     * @urlParam flock integer required The flock ID. Example: 6
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "flock_id": 6,
     *     "flock_type": "Layer",
     *     "chart_frequency": "daily",
     *     "charts": {
     *       "avg_production_chart": [
     *         {
     *           "duration": {"label": "Day 162", "start_date": "2026-10-10", "end_date": "2026-10-10"},
     *           "type": "daily",
     *           "formula": "hangar production = (total eggs of hangar / alive birds of hangar) * 100 %; flock = average of hangar productions",
     *           "value": "244,75",
     *           "calc": {"hangars": [], "hangar_average": 244.75}
     *         }
     *       ]
     *     }
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Flock not found."
     * }
     */
    public function show($flock)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please provide a valid authentication token.',
            ], 401);
        }

        $flockModel = Flock::whereIn('farm_id', $this->accessibleFarmIds())
            ->with('flockHangarAllocations')
            ->find($flock);

        if (!$flockModel) {
            return response()->json([
                'success' => false,
                'message' => 'Flock not found.',
            ], 404);
        }

        $breedType = $this->breedType($flockModel->breed);
        $isLayer = $breedType === 'Layer';
        $dailyRecords = DailyRecord::where('flock_id', $flockModel->id)->orderBy('record_date', 'asc')->get();

        $charts = $dailyRecords->isEmpty()
            ? []
            : $this->buildCharts($dailyRecords, $flockModel, $flockModel->flockHangarAllocations->sum('quantity'), $isLayer);

        return response()->json([
            'success' => true,
            'data' => [
                'flock_id' => $flockModel->id,
                'flock_type' => $isLayer ? 'Layer' : 'Broiler',
                'chart_frequency' => $charts['chart_frequency'] ?? null,
                'charts' => $charts ? array_diff_key($charts, ['chart_frequency' => 1]) : [],
            ],
        ]);
    }

    /**
     * Build all chart series with calculation detail. Same rules as the flock detail chart.
     */
    private function buildCharts($dailyRecords, Flock $flock, $totalBird, bool $isLayer): array
    {
        $start = Carbon::parse($flock->start_date)->startOfDay();
        $history = $flock->feedPerBirdHistoryByHangar(true);

        // Frequency from the number of distinct record days
        $dataDays = $dailyRecords->map(fn ($record) => $record->record_date->toDateString())->unique()->count();
        $frequency = $this->chartFrequency($dataDays);

        // Day values per hangar and date
        $recordsByHangarDate = [];
        foreach ($dailyRecords as $record) {
            $date = $record->record_date->toDateString();
            $day = $recordsByHangarDate[$record->hangar_id][$date] ?? ['mortality' => 0, 'feed' => 0.0, 'eggs' => 0, 'weight' => null];
            $day['mortality'] += (int) $record->mortality;
            $day['feed'] += (float) $record->feed_kg;
            $day['eggs'] += (int) $record->eggs_count;
            if ((float) $record->chicks_weight > 0) {
                $day['weight'] = (float) $record->chicks_weight;
            }
            $recordsByHangarDate[$record->hangar_id][$date] = $day;
        }

        // Per hangar, ascending by date
        $hangarDays = [];
        foreach ($flock->flockHangarAllocations as $allocation) {
            $hangarId = $allocation->hangar_id;
            $previousSum = 0;
            $hangarDays[$hangarId] = [];

            foreach (array_reverse($history[$hangarId] ?? []) as $entry) {
                $weight = $recordsByHangarDate[$hangarId][$entry['date']]['weight'] ?? null;
                $validFcr = $previousSum > 0 && $weight !== null;

                $hangarDays[$hangarId][] = [
                    'date' => $entry['date'],
                    'feed_yesterday_kg' => $entry['feed_yesterday_kg'],
                    'live_birds' => $entry['live_birds'],
                    'feed_per_bird' => $entry['feed_per_bird'],
                    'feed_per_bird_sum' => $entry['feed_per_bird_sum'],
                    'previous_feed_per_bird_sum' => $previousSum,
                    'weight' => $weight,
                    'act_fcr' => $validFcr ? $weight / $previousSum * 100 : null,
                    'trend_fcr' => $validFcr ? $previousSum / $weight : null,
                ];

                $previousSum = $entry['feed_per_bird_sum'];
            }
        }

        // Group record dates into periods
        $dates = $dailyRecords->map(fn ($record) => $record->record_date->toDateString())->unique()->sort()->values();
        $periods = [];
        foreach ($dates as $date) {
            $period = $this->chartPeriod($start, Carbon::parse($date), $frequency);
            $periods[$period['key']] ??= $period + ['dates' => []];
            $periods[$period['key']]['dates'][] = $date;
        }

        $charts = $isLayer
            ? ['avg_production_chart' => [], 'feed_intake_chart' => [], 'mortality_chart' => []]
            : ['avg_weight_chart' => [], 'trend_fcr_chart' => [], 'mortality_chart' => []];

        $cumulativeMortality = 0;

        foreach ($periods as $period) {
            $cutoff = $period['end'];
            $dateSet = array_flip($period['dates']);

            $duration = [
                'label' => $period['label'],
                'start_date' => $period['start'],
                'end_date' => $period['end'],
            ];

            // Mortality: daily sums inside the period, then the running total
            $periodRecords = $dailyRecords->filter(fn ($record) => isset($dateSet[$record->record_date->toDateString()]));
            $mortalityDaily = [];
            foreach ($period['dates'] as $date) {
                $mortalityDaily[] = [
                    'date' => $date,
                    'mortality' => (int) $periodRecords->filter(fn ($record) => $record->record_date->toDateString() === $date)->sum('mortality'),
                ];
            }
            $periodMortality = array_sum(array_column($mortalityDaily, 'mortality'));
            $cumulativeMortality += $periodMortality;

            $productionHangars = [];
            $feedHangars = [];
            $weightHangars = [];
            $fcrHangars = [];

            foreach ($hangarDays as $hangarId => $rows) {
                $dayRecords = $recordsByHangarDate[$hangarId] ?? [];
                $periodRows = array_values(array_filter($rows, fn ($row) => isset($dateSet[$row['date']])));

                // Layer avg production: eggs and alive birds of each day, then the hangar's production %
                $hangarDaily = [];
                foreach ($periodRows as $row) {
                    $hangarDaily[] = [
                        'date' => $row['date'],
                        'eggs' => $dayRecords[$row['date']]['eggs'] ?? 0,
                        'live_birds' => $row['live_birds'],
                    ];
                }
                $hangarEggs = array_sum(array_column($hangarDaily, 'eggs'));
                $hangarLiveBirds = array_sum(array_column($hangarDaily, 'live_birds'));
                $productionHangars[] = [
                    'hangar_id' => $hangarId,
                    'daily' => $hangarDaily,
                    'total_eggs' => $hangarEggs,
                    'total_live_birds' => $hangarLiveBirds,
                    'production' => $hangarLiveBirds > 0 ? $hangarEggs / $hangarLiveBirds * 100 : null,
                ];

                // Layer feed per bird: daily inputs, and the last 7 values up to the period end
                $last7 = array_slice(array_values(array_filter($rows, fn ($row) => $row['date'] <= $cutoff)), -7);
                $feedHangars[] = [
                    'hangar_id' => $hangarId,
                    'daily' => array_map(fn ($row) => [
                        'date' => $row['date'],
                        'previous_day_feed_kg' => $row['feed_yesterday_kg'],
                        'live_birds' => $row['live_birds'],
                        'feed_per_bird' => $row['feed_per_bird'],
                    ], $periodRows),
                    'last_7' => array_map(fn ($row) => [
                        'date' => $row['date'],
                        'feed_per_bird' => $row['feed_per_bird'],
                    ], $last7),
                    'rolling_7' => $last7 ? array_sum(array_column($last7, 'feed_per_bird')) / count($last7) : null,
                ];

                // Broiler avg weight: latest weight up to the period end
                $weightRow = $this->latestChartRow($rows, 'weight', $cutoff);
                $weightHangars[] = [
                    'hangar_id' => $hangarId,
                    'date' => $weightRow['date'] ?? null,
                    'weight' => $weightRow['weight'] ?? null,
                ];

                // Broiler trend FCR: latest valid value up to the period end
                $fcrRow = $this->latestChartRow($rows, 'trend_fcr', $cutoff);
                $fcrHangars[] = [
                    'hangar_id' => $hangarId,
                    'date' => $fcrRow['date'] ?? null,
                    'previous_feed_per_bird_sum' => $fcrRow['previous_feed_per_bird_sum'] ?? null,
                    'weight' => $fcrRow['weight'] ?? null,
                    'trend_fcr' => $fcrRow['trend_fcr'] ?? null,
                ];
            }

            // Flock value = average over hangars
            $mean = function (array $values) {
                $values = array_filter($values, fn ($v) => $v !== null);

                return $values ? array_sum($values) / count($values) : null;
            };

            $mortalityCalc = [
                'daily' => $mortalityDaily,
                'period_mortality' => $periodMortality,
                'cumulative_mortality' => $cumulativeMortality,
            ];
            $mortalityItem = [
                'duration' => $duration,
                'type' => $frequency,
                'formula' => 'cumulative mortality = sum of period mortality from the first period up to this period end',
                'value' => $cumulativeMortality,
                'calc' => $mortalityCalc,
            ];

            if ($isLayer) {
                $avgProduction = $mean(array_column($productionHangars, 'production'));
                $feedPerBird = $mean(array_column($feedHangars, 'rolling_7'));

                $charts['avg_production_chart'][] = [
                    'duration' => $duration,
                    'type' => $frequency,
                    'formula' => 'hangar production = (total eggs of hangar / alive birds of hangar) * 100 %; flock = average of hangar productions',
                    'value' => $avgProduction !== null ? DecimalHelper::formatForApi($avgProduction, 2) : null,
                    'calc' => ['hangars' => $productionHangars, 'hangar_average' => $avgProduction],
                ];
                $charts['feed_intake_chart'][] = [
                    'duration' => $duration,
                    'type' => $frequency,
                    'formula' => 'feed per bird of day = feed of previous day * 1000 / alive birds of day; hangar value = average of its last 7 daily values up to period end; flock = average of hangar values',
                    'value' => $feedPerBird !== null ? DecimalHelper::formatForApi($feedPerBird, 2) : null,
                    'calc' => ['hangars' => $feedHangars, 'hangar_average' => $feedPerBird],
                ];
                $charts['mortality_chart'][] = $mortalityItem;
            } else {
                $avgWeight = $mean(array_column($weightHangars, 'weight'));
                $trendFcr = $mean(array_column($fcrHangars, 'trend_fcr'));

                $charts['avg_weight_chart'][] = [
                    'duration' => $duration,
                    'type' => $frequency,
                    'formula' => 'avg weight = average over hangars of each hangar\'s latest chick weight up to period end',
                    'value' => $avgWeight !== null ? DecimalHelper::formatEuropean(round($avgWeight), 0) : null,
                    'calc' => ['hangars' => $weightHangars, 'hangar_average' => $avgWeight],
                ];
                $charts['trend_fcr_chart'][] = [
                    'duration' => $duration,
                    'type' => $frequency,
                    'formula' => 'trend FCR (hangar) = feed per bird sum of previous day / chick weight of day; flock = average over hangars of latest value up to period end',
                    'value' => $trendFcr !== null ? DecimalHelper::formatForApi($trendFcr, 2) : null,
                    'calc' => ['hangars' => $fcrHangars, 'hangar_average' => $trendFcr],
                ];
                $charts['mortality_chart'][] = $mortalityItem;
            }
        }

        $charts['chart_frequency'] = $frequency;

        return $charts;
    }

    /**
     * Chart frequency by number of days with daily records: daily under 15, weekly to 90, every 2 weeks to 180, monthly after.
     */
    private function chartFrequency(int $days): string
    {
        if ($days < 15) {
            return 'daily';
        }

        if ($days <= 90) {
            return 'weekly';
        }

        if ($days <= 180) {
            return 'biweekly';
        }

        return 'monthly';
    }

    /**
     * Period containing a date. Daily, weekly and biweekly periods are counted from the flock start; monthly are calendar months.
     */
    private function chartPeriod(Carbon $start, Carbon $date, string $frequency): array
    {
        if ($frequency === 'monthly') {
            $monthStart = $date->copy()->startOfMonth();

            return [
                'key' => $monthStart->format('Y-m'),
                'label' => $monthStart->format('M Y'),
                'start' => $monthStart->toDateString(),
                'end' => $date->copy()->endOfMonth()->toDateString(),
            ];
        }

        $age = max(0, (int) $start->diffInDays($date, false));
        $step = ['daily' => 1, 'weekly' => 7, 'biweekly' => 14][$frequency];
        $index = intdiv($age, $step);
        $periodStart = $start->copy()->addDays($index * $step);

        $label = match ($frequency) {
            'daily' => 'Day ' . $age,
            'weekly' => 'Week ' . ($index + 1),
            default => 'Week ' . ($index * 2 + 1) . '-' . ($index * 2 + 2),
        };

        return [
            'key' => $index,
            'label' => $label,
            'start' => $periodStart->toDateString(),
            'end' => $periodStart->copy()->addDays($step - 1)->toDateString(),
        ];
    }

    /**
     * Latest row (ascending daily rows) on or before the cutoff whose field is not null. Null if none.
     */
    private function latestChartRow(array $rows, string $field, string $cutoff): ?array
    {
        $found = null;
        foreach ($rows as $row) {
            if ($row['date'] > $cutoff) {
                break;
            }
            if ($row[$field] !== null) {
                $found = $row;
            }
        }

        return $found;
    }

    /**
     * Breed type from the breed text, same rules as the flock API.
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
     * Farms the user can access: all for super admin, otherwise farms they created or are assigned to.
     */
    private function accessibleFarmIds()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return Farm::pluck('id');
        }

        return Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->pluck('id');
    }
}
