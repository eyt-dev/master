<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaterialName;

class MaterialNameSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            'Barley',
            'Corn',
            'Finisher feed',
            'Grower feed',
            'Limestone',
            'Premix',
            'Soybean meal',
            'Starter feed',
            'Wheat',
        ];

        foreach ($materials as $name) {
            MaterialName::firstOrCreate(['name' => $name]);
        }
    }
}
