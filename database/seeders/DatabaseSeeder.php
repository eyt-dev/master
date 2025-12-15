<?php

namespace Database\Seeders;

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
            SettingSeeder::class,
            ContactSeeder::class
        ]);
    }
}
