<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('url');
                $table->foreign('created_by')->references('id')->on('admins')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'created_by') && DB::connection()->getDriverName() !== 'sqlite') {
                $table->dropForeign(['created_by']);
            }
        });

        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('projects', function (Blueprint $table) {
                if (Schema::hasColumn('projects', 'created_by')) {
                    $table->dropColumn('created_by');
                }
            });
        }
    }
};
