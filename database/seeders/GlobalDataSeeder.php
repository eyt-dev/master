<?php

namespace Database\Seeders;

use App\Imports\MainImport;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class GlobalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Excel::import(new MainImport(),database_path('seeders/global_data.xlsx'));
    }
}
