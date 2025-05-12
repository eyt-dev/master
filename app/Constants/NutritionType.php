<?php


namespace App\Constants;


class NutritionType
{
    const INDIVIDUAL = 1;
    const COMPLEX = 2;
    const CARRIER_MATERIAL = 3;

    public static function getNutritionType()
    {
        return[
            self::INDIVIDUAL => 'Individual',
            self::COMPLEX => 'Complex',
            self::CARRIER_MATERIAL => 'Carrier Material',
        ];
    }

    public static function getNutritionbadge()
    {
        return[
            self::INDIVIDUAL => 'primary',
            self::COMPLEX => 'secondary',
            self::CARRIER_MATERIAL => 'warning',
        ];
    }
}
