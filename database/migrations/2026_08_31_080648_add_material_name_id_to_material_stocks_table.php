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
            $table->unsignedBigInteger('material_name_id')->nullable()->after('name');
            $table->foreign('material_name_id')->references('id')->on('material_names')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_stocks', function (Blueprint $table) {
            $table->dropForeign(['material_name_id']);
            $table->dropColumn('material_name_id');
        });
    }
};
