<?php

namespace App\Imports;

use App\Models\Country;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CountryImport implements ToModel, WithHeadingRow, SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Country([
            'image' => $row['image'] ?? null,
            'name' => $row['name'],
            'alpha_2_code' => $row['alpha_2_code'],
            'alpha_3_code' => $row['alpha_3_code'],
            'dial_code' => $row['diall_code'],
        ]);
    }
}
