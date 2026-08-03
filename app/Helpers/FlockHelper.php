<?php

namespace App\Helpers;

use App\Models\Flock;

class FlockHelper
{
    /**
     * Generate flock label in format: farm-breed-date (lowercase, no spaces)
     * Example: mytestfarm-ross308-20260803
     * 
     * @param Flock $flock
     * @return string
     */
    public static function getFlockLabel(Flock $flock): string
    {
        $farmName = strtolower(str_replace(' ', '', $flock->farm->name));
        $breed = strtolower(str_replace(' ', '', $flock->breed));
        $startDate = $flock->start_date->format('Ymd');
        
        return "{$farmName}-{$breed}-{$startDate}";
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
