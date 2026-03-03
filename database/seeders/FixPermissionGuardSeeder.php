<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class FixPermissionGuardSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // Update permissions guard
            Permission::where('guard_name', 'web')
                ->update(['guard_name' => 'admin']);

            // Update roles guard
            Role::where('guard_name', 'web')
                ->update(['guard_name' => 'admin']);
        });

        // Clear permission cache (VERY IMPORTANT)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('✅ Guard name updated from web → admin for roles & permissions');
    }
}
