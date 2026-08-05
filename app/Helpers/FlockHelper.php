<?php

namespace App\Helpers;

use App\Models\Flock;

class FlockHelper
{
    /**
     * Get flock label - uses the flock name field directly
     * Example: Farm1-Flock1
     * 
     * @param Flock|null $flock
     * @return string
     */
    public static function getFlockLabel(?Flock $flock): string
    {
        if (!$flock) {
            return 'N/A';
        }
        
        // Use the flock name field directly if available
        if ($flock->name) {
            return $flock->name;
        }
        
        // Fallback to N/A if no name exists
        return 'N/A';
    }

    /**
     * Get all flocks formatted with their labels
     * Used for dropdowns
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function getAllFlockOptions()
    {
        $flocks = Flock::with('farm')->get();
        
        return $flocks->map(function($flock) {
            return [
                'id' => $flock->id,
                'label' => self::getFlockLabel($flock),
                'farm_id' => $flock->farm_id
            ];
        })->sortBy('label');
    }

    /**
     * Get flocks by farm
     * 
     * @param int $farmId
     * @return \Illuminate\Support\Collection
     */
    public static function getFlocksByFarm(int $farmId)
    {
        $flocks = Flock::where('farm_id', $farmId)->with('farm')->get();
        
        return $flocks->map(function($flock) {
            return [
                'id' => $flock->id,
                'label' => self::getFlockLabel($flock),
                'farm_id' => $flock->farm_id
            ];
        })->sortBy('label');
    }

    /**
     * Get flock label by flock ID
     * 
     * @param int $flockId
     * @return string|null
     */
    public static function getFlockLabelById(int $flockId): ?string
    {
        $flock = Flock::with('farm')->find($flockId);
        
        if (!$flock) {
            return null;
        }
        
        return self::getFlockLabel($flock);
    }
}

