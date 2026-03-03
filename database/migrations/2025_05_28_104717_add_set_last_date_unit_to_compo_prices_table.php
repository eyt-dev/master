<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('compo_prices', function (Blueprint $table) {
            if (Schema::hasColumn('compo_prices', 'description')) {
                $table->dropColumn('description');
            }
            $table->integer('unit')->after('price');
            $table->boolean('set_last_date_as_default')->default(false)->after('unit');
            $table->boolean('set_last_unit_as_default')->default(false)->after('set_last_date_as_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compo_prices', function (Blueprint $table) {
            $table->dropColumn('set_last_unit_as_default');
            $table->dropColumn('set_last_date_as_default');
            $table->dropColumn('unit');
            $table->text('description')->nullable();
        });
    }
};
