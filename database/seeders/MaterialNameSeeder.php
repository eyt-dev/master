<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaterialName;

class MaterialNameSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            ['name' => 'Barley', 'type' => 'Feed Stuff'],
            ['name' => 'Corn', 'type' => 'Feed Stuff'],
            ['name' => 'Finisher feed', 'type' => 'Pelleted feed'],
            ['name' => 'Grower feed', 'type' => 'Pelleted feed'],
            ['name' => 'Limestone', 'type' => 'Feed Stuff'],
            ['name' => 'Premix', 'type' => 'Feed Stuff'],
            ['name' => 'Soybean meal', 'type' => 'Feed Stuff'],
            ['name' => 'Starter feed', 'type' => 'Pelleted feed'],
            ['name' => 'Wheat', 'type' => 'Feed Stuff'],
        ];

        foreach ($materials as $material) {
            MaterialName::updateOrCreate(['name' => $material['name']], $material);
        }
    }
}
