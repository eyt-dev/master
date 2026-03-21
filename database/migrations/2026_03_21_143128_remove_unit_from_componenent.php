<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('components', function (Blueprint $table) {
            // First drop foreign key
            $table->dropForeign(['unit_id']);

            // Then drop column
            $table->dropColumn('unit_id');
        });
    }

    public function down()
    {
        Schema::table('components', function (Blueprint $table) {
            $table->foreignId('unit_id')
                ->constrained('units')
                ->onDelete('cascade');
        });
    }
};
