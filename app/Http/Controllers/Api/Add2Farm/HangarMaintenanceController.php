<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\Hangar;
use App\Models\FlockHangar;

/**
 * TEMPORARY CONTROLLER - FOR MAINTENANCE ONLY
 * This controller should be removed after the maintenance is complete.
 * It's used to synchronize hangar status with their flock allocations.
 *
 * IMPORTANT: Delete this file after running the sync-all endpoint
 */
class HangarMaintenanceController extends BaseController
{
    /**
     * Full synchronization - Activate allocated hangars and deactivate unallocated ones
     *
     * This is a temporary endpoint to completely fix hangar status.
     * It will:
     * 1. Activate all hangars that are allocated to any flock
     * 2. Deactivate all hangars that are NOT allocated to any flock
     *
     * IMPORTANT: This should be removed after running once!
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncAllHangarStatus()
    {
        try {
            // Get all unique hangar IDs that are allocated to any flock
            $allocatedHangarIds = FlockHangar::distinct()
                ->pluck('hangar_id')
                ->toArray();

            // Update allocated hangars to Active
            $activateCount = Hangar::whereIn('id', $allocatedHangarIds)
                ->where('status', '!=', 'Active')
                ->update(['status' => 'Active']);

            // Update unallocated hangars to Inactive
            $deactivateCount = Hangar::whereNotIn('id', $allocatedHangarIds)
                ->where('status', '!=', 'Inactive')
                ->update(['status' => 'Inactive']);

            // Get detailed information about updated hangars
            $allocatedHangars = Hangar::whereIn('id', $allocatedHangarIds)->get();
            $unallocatedHangars = Hangar::whereNotIn('id', $allocatedHangarIds)->get();

            $allocatedDetails = $allocatedHangars->map(function ($hangar) {
                $flockCount = FlockHangar::where('hangar_id', $hangar->id)->count();
                return [
                    'id' => $hangar->id,
                    'name' => $hangar->name,
                    'farm_id' => $hangar->farm_id,
                    'status' => $hangar->status,
                    'allocated_to_flocks' => $flockCount,
                ];
            });

            $unallocatedDetails = $unallocatedHangars->map(function ($hangar) {
                return [
                    'id' => $hangar->id,
                    'name' => $hangar->name,
                    'farm_id' => $hangar->farm_id,
                    'status' => $hangar->status,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Full hangar status synchronization completed successfully.',
                'hangars_activated' => $activateCount,
                'hangars_deactivated' => $deactivateCount,
                'total_allocated_hangars' => count($allocatedHangarIds),
                'allocated_hangars' => $allocatedDetails,
                'unallocated_hangars' => $unallocatedDetails,
                'warning' => 'REMEMBER: This is a temporary endpoint. Delete HangarMaintenanceController and remove routes after use!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error during hangar status synchronization.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
