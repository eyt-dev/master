<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Creates the EggWholesaler module and its permissions, and grants them to every
 * role that already has the matching FeedSupplier permission.
 */
return new class extends Migration
{
    private string $moduleName = 'EggWholesaler';
    private array $actions = ['create', 'edit', 'delete', 'view'];

    public function up(): void
    {
        $permissionsTable = config('permission.table_names.permissions');
        $rolePermissionsTable = config('permission.table_names.role_has_permissions');

        $now = now();

        $moduleId = DB::table('modules')->where('name', $this->moduleName)->value('id');
        if (!$moduleId) {
            $moduleId = DB::table('modules')->insertGetId([
                'name' => $this->moduleName,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        foreach ($this->actions as $action) {
            $name = $action . '.egg_wholesaler';
            $feedName = $action . '.feed_supplier';

            $exists = DB::table($permissionsTable)
                ->where('name', $name)
                ->where('guard_name', 'admin')
                ->exists();

            if ($exists) {
                continue;
            }

            $permissionId = DB::table($permissionsTable)->insertGetId([
                'name' => $name,
                'guard_name' => 'admin',
                'module' => (string) $moduleId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Grant to the roles that have the matching feed supplier permission
            $feedPermissionId = DB::table($permissionsTable)
                ->where('name', $feedName)
                ->where('guard_name', 'admin')
                ->value('id');

            if (!$feedPermissionId) {
                continue;
            }

            $roleIds = DB::table($rolePermissionsTable)
                ->where('permission_id', $feedPermissionId)
                ->pluck('role_id');

            foreach ($roleIds as $roleId) {
                DB::table($rolePermissionsTable)->insertOrIgnore([
                    'permission_id' => $permissionId,
                    'role_id' => $roleId,
                ]);
            }
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $permissionsTable = config('permission.table_names.permissions');
        $rolePermissionsTable = config('permission.table_names.role_has_permissions');
        $modelPermissionsTable = config('permission.table_names.model_has_permissions');

        $permissionIds = DB::table($permissionsTable)
            ->whereIn('name', array_map(fn ($a) => $a . '.egg_wholesaler', $this->actions))
            ->where('guard_name', 'admin')
            ->pluck('id');

        DB::table($rolePermissionsTable)->whereIn('permission_id', $permissionIds)->delete();
        DB::table($modelPermissionsTable)->whereIn('permission_id', $permissionIds)->delete();
        DB::table($permissionsTable)->whereIn('id', $permissionIds)->delete();

        DB::table('modules')->where('name', $this->moduleName)->delete();

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
