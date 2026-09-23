<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\FlockEnd;
use Illuminate\Support\Facades\DB;

/**
 * TEMPORARY CONTROLLER - FOR MAINTENANCE ONLY
 * This controller should be removed after the maintenance is complete.
 * It's used to synchronize net_weight for flock_end records.
 *
 * IMPORTANT: Delete this file after running the sync-all endpoint
 */
class FlockEndMaintenanceController extends BaseController
{
    /**
     * Sync net_weight for all flock_end records
     *
     * Calculate and update net_weight for all flock_end records that have NULL or 0 value.
     * Net weight is calculated as: gross_weight (if no calculation needed)
     * Or can be derived from: total_weight - (cages_weight * cages_count)
     *
     * IMPORTANT: This should be removed after running once!
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncNetWeights()
    {
        try {
            DB::beginTransaction();

            // Find all flock_end records with NULL or 0 net_weight
            $recordsToSync = FlockEnd::where(function ($query) {
                $query->whereNull('net_weight')
                      ->orWhere('net_weight', 0);
            })->get();

            $syncedCount = 0;
            $skippedCount = 0;
            $syncedDetails = [];
            $skippedDetails = [];

            foreach ($recordsToSync as $record) {
                // Calculate net_weight: gross_weight - (cages_weight * cages_count)
                $grossWeight = (float) $record->total_weight;
                $cagesWeight = (float) $record->cages_weight;
                $cagesCount = (int) $record->cages_count;

                if ($grossWeight > 0 && $cagesWeight > 0 && $cagesCount > 0) {
                    $calculatedNetWeight = $grossWeight - ($cagesWeight * $cagesCount);

                    // Update the record
                    $record->update(['net_weight' => $calculatedNetWeight]);
                    $syncedCount++;

                    $syncedDetails[] = [
                        'id' => $record->id,
                        'flock_id' => $record->flock_id,
                        'flock_name' => $record->flock?->name,
                        'gross_weight' => $this->formatDecimal($grossWeight),
                        'calculated_net_weight' => $this->formatDecimal($calculatedNetWeight),
                        'cages_weight' => $this->formatDecimal($cagesWeight),
                        'cages_count' => $cagesCount,
                    ];
                } else {
                    $skippedCount++;
                    $skippedDetails[] = [
                        'id' => $record->id,
                        'flock_id' => $record->flock_id,
                        'flock_name' => $record->flock?->name,
                        'reason' => 'Invalid gross_weight, cages_weight, or cages_count',
                        'gross_weight' => $this->formatDecimal($grossWeight),
                        'cages_weight' => $this->formatDecimal($cagesWeight),
                        'cages_count' => $cagesCount,
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Net weight synchronization completed successfully.',
                'total_records_processed' => count($recordsToSync),
                'records_synced' => $syncedCount,
                'records_skipped' => $skippedCount,
                'synced_details' => $syncedDetails,
                'skipped_details' => $skippedDetails,
                'warning' => 'REMEMBER: This is a temporary endpoint. Delete FlockEndMaintenanceController and remove routes after use!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Flock end net weight sync error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error during net weight synchronization.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
