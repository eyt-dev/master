<?php

namespace Database\Seeders;

use App\Models\Form;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CreateFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $forms = [
            ['name' => 'powder', 'unit_id' => 3],
            ['name' => 'liquid', 'unit_id' => 6],
        ];

        foreach ($forms as $form) {
            Form::updateOrCreate(
                ['name' => $form['name']],
                ['unit_id' => $form['unit_id'], 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
            );
        }

    }

}
