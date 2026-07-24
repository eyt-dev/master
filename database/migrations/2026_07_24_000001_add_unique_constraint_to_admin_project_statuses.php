<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_project_statuses', function (Blueprint $table) {
            if (DB::connection()->getDriverName() !== 'sqlite') {
                $table->unique(['admin_id', 'project_id']);
            }
        });

        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('CREATE UNIQUE INDEX unique_admin_project ON admin_project_statuses(admin_id, project_id)');
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS unique_admin_project');
        } else {
            Schema::table('admin_project_statuses', function (Blueprint $table) {
                $table->dropUnique(['admin_id', 'project_id']);
            });
        }
    }
};
