<?php

use Database\Seeders\CreateSuperAdminSeeder;
use Database\Seeders\CreatePermissionRoleSeeder;
use Database\Seeders\CountriesRegionSeeder;
use Database\Seeders\ThemeSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(adminseeder::class);
        $this->call([
            CreatePermissionRoleSeeder::class,
            CreateSuperAdminSeeder::class,
            CountriesRegionSeeder::class,
            ThemeSeeder::class
        ]);
    }
}
