<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Hash;
use Carbon\Carbon;

class CreateSuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Super Admin
        $superAdmin = Admin::create([
            'name' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('admin@123'),
            'type' => Admin::SUPER_ADMIN, // Usually 0
            'email_verified_at' => null,
            'remember_token' => null,
            'created_by' => 0,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        $superAdminRole = Role::where('name', 'SuperAdmin')->first();
        if ($superAdminRole) {
            $superAdmin->assignRole([$superAdminRole->id]);
        }

        // Create Admin
        $admin = Admin::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin@123'),
            'type' => Admin::ADMIN, // Usually 1
            'email_verified_at' => null,
            'remember_token' => null,
            'created_by' => 1, // Created by SuperAdmin
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $admin->assignRole([$adminRole->id]);
        }
    }
}