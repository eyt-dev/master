<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MaterialName;
use Illuminate\Support\Facades\DB;

class MaterialNameSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Truncate the table to remove old data
        MaterialName::truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $materials = [
            // Pelleted Feed (material_type_id = 1)
            ['name' => 'Pre-Starter', 'material_type_id' => 1],
            ['name' => 'Starter (Stg1)', 'material_type_id' => 1],
            ['name' => 'Grower (Stg2)', 'material_type_id' => 1],
            ['name' => 'Grower (Stg3)', 'material_type_id' => 1],
            ['name' => 'Finisher (Stg4)', 'material_type_id' => 1],
            ['name' => 'Finisher (Stg5)', 'material_type_id' => 1],

            // Mash Feed (material_type_id = 2)
            ['name' => 'Layer Rearing', 'material_type_id' => 2],
            ['name' => 'Layer Production', 'material_type_id' => 2],

            // Feed Ingredient (material_type_id = 3)
            ['name' => 'Maize Corn', 'material_type_id' => 3],
            ['name' => 'Soybean Meal (46)', 'material_type_id' => 3],
            ['name' => 'Soybean Meal (44)', 'material_type_id' => 3],

            // Premix (material_type_id = 4)
            ['name' => 'ADD2CARE Layer 0,5%', 'material_type_id' => 4],
            ['name' => 'ADD2CARE Layer 1,5%', 'material_type_id' => 4],
            ['name' => 'ADD2CARE Broiler 1%', 'material_type_id' => 4],
            ['name' => 'ADD2CARE Broiler 2%', 'material_type_id' => 4],
        ];

        foreach ($materials as $material) {
            MaterialName::updateOrCreate(['name' => $material['name']], $material);
        }
    }
}
