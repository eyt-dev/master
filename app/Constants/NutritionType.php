<?php


namespace App\Constants;


class NutritionType
{
    const INDIVIDUAL = 1;
    const COMPLEX = 2;

    public static function getNutritionType()
    {
        return[
            self::INDIVIDUAL => 'individual',
            self::COMPLEX => 'complex'
        ];
    }

    public static function getNutritionbadge()
    {
        return[
            self::INDIVIDUAL => 'primary',
            self::COMPLEX => 'warning'
        ];
    }
}
