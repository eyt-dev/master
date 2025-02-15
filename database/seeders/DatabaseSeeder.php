<?php

use Database\Seeders\CreateAdminUserSeeder;
use Database\Seeders\CreatePermissionRoleSeeder;
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
        // $this->call(UserSeeder::class);
        $this->call([
            CreatePermissionRoleSeeder::class,
            CreateAdminUserSeeder::class
        ]);
    }
}
