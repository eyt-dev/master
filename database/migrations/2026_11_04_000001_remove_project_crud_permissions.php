<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $permissions = ['view.project', 'create.project', 'edit.project', 'delete.project'];

    public function up(): void
    {
        $permissionIds = DB::table(config('permission.table_names.permissions'))
            ->whereIn('name', $this->permissions)
            ->where('guard_name', 'admin')
            ->pluck('id');

        if ($permissionIds->isEmpty()) {
            return;
        }

        DB::table(config('permission.table_names.role_has_permissions'))
            ->whereIn('permission_id', $permissionIds)
            ->delete();

        DB::table(config('permission.table_names.model_has_permissions'))
            ->whereIn('permission_id', $permissionIds)
            ->delete();

        DB::table(config('permission.table_names.permissions'))
            ->whereIn('id', $permissionIds)
            ->delete();
    }

    public function down(): void
    {
        foreach ($this->permissions as $name) {
            DB::table(config('permission.table_names.permissions'))->updateOrInsert(
                ['name' => $name, 'guard_name' => 'admin'],
                ['name' => $name, 'guard_name' => 'admin']
            );
        }
    }
};
