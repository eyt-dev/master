<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Populate assigned_to field from admin_farm pivot table
        // For each farm, set assigned_to to the admin_id from the pivot table
        DB::statement('
            UPDATE farms
            SET assigned_to = (
                SELECT admin_id
                FROM admin_farm
                WHERE admin_farm.farm_id = farms.id
                LIMIT 1
            )
            WHERE assigned_to IS NULL
            AND id IN (
                SELECT DISTINCT farm_id FROM admin_farm
            )
        ');
    }

    public function down(): void
    {
        // Revert by setting assigned_to back to null where it was previously null
        // Note: This is a data-only migration, down() is provided for completeness
    }
};
