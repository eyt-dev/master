<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('material_stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('material_stocks', 'material_name_id')) {
                $table->unsignedBigInteger('material_name_id')->nullable()->after('name');
            }

            // Check if foreign key already exists before adding
            if (!$this->hasForeignKey('material_stocks', 'material_stocks_material_name_id_foreign')) {
                $table->foreign('material_name_id')->references('id')->on('material_names')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_stocks', function (Blueprint $table) {
            if ($this->hasForeignKey('material_stocks', 'material_stocks_material_name_id_foreign')) {
                $table->dropForeign(['material_name_id']);
            }
            if (Schema::hasColumn('material_stocks', 'material_name_id')) {
                $table->dropColumn('material_name_id');
            }
        });
    }

    private function hasForeignKey($table, $foreignKey)
    {
        $database = config('database.connections.' . config('database.default') . '.database');
        $constraints = \DB::select("
            SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?
        ", [$database, $table, $foreignKey]);
        return count($constraints) > 0;
    }
};
