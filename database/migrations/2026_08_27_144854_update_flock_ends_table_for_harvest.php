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
        Schema::table('flock_ends', function (Blueprint $table) {
            $table->dropColumn(['total_initial_qty', 'total_final_qty', 'total_mortality', 'mortality_rate']);

            $table->renameColumn('end_date', 'sale_date');

            $table->unsignedBigInteger('hangar_id')->after('slaughter_id');
            $table->integer('cages_count')->after('hangar_id');
            $table->decimal('cages_weight', 8, 2)->after('cages_count');
            $table->integer('birds_per_cage')->after('cages_weight');
            $table->integer('total_birds_harvested')->after('birds_per_cage');
            $table->integer('available_birds')->after('total_birds_harvested');
            $table->integer('remaining_birds')->after('available_birds');
            $table->decimal('total_weight', 10, 2)->after('remaining_birds');
            $table->decimal('avg_weight_per_bird', 5, 2)->nullable()->after('total_weight');

            $table->foreign('hangar_id')->references('id')->on('hangars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flock_ends', function (Blueprint $table) {
            $table->dropForeign(['hangar_id']);
            $table->dropColumn(['hangar_id', 'cages_count', 'cages_weight', 'birds_per_cage', 'total_birds_harvested', 'available_birds', 'remaining_birds', 'total_weight', 'avg_weight_per_bird']);

            $table->renameColumn('sale_date', 'end_date');

            $table->integer('total_initial_qty');
            $table->integer('total_final_qty');
            $table->integer('total_mortality');
            $table->decimal('mortality_rate', 5, 2);
        });
    }
};
