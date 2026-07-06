<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('hangars', function (Blueprint $table) {
            $table->string('name')->after('farm_id')->nullable();
        });
        
        // Auto-generate names for existing hangars
        \DB::statement("UPDATE hangars SET name = CONCAT('Hangar ', id) WHERE name IS NULL");
        
        // Make name not nullable after populating
        Schema::table('hangars', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }

    public function down()
    {
        Schema::table('hangars', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
