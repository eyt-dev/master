<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Services\Add2Farm\TranslationService;
use App\Helpers\DecimalHelper;

class BaseController extends Controller
{
    protected TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }

    /**
     * Get type label based on type value
     */
    protected function getTypeLabel(int $type): string
    {
        return $this->translationService->getTypeLabel($type);
    }

    /**
     * Get status label based on status value
     */
    protected function getStatusLabel(string $status): string
    {
        return $this->translationService->getStatusLabel($status);
    }

    /**
     * Convert period decimal to comma (for API response)
     * Example: 1.5 → "1,5" | 1000.50 → "1000,50"
     */
    protected function formatDecimal($value, $decimals = 2)
    {
        return DecimalHelper::formatForApi($value, $decimals);
    }

    /**
     * Parse decimal from request (handles comma or period)
     * Example: "1,5" → 1.5 | "1.5" → 1.5
     */
    protected function parseDecimal($value)
    {
        return DecimalHelper::parse($value);
    }

    /**
     * Format array of decimal fields for API response
     * Converts specified decimal fields from period to comma format
     */
    protected function formatDecimals($data, $fields = [])
    {
        return DecimalHelper::formatArray($data, $fields);
    }

    /**
     * Extract breed type from breed string
     * Supports formats:
     * - "Layer, Lohmann brown" → "Layer"
     * - "Broiler, Cobb 500" → "Broiler"
     * - "Cobb" → "Broiler"
     * - "Lohmann" → "Layer"
     */
    protected function extractBreedType($breedString)
    {
        $breedType = 'Layer';

        if (!empty($breedString)) {
            // If breed contains comma, take first part (e.g., "Layer, Lohmann brown" → "Layer")
            if (strpos($breedString, ',') !== false) {
                $breedParts = explode(',', $breedString);
                $breedType = trim($breedParts[0]);
            } else {
                // Check for specific breed names
                if (stripos($breedString, 'cobb') !== false || stripos($breedString, 'ross') !== false) {
                    $breedType = 'Broiler';
                } elseif (stripos($breedString, 'lohmann') !== false || stripos($breedString, 'hy-line') !== false) {
                    $breedType = 'Layer';
                }
            }
        }

        return $breedType;
    }

    /**
     * Calculate flock age based on start and optional end date
     * If no end_date: calculates from start_date to today
     * If end_date provided: calculates from start_date to end_date (flock duration)
     *
     * Age format depends on breed type:
     * - Broiler: Always in days (Day1, Day2, ..., DayN)
     * - Layer: Days for first 28 days (Day1-Day28), then weeks (Week4, Week5, ..., WeekN)
     */
    protected function calculateFlockAge($startDate, $endDate = null, $breedType = null)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = $endDate ? \Carbon\Carbon::parse($endDate) : \Carbon\Carbon::now();
        $days = $start->diffInDays($end);

        // Default to Layer if breed type not provided
        if ($breedType === null) {
            $breedType = 'Layer';
        }

        // Broiler: always display in days
        if ($breedType === 'Broiler') {
            return "Day {$days}";
        }

        // Layer: days for first 28 days, then weeks
        if ($days <= 28) {
            return "Day {$days}";
        } else {
            $weeks = (int)($days / 7);
            return "Week {$weeks}";
        }
    }

    /**
     * Calculate remaining feed for a hangar
     * Remaining = Total Feed Stock - Total Daily Record Consumption
     * Simple calculation at hangar level (not per feed entry)
     */
    protected function getHangarRemainingFeed($hangarId)
    {
        $totalStock = \App\Models\MaterialStockHangar::where('hangar_id', $hangarId)
            ->byMaterialType('pelleted feed')
            ->sum('quantity') ?? 0;

        $totalConsumed = \App\Models\DailyRecord::where('hangar_id', $hangarId)
            ->sum('feed_kg') ?? 0;

        return max(0, $totalStock - $totalConsumed);
    }

    /**
     * Check if current user is SUPER_ADMIN (type = 0)
     */
    protected function isSuperAdmin(): bool
    {
        return (int)auth()->user()->type === \App\Models\Admin::SUPER_ADMIN;
    }

    /**
     * Get farms accessible by user
     * If user is SUPER_ADMIN (type=0), returns all farms
     * Otherwise returns only farms user has direct access to (created_by or assigned_to)
     */
    protected function getAccessibleFarmIds()
    {
        $user = auth()->user();

        // SUPER_ADMIN (type = 0) has access to all farms (handles both int and string type values)
        if ($this->isSuperAdmin()) {
            return \App\Models\Farm::pluck('id');
        }

        // Regular users have access only to their assigned farms
        return \App\Models\Farm::where(function ($q) use ($user) {
            $q->where('created_by', $user->id)
              ->orWhere('assigned_to', $user->id);
        })->pluck('id');
    }
}
