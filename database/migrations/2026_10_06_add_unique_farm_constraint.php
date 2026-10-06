<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicate entries, keeping only the first one per farm
        DB::statement('
            DELETE FROM admin_farm
            WHERE id NOT IN (
                SELECT min_id FROM (
                    SELECT MIN(id) as min_id
                    FROM admin_farm
                    GROUP BY farm_id
                ) as temp
            )
        ');

        Schema::table('admin_farm', function (Blueprint $table) {
            $table->unique('farm_id');
        });
    }

    public function down(): void
    {
        Schema::table('admin_farm', function (Blueprint $table) {
            $table->dropUnique(['farm_id']);
        });
    }
};
