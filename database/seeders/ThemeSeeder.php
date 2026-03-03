<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Theme;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Theme::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'theme 1',
                'created_by' => 1
            ]
        );
    }
}
