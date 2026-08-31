<?php

namespace App\Helpers;

/**
 * Helper class to handle decimal number conversion
 * Project uses comma (,) as decimal separator instead of period (.)
 * Examples: 1,5 = 1.5 | 1000,50 = 1000.50
 */
class DecimalHelper
{
    /**
     * Convert comma-separated decimal to period-separated (for PHP processing)
     * Input: "1,5" or "1000,50"
     * Output: 1.5 or 1000.50 (as float)
     */
    public static function toFloat($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Convert comma to period for PHP processing
        $cleaned = str_replace(',', '.', $value);
        return (float) $cleaned;
    }

    /**
     * Convert period-separated decimal to comma-separated (for API response)
     * Input: 1.5 or 1000.50 (as float/number)
     * Output: "1,5" or "1000,50" (as string)
     */
    public static function toString($value, $decimals = 2)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Round to specified decimals and replace period with comma
        $rounded = round((float) $value, $decimals);
        return str_replace('.', ',', (string) $rounded);
    }

    /**
     * Convert period-separated decimal to comma-separated (returns as float string)
     * Used when you want the value as string but formatted with comma
     * Input: 1.5
     * Output: "1,5"
     */
    public static function formatForApi($value, $decimals = 2)
    {
        return self::toString($value, $decimals);
    }

    /**
     * Parse request parameter (handles both comma and period)
     * Accepts either format and returns float
     * Input: "1,5" or "1.5"
     * Output: 1.5 (as float)
     */
    public static function parse($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Handle both comma and period
        $cleaned = str_replace(',', '.', $value);
        return (float) $cleaned;
    }

    /**
     * Format array of decimal values for API response
     * Converts all numeric values to comma format
     */
    public static function formatArray($data, $decimalFields = [])
    {
        if (!is_array($data)) {
            return $data;
        }

        foreach ($decimalFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = self::formatForApi($data[$field]);
            }
        }

        return $data;
    }
}
