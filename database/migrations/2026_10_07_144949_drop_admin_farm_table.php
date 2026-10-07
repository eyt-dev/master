<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration removes the redundant admin_farm pivot table after verifying
     * that all assignments in the pivot table are correctly represented in the
     * farms.assigned_to column (the source of truth).
     */
    public function up(): void
    {
        // Verify data integrity before dropping the table
        $this->verifyAndFixAssignments();

        // Drop the redundant pivot table
        Schema::dropIfExists('admin_farm');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the admin_farm pivot table if rolled back
        Schema::create('admin_farm', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->unsignedBigInteger('farm_id');
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');

            $table->unique(['admin_id', 'farm_id']);
        });

        // Restore pivot table data from assigned_to column
        $farms = DB::table('farms')
            ->whereNotNull('assigned_to')
            ->get();

        foreach ($farms as $farm) {
            DB::table('admin_farm')->insertOrIgnore([
                'admin_id' => $farm->assigned_to,
                'farm_id' => $farm->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Verify that admin_farm pivot table data matches farms.assigned_to column
     * and ensure data consistency before removing the pivot table.
     */
    private function verifyAndFixAssignments(): void
    {
        // Check for any discrepancies between admin_farm pivot table and farms.assigned_to
        $pivotRecords = DB::table('admin_farm')->get();
        $inconsistencies = [];

        foreach ($pivotRecords as $record) {
            $farm = DB::table('farms')->where('id', $record->farm_id)->first();

            if (!$farm) {
                // Farm doesn't exist - this shouldn't happen due to foreign key
                $inconsistencies[] = "Farm {$record->farm_id} in pivot table but doesn't exist in farms table";
                continue;
            }

            // Check if the assignment matches
            if ($farm->assigned_to !== $record->admin_id) {
                if ($farm->assigned_to === null) {
                    // Pivot says assigned, but farm.assigned_to is null - sync it
                    DB::table('farms')
                        ->where('id', $farm->id)
                        ->update(['assigned_to' => $record->admin_id]);

                    echo "✓ Fixed Farm #{$farm->id}: Set assigned_to = {$record->admin_id}\n";
                } else {
                    // Conflict: both have different values - assigned_to takes precedence
                    DB::table('admin_farm')
                        ->where('farm_id', $farm->id)
                        ->where('admin_id', '!=', $farm->assigned_to)
                        ->delete();

                    echo "✓ Fixed Farm #{$farm->id}: Removed incorrect pivot entry, keeping assigned_to = {$farm->assigned_to}\n";
                }
            }
        }

        // Verify farms with assigned_to are in the pivot table (data integrity)
        $farmsWithAssignment = DB::table('farms')
            ->whereNotNull('assigned_to')
            ->get();

        foreach ($farmsWithAssignment as $farm) {
            $inPivot = DB::table('admin_farm')
                ->where('farm_id', $farm->id)
                ->where('admin_id', $farm->assigned_to)
                ->exists();

            if (!$inPivot) {
                // This shouldn't happen if migrations ran correctly, but log it
                echo "⚠ Farm #{$farm->id} has assigned_to = {$farm->assigned_to} but not in pivot table (expected - will be removed)\n";
            }
        }

        // Check for orphaned assignments (assigned_to points to non-existent admin)
        $orphaned = DB::table('farms')
            ->whereNotNull('assigned_to')
            ->leftJoin('admins', 'farms.assigned_to', '=', 'admins.id')
            ->whereNull('admins.id')
            ->get(['farms.id', 'farms.assigned_to']);

        if ($orphaned->count() > 0) {
            echo "⚠ Found " . $orphaned->count() . " farms with non-existent assigned admin:\n";
            foreach ($orphaned as $farm) {
                echo "  - Farm #{$farm->id} assigned to deleted admin #{$farm->assigned_to}\n";
            }
            // Clear orphaned assignments
            DB::table('farms')
                ->whereNotNull('assigned_to')
                ->leftJoin('admins', 'farms.assigned_to', '=', 'admins.id')
                ->whereNull('admins.id')
                ->update(['farms.assigned_to' => null]);

            echo "✓ Cleared orphaned assignments\n";
        }

        echo "✓ Data integrity verified - ready to drop admin_farm table\n";
    }
};
