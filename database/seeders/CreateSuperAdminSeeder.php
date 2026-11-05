<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\TypeMapping;
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

        // Assign SuperAdmin role if it exists
        $superAdminRole = Role::where('name', 'SuperAdmin')->first();
        if ($superAdminRole) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Create Admin
        $admin = Admin::create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin@123'),
            'type' => TypeMapping::defaultIdForParent(TypeMapping::PARENT_ADMIN), // Admin category (type_mappings.id)
            'email_verified_at' => null,
            'remember_token' => null,
            'created_by' => $superAdmin->id, // Use the actual superadmin ID
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        // Assign Admin role if it exists
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $admin->assignRole($adminRole);
        }
    }
}