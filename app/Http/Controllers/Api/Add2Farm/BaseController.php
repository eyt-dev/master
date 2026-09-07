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
     * Returns format like: "Day 1", "Week 2", "Month 1 Week 2"
     */
    protected function calculateFlockAge($startDate, $endDate = null)
    {
        $start = \Carbon\Carbon::parse($startDate);
        $end = $endDate ? \Carbon\Carbon::parse($endDate) : \Carbon\Carbon::now();
        $days = $start->diffInDays($end);

        if ($days == 0) {
            return "Day 0";
        } elseif ($days == 1) {
            return "Day 1";
        } elseif ($days < 7) {
            return "Day {$days}";
        } elseif ($days < 30) {
            $weeks = (int)($days / 7);
            $remainingDays = $days % 7;
            $age = "Week {$weeks}";
            if ($remainingDays > 0) {
                $age .= " Day {$remainingDays}";
            }
            return $age;
        } else {
            $months = (int)($days / 30);
            $remainingDays = $days % 30;
            $weeks = (int)($remainingDays / 7);
            $age = "Month {$months}";
            if ($weeks > 0) {
                $age .= " Week {$weeks}";
            }
            if ($remainingDays % 7 > 0 && $weeks == 0) {
                $age .= " Day " . ($remainingDays % 7);
            }
            return $age;
        }
    }
}
