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
            if (Schema::hasColumn('projects', 'created_by') && DB::connection()->getDriverName() !== 'sqlite') {
                $table->dropForeign(['created_by']);
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            $columns = array_filter(
                ['created_by', 'description', 'url'],
                fn ($column) => Schema::hasColumn('projects', $column)
            );

            if ($columns) {
                $table->dropColumn($columns);
            }

            if (Schema::hasColumn('projects', 'created_at') && Schema::hasColumn('projects', 'updated_at')) {
                $table->dropTimestamps();
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'description')) {
                $table->text('description')->nullable();
            }

            if (!Schema::hasColumn('projects', 'url')) {
                $table->string('url')->nullable();
            }

            if (!Schema::hasColumn('projects', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable();
                $table->foreign('created_by')->references('id')->on('admins')->nullOnDelete();
            }

            if (!Schema::hasColumn('projects', 'created_at')) {
                $table->timestamps();
            }
        });
    }
};
