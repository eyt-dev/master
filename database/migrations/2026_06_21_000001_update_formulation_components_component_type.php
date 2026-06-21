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
        Schema::table('formulation_components', function (Blueprint $table) {
            // Add the new foreign key column
            $table->foreignId('component_type_id')
                ->nullable()
                ->after('component_id')
                ->constrained('forms')
                ->nullOnDelete();
        });

        // Copy data from component_type enum to component_type_id
        // This assumes: 1 = Powder, 2 = Liquid (based on forms table)
        \DB::statement("UPDATE formulation_components 
                       SET component_type_id = CASE 
                           WHEN component_type = 'powder' THEN 1 
                           WHEN component_type = 'liquid' THEN 2 
                           ELSE NULL 
                       END");

        // Drop the old component_type column
        Schema::table('formulation_components', function (Blueprint $table) {
            $table->dropColumn('component_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formulation_components', function (Blueprint $table) {
            $table->enum('component_type', ['liquid', 'powder'])->after('component_id');
        });

        // Copy data back from component_type_id to component_type
        \DB::statement("UPDATE formulation_components 
                       SET component_type = CASE 
                           WHEN component_type_id = 1 THEN 'powder' 
                           WHEN component_type_id = 2 THEN 'liquid' 
                           ELSE NULL 
                       END");

        Schema::table('formulation_components', function (Blueprint $table) {
            $table->dropForeign(['component_type_id']);
            $table->dropColumn('component_type_id');
        });
    }
};
