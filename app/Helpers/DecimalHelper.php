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

        // Use number_format to ensure decimal places are preserved (e.g., 900.00 not 900)
        $formatted = number_format((float) $value, $decimals, '.', '');
        return str_replace('.', ',', $formatted);
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

    /**
     * Format number with European formatting (thousands separator: ., decimal separator: ,)
     * Examples: 1234.56 → "1.234,56", 10000 → "10.000", 1234567.89 → "1.234.567,89"
     * Input: 1234.56 (float/int)
     * Output: "1.234,56" (string)
     */
    public static function formatEuropean($value, $decimals = 2)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Format with thousands separator (.) and decimal separator (,)
        return number_format((float) $value, $decimals, ',', '.');
    }

    /**
     * Parse a number typed in European format (dot = thousands, comma = decimal)
     * A dot is a thousands separator only when it groups three digits ("10.000"),
     * so plain decimals ("1.5") and coordinates ("12.9716") keep their meaning.
     * Input: "1.000,50" → 1000.5 | "10.000" → 10000 | "1,5" → 1.5 | "12.9716" → 12.9716
     * Numbers pass through as float; unreadable input is returned unchanged so validation rejects it
     */
    public static function parseEuropean($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $cleaned = str_replace(' ', '', trim((string) $value));

        if (preg_match('/^-?(\d{1,3}(\.\d{3})+|\d+)(,\d+)?$/', $cleaned)) {
            return (float) str_replace(['.', ','], ['', '.'], $cleaned);
        }

        if (preg_match('/^-?\d+(\.\d+)?$/', $cleaned)) {
            return (float) $cleaned;
        }

        return $value;
    }

    /**
     * Parse a whole number typed in European format ("10.000" → 10000)
     * Returns an int when the value is whole; anything else is returned as-is so the 'integer' rule rejects it
     */
    public static function parseEuropeanInteger($value)
    {
        $number = self::parseEuropean($value);

        if (is_float($number) && floor($number) == $number) {
            return (int) $number;
        }

        return $number;
    }

    /**
     * Format a whole number with European thousands separator
     * Examples: 10000 → "10.000" | 1250 → "1.250" | 'N/A' → 'N/A'
     */
    public static function formatInteger($value)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return $value;
        }

        return number_format((float) $value, 0, ',', '.');
    }

    /**
     * Format a coordinate with comma decimal and no trailing zeros
     * Examples: 12.97160000 → "12,9716" | 77.5 → "77,5" | 0 → "0"
     */
    public static function formatCoordinate($value)
    {
        if ($value === null || $value === '' || !is_numeric($value)) {
            return $value;
        }

        $formatted = rtrim(rtrim(number_format((float) $value, 8, '.', ''), '0'), '.');

        return str_replace('.', ',', $formatted);
    }

    /**
     * Format number as European style for display purposes
     * Alias for formatEuropean for semantic clarity
     */
    public static function formatForDisplay($value, $decimals = 2)
    {
        return self::formatEuropean($value, $decimals);
    }

    /**
     * Format quantity for API response (comma decimal, trailing ",00" removed)
     * Examples: 1250 → "1250" | 1250.5 → "1250,50" | 1250.25 → "1250,25"
     */
    public static function formatQuantity($value, $decimals = 2)
    {
        return self::stripZeroDecimals(self::formatForApi($value, $decimals));
    }

    /**
     * Format quantity for display (European format, trailing ",00" removed)
     * Examples: 1250 → "1.250" | 1250.5 → "1.250,50" | 1250.25 → "1.250,25"
     */
    public static function formatQuantityEuropean($value, $decimals = 2)
    {
        return self::stripZeroDecimals(self::formatEuropean($value, $decimals));
    }

    /**
     * Remove a trailing decimal part made only of zeros (",00", ",0")
     */
    private static function stripZeroDecimals($formatted)
    {
        if ($formatted === null) {
            return null;
        }

        return preg_replace('/,0+$/', '', $formatted);
    }
}
