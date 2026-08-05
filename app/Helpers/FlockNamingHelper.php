<?php

namespace App\Helpers;

use App\Models\Flock;
use App\Models\Farm;

class FlockNamingHelper
{
    /**
     * Generate automatic flock name based on farm and sequential number
     * Format: FarmName-Flock1, FarmName-Flock2, etc.
     * Excludes the current flock if updating
     * 
     * @param int $farmId
     * @param int|null $excludeFlockId - ID of current flock if updating (to exclude from count)
     * @return string
     */
    public static function generateFlockName($farmId, $excludeFlockId = null)
    {
        $farm = Farm::find($farmId);
        
        if (!$farm) {
            return 'Flock-Unknown';
        }

        // Get the count of existing flocks for this farm, excluding current flock if updating
        $query = Flock::where('farm_id', $farmId);
        if ($excludeFlockId) {
            $query->where('id', '!=', $excludeFlockId);
        }
        $flockCount = $query->count();
        $sequenceNumber = $flockCount + 1;

        // Format: Remove spaces and special characters from farm name, keep camelcase style
        $farmName = self::sanitizeFarmName($farm->name);

        // Generate name: FarmName-Flock1, FarmName-Flock2, etc.
        return "{$farmName}-Flock{$sequenceNumber}";
    }

    /**
     * Sanitize farm name for use in flock naming
     * Removes spaces and special characters, keeps alphanumeric
     * 
     * @param string $farmName
     * @return string
     */
    private static function sanitizeFarmName($farmName)
    {
        // Remove spaces
        $name = str_replace(' ', '', $farmName);
        
        // Remove special characters except alphanumeric
        $name = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        
        return $name ?: 'Farm';
    }

    /**
     * Get next sequence number for a farm
     * 
     * @param int $farmId
     * @return int
     */
    public static function getNextSequenceNumber($farmId)
    {
        return Flock::where('farm_id', $farmId)->count() + 1;
    }
}
