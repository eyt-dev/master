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
        Schema::table('material_names', function (Blueprint $table) {
            $table->unsignedBigInteger('material_type_id')->nullable();
            $table->foreign('material_type_id')->references('id')->on('material_types')->onDelete('set null');
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_names', function (Blueprint $table) {
            $table->dropForeign(['material_type_id']);
            $table->dropColumn('material_type_id');
            $table->string('type')->nullable();
        });
    }
};
