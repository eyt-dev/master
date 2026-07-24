<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (!Schema::hasColumn('admins', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('url');
                $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            if (Schema::hasColumn('admins', 'project_id') && DB::connection()->getDriverName() !== 'sqlite') {
                $table->dropForeign(['project_id']);
            }
        });

        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('admins', function (Blueprint $table) {
                if (Schema::hasColumn('admins', 'project_id')) {
                    $table->dropColumn('project_id');
                }
            });
        }
    }
};
