<?php

namespace App\Imports;

use App\Models\Element;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ElementImport implements ToModel, WithHeadingRow,SkipsEmptyRows
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new Element([
            'name' => $row['name'],
            'eu_code' => $row['eu_code'],
            'synonym' => $row['synonym'] ?? null,
            'attachment' => $row['attachment'] ?? null,
        ]);
    }
}
