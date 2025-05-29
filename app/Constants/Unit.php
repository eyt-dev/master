<?php


namespace App\Constants;


class Unit
{
    const KG = 1;
    const TON = 2;
    const LITRE = 3;
    const LITRE_1000 = 4;

    public static function getUnit()
    {
        return [
            self::KG => 'Kg',
            self::TON => 'Ton',
            self::LITRE => 'Litre',
            self:: LITRE_1000 => '1000 Litre',
        ];
    }
}
