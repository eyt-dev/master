<?php

namespace App\Helpers;

use App\Models\DailyRecord;
use App\Models\MaterialStockHangar;

/**
 * Remaining feed calculation.
 * Daily record feed is taken from stock oldest first (FIFO), and only from stock whose
 * stock_date is on or before the record_date. So a newly added stock keeps its full
 * quantity until daily records are entered against it.
 */
class FeedStockHelper
{
    /**
     * Remaining kg per hangar allocation, keyed by material_stock_hangars.id
     */
    public static function remainingByAllocation(int $hangarId): array
    {
        $allocations = MaterialStockHangar::where('hangar_id', $hangarId)
            ->byMaterialType()
            ->with('materialStock:id,stock_date')
            ->get()
            ->sort(function ($a, $b) {
                return [$a->materialStock->stock_date->format('Y-m-d'), $a->id]
                    <=> [$b->materialStock->stock_date->format('Y-m-d'), $b->id];
            });

        $remaining = [];
        $stockDates = [];
        foreach ($allocations as $allocation) {
            $remaining[$allocation->id] = (float) $allocation->quantity;
            $stockDates[$allocation->id] = $allocation->materialStock->stock_date->format('Y-m-d');
        }

        $records = DailyRecord::where('hangar_id', $hangarId)
            ->orderBy('record_date')
            ->orderBy('id')
            ->get(['record_date', 'feed_kg']);

        foreach ($records as $record) {
            $need = (float) $record->feed_kg;
            $recordDate = $record->record_date->format('Y-m-d');

            foreach ($allocations as $allocation) {
                if ($need <= 0) {
                    break;
                }

                $id = $allocation->id;
                if ($stockDates[$id] > $recordDate || $remaining[$id] <= 0) {
                    continue;
                }

                $taken = min($need, $remaining[$id]);
                $remaining[$id] -= $taken;
                $need -= $taken;
            }
        }

        return array_map(fn ($value) => round(max(0, $value), 2), $remaining);
    }

    /**
     * Total remaining kg across all stock allocated to a hangar
     */
    public static function hangarRemaining(int $hangarId): float
    {
        return round(array_sum(self::remainingByAllocation($hangarId)), 2);
    }
}
