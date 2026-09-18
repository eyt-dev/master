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
        DB::statement("ALTER TABLE material_names MODIFY type VARCHAR(255) NULL");
        DB::statement("UPDATE material_names SET type = 'Feed Stuff' WHERE type = 'Feedstuff'");
        DB::statement("ALTER TABLE material_names MODIFY type ENUM('Pelleted feed', 'Feed Stuff') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE material_names MODIFY type VARCHAR(255) NULL");
        DB::statement("UPDATE material_names SET type = 'Feedstuff' WHERE type = 'Feed Stuff'");
        DB::statement("ALTER TABLE material_names MODIFY type ENUM('Pelleted feed', 'Feedstuff') NULL");
    }
};
