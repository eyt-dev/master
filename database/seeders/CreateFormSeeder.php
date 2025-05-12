<?php

namespace Database\Seeders;

use App\Models\Form;
use Illuminate\Database\Seeder;

class CreateFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = [
            'Powder' => [3, 4],
            'Liquid' => [5, 6],
        ];

        foreach ($forms as $formName => $unitIds) {
            $form = Form::firstOrCreate(
                ['name' => $formName],
                ['created_at' => now(), 'updated_at' => now()]
            );

            $form->units()->syncWithoutDetaching($unitIds);
        }
    }

}
