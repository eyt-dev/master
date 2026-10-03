<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Helper function to replace foreign key constraint
        $replaceConstraint = function ($table, $column, $references, $onDelete = 'restrict') {
            $constraintName = $table . '_' . $column . '_foreign';

            // Get the actual constraint name from database
            $result = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$table, $column]);

            if (!empty($result)) {
                $actualName = $result[0]->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$actualName}`");
                } catch (\Exception $e) {
                    // Constraint might already be dropped
                }
            }

            // Now add the new constraint
            $parts = explode('|', $references);
            $refTable = $parts[0];
            $refColumn = $parts[1];

            try {
                DB::statement("
                    ALTER TABLE `{$table}`
                    ADD CONSTRAINT `{$constraintName}`
                    FOREIGN KEY (`{$column}`)
                    REFERENCES `{$refTable}` (`{$refColumn}`)
                    ON DELETE {$onDelete}
                ");
            } catch (\Exception $e) {
                // Constraint might already exist
            }
        };

        // FLOCKS TABLE
        $replaceConstraint('flocks', 'farm_id', 'farms|id', 'RESTRICT');
        $replaceConstraint('flocks', 'chicks_supplier_id', 'chicks_suppliers|id', 'RESTRICT');
        $replaceConstraint('flocks', 'created_by', 'admins|id', 'RESTRICT');

        // FARMS TABLE
        $replaceConstraint('farms', 'assigned_to', 'admins|id', 'RESTRICT');
        $replaceConstraint('farms', 'created_by', 'admins|id', 'RESTRICT');

        // HANGARS TABLE
        $replaceConstraint('hangars', 'farm_id', 'farms|id', 'RESTRICT');
        $replaceConstraint('hangars', 'created_by', 'admins|id', 'RESTRICT');

        // DAILY_RECORDS TABLE
        $replaceConstraint('daily_records', 'farm_id', 'farms|id', 'RESTRICT');
        $replaceConstraint('daily_records', 'hangar_id', 'hangars|id', 'RESTRICT');
        $replaceConstraint('daily_records', 'flock_id', 'flocks|id', 'RESTRICT');
        $replaceConstraint('daily_records', 'created_by', 'admins|id', 'RESTRICT');

        // FLOCK_ENDS TABLE
        $replaceConstraint('flock_ends', 'flock_id', 'flocks|id', 'RESTRICT');
        $replaceConstraint('flock_ends', 'ended_by', 'admins|id', 'RESTRICT');

        // FLOCK_END_DETAILS TABLE
        $replaceConstraint('flock_end_details', 'flock_end_id', 'flock_ends|id', 'RESTRICT');
        $replaceConstraint('flock_end_details', 'hangar_id', 'hangars|id', 'RESTRICT');

        // ADMIN_FARM_PIVOT TABLE
        $replaceConstraint('admin_farm_pivot', 'admin_id', 'admins|id', 'RESTRICT');
        $replaceConstraint('admin_farm_pivot', 'farm_id', 'farms|id', 'RESTRICT');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Helper function to replace foreign key constraint back to CASCADE
        $replaceConstraint = function ($table, $column, $references) {
            $constraintName = $table . '_' . $column . '_foreign';

            // Get the actual constraint name from database
            $result = DB::select("
                SELECT CONSTRAINT_NAME
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$table, $column]);

            if (!empty($result)) {
                $actualName = $result[0]->CONSTRAINT_NAME;
                try {
                    DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$actualName}`");
                } catch (\Exception $e) {
                    // Constraint might already be dropped
                }
            }

            // Now add the constraint back with CASCADE
            $parts = explode('|', $references);
            $refTable = $parts[0];
            $refColumn = $parts[1];

            try {
                DB::statement("
                    ALTER TABLE `{$table}`
                    ADD CONSTRAINT `{$constraintName}`
                    FOREIGN KEY (`{$column}`)
                    REFERENCES `{$refTable}` (`{$refColumn}`)
                    ON DELETE CASCADE
                ");
            } catch (\Exception $e) {
                // Constraint might already exist
            }
        };

        // Revert all constraints back to CASCADE
        $replaceConstraint('flocks', 'farm_id', 'farms|id');
        $replaceConstraint('flocks', 'chicks_supplier_id', 'chicks_suppliers|id');
        $replaceConstraint('flocks', 'created_by', 'admins|id');

        $replaceConstraint('farms', 'assigned_to', 'admins|id');
        $replaceConstraint('farms', 'created_by', 'admins|id');

        $replaceConstraint('hangars', 'farm_id', 'farms|id');
        $replaceConstraint('hangars', 'created_by', 'admins|id');

        $replaceConstraint('daily_records', 'farm_id', 'farms|id');
        $replaceConstraint('daily_records', 'hangar_id', 'hangars|id');
        $replaceConstraint('daily_records', 'flock_id', 'flocks|id');
        $replaceConstraint('daily_records', 'created_by', 'admins|id');

        $replaceConstraint('flock_ends', 'flock_id', 'flocks|id');
        $replaceConstraint('flock_ends', 'ended_by', 'admins|id');

        $replaceConstraint('flock_end_details', 'flock_end_id', 'flock_ends|id');
        $replaceConstraint('flock_end_details', 'hangar_id', 'hangars|id');

        $replaceConstraint('admin_farm_pivot', 'admin_id', 'admins|id');
        $replaceConstraint('admin_farm_pivot', 'farm_id', 'farms|id');

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
