<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaterialName;

class MaterialNameSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['name' => 'Barley', 'type' => 'Feedstuff'],
            ['name' => 'Corn', 'type' => 'Feedstuff'],
            ['name' => 'Finisher feed', 'type' => 'Pelleted feed'],
            ['name' => 'Grower feed', 'type' => 'Pelleted feed'],
            ['name' => 'Limestone', 'type' => 'Feedstuff'],
            ['name' => 'Premix', 'type' => 'Feedstuff'],
            ['name' => 'Soybean meal', 'type' => 'Feedstuff'],
            ['name' => 'Starter feed', 'type' => 'Pelleted feed'],
            ['name' => 'Wheat', 'type' => 'Feedstuff'],
        ];

        foreach ($materials as $material) {
            MaterialName::firstOrCreate(['name' => $material['name']], $material);
        }
    }
}
