<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CreatePermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $date = Carbon::now()->format('Y-m-d H:i:s');
        $module = [
            ['name' => 'Profile', 'created_at' => $date, 'updated_at' => $date], //1
            ['name' => 'Dashboard', 'created_at' => $date, 'updated_at' => $date], //2
            ['name' => 'Admin', 'created_at' => $date, 'updated_at' => $date], //3
            ['name' => 'Public Vendor', 'created_at' => $date, 'updated_at' => $date], //4
            ['name' => 'Private Vendor', 'created_at' => $date, 'updated_at' => $date], //5
            ['name' => 'Role', 'created_at' => $date, 'updated_at' => $date], //6
            ['name' => 'Permission', 'created_at' => $date, 'updated_at' => $date], //7
            ['name' => 'Game', 'created_at' => $date, 'updated_at' => $date], //8
            ['name' => 'Wheel', 'created_at' => $date, 'updated_at' => $date], //9
            ['name' => 'Store View', 'created_at' => $date, 'updated_at' => $date], //10
        ];

        $permission = [
            ['name' => 'create.admin', 'guard_name' => 'web', 'module' => 3, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'edit.admin', 'guard_name' => 'web', 'module' => 3, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'delete.admin', 'guard_name' => 'web', 'module' => 3, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'view.admin', 'guard_name' => 'web', 'module' => 3, 'created_at' => $date, 'updated_at' => $date],

            ['name' => 'create.public_vendor', 'guard_name' => 'web', 'module' => 4, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'edit.public_vendor', 'guard_name' => 'web', 'module' => 4, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'delete.public_vendor', 'guard_name' => 'web', 'module' => 4, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'view.public_vendor', 'guard_name' => 'web', 'module' => 4, 'created_at' => $date, 'updated_at' => $date],

            ['name' => 'create.private_vendor', 'guard_name' => 'web', 'module' => 5, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'edit.private_vendor', 'guard_name' => 'web', 'module' => 5, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'delete.private_vendor', 'guard_name' => 'web', 'module' => 5, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'view.private_vendor', 'guard_name' => 'web', 'module' => 5, 'created_at' => $date, 'updated_at' => $date],

            ['name' => 'create.role', 'guard_name' => 'web', 'module' => 6, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'edit.role', 'guard_name' => 'web', 'module' => 6, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'delete.role', 'guard_name' => 'web', 'module' => 6, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'view.role', 'guard_name' => 'web', 'module' => 6, 'created_at' => $date, 'updated_at' => $date],

            ['name' => 'create.permission', 'guard_name' => 'web', 'module' => 7, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'edit.permission', 'guard_name' => 'web', 'module' => 7, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'delete.permission', 'guard_name' => 'web', 'module' => 7, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'view.permission', 'guard_name' => 'web', 'module' => 7, 'created_at' => $date, 'updated_at' => $date],

            ['name' => 'create.game', 'guard_name' => 'web', 'module' => 8, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'edit.game', 'guard_name' => 'web', 'module' => 8, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'delete.game', 'guard_name' => 'web', 'module' => 8, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'view.game', 'guard_name' => 'web', 'module' => 8, 'created_at' => $date, 'updated_at' => $date],

            ['name' => 'create.wheel', 'guard_name' => 'web', 'module' => 9, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'edit.wheel', 'guard_name' => 'web', 'module' => 9, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'delete.wheel', 'guard_name' => 'web', 'module' => 9, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'view.wheel', 'guard_name' => 'web', 'module' => 9, 'created_at' => $date, 'updated_at' => $date],

            ['name' => 'create.store_view', 'guard_name' => 'web', 'module' => 10, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'edit.store_view', 'guard_name' => 'web', 'module' => 10, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'delete.store_view', 'guard_name' => 'web', 'module' => 10, 'created_at' => $date, 'updated_at' => $date],
            ['name' => 'view.store_view', 'guard_name' => 'web', 'module' => 10, 'created_at' => $date, 'updated_at' => $date],
        ];

        Module::insert($module);
        Permission::insert($permission);
        $role = Role::create(
            ['guard_name' => 'web', 'name' => 'SuperAdmin'],
        );
        $role->givePermissionTo(Permission::all());

        $adminRole = Role::create(
            ['guard_name' => 'web', 'name' => 'Admin'],
        );
        $adminRole->givePermissionTo(Permission::where("module",5)->get());

        Role::insert([
            ['guard_name' => 'web', 'name' => 'PublicVendor'],
            ['guard_name' => 'web', 'name' => 'PrivateVendor']
        ]);

    }
}