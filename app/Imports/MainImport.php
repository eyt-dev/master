<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MainImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => new CountryImport(),
            1 => new UnitImport(),
            2 => new ElementImport(),
        ];
    }
}
