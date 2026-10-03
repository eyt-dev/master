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

        // GAMES TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'games'")) {
            $replaceConstraint('games', 'created_by', 'admins|id', 'RESTRICT');
        }

        // GAME_CLIPS TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'game_clips'")) {
            $replaceConstraint('game_clips', 'game_id', 'games|id', 'RESTRICT');
        }

        // WHEEL_CLIPS TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'wheel_clips'")) {
            $replaceConstraint('wheel_clips', 'game_id', 'games|id', 'RESTRICT');
            $replaceConstraint('wheel_clips', 'wheel_id', 'wheels|id', 'RESTRICT');
        }

        // CATEGORIES TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'categories'")) {
            $replaceConstraint('categories', 'store_view_id', 'store_views|id', 'RESTRICT');
            $replaceConstraint('categories', 'created_by', 'admins|id', 'RESTRICT');
        }

        // PAGES TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'pages'")) {
            $replaceConstraint('pages', 'created_by', 'admins|id', 'RESTRICT');
        }

        // SLIDES TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'slides'")) {
            $replaceConstraint('slides', 'created_by', 'admins|id', 'RESTRICT');
        }

        // TESTIMONIALS TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'testimonials'")) {
            $replaceConstraint('testimonials', 'created_by', 'admins|id', 'RESTRICT');
        }

        // PERMISSION TABLES
        $tableNames = config('permission.table_names');
        if ($tableNames) {
            if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$tableNames['model_has_permissions']])) {
                $replaceConstraint($tableNames['model_has_permissions'], 'permission_id', $tableNames['permissions'] . '|id', 'RESTRICT');
            }

            if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$tableNames['model_has_roles']])) {
                $replaceConstraint($tableNames['model_has_roles'], 'role_id', $tableNames['roles'] . '|id', 'RESTRICT');
            }

            if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$tableNames['role_has_permissions']])) {
                $replaceConstraint($tableNames['role_has_permissions'], 'permission_id', $tableNames['permissions'] . '|id', 'RESTRICT');
                $replaceConstraint($tableNames['role_has_permissions'], 'role_id', $tableNames['roles'] . '|id', 'RESTRICT');
            }
        }

        // MATERIAL_NAMES TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'material_names'")) {
            $replaceConstraint('material_names', 'created_by', 'admins|id', 'RESTRICT');
        }

        // COMPONENTS TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'components'")) {
            $replaceConstraint('components', 'created_by', 'admins|id', 'RESTRICT');
        }

        // FORMULATION_COMPONENTS TABLE
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'formulation_components'")) {
            $replaceConstraint('formulation_components', 'formulation_id', 'formulations|id', 'RESTRICT');
        }

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
        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'games'")) {
            $replaceConstraint('games', 'created_by', 'admins|id');
        }

        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'game_clips'")) {
            $replaceConstraint('game_clips', 'game_id', 'games|id');
        }

        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'wheel_clips'")) {
            $replaceConstraint('wheel_clips', 'game_id', 'games|id');
            $replaceConstraint('wheel_clips', 'wheel_id', 'wheels|id');
        }

        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'categories'")) {
            $replaceConstraint('categories', 'store_view_id', 'store_views|id');
            $replaceConstraint('categories', 'created_by', 'admins|id');
        }

        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'pages'")) {
            $replaceConstraint('pages', 'created_by', 'admins|id');
        }

        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'slides'")) {
            $replaceConstraint('slides', 'created_by', 'admins|id');
        }

        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'testimonials'")) {
            $replaceConstraint('testimonials', 'created_by', 'admins|id');
        }

        $tableNames = config('permission.table_names');
        if ($tableNames) {
            if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$tableNames['model_has_permissions']])) {
                $replaceConstraint($tableNames['model_has_permissions'], 'permission_id', $tableNames['permissions'] . '|id');
            }

            if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$tableNames['model_has_roles']])) {
                $replaceConstraint($tableNames['model_has_roles'], 'role_id', $tableNames['roles'] . '|id');
            }

            if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = ?", [$tableNames['role_has_permissions']])) {
                $replaceConstraint($tableNames['role_has_permissions'], 'permission_id', $tableNames['permissions'] . '|id');
                $replaceConstraint($tableNames['role_has_permissions'], 'role_id', $tableNames['roles'] . '|id');
            }
        }

        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'material_names'")) {
            $replaceConstraint('material_names', 'created_by', 'admins|id');
        }

        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'components'")) {
            $replaceConstraint('components', 'created_by', 'admins|id');
        }

        if (DB::select("SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'formulation_components'")) {
            $replaceConstraint('formulation_components', 'formulation_id', 'formulations|id');
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
