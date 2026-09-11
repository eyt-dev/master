<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing assigned_to values to admin_farm pivot table
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
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all records from admin_farm pivot table (or keep them)
        // DB::table('admin_farm')->truncate();
    }
};
