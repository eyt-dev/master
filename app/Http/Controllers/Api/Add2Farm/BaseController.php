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
}
