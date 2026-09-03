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
        Schema::table('flock_end_details', function (Blueprint $table) {
            $table->decimal('gross_weight', 10, 2)->nullable()->after('batch_number');
            $table->json('batch_weights')->nullable()->after('gross_weight');
        });

        DB::statement('UPDATE flock_end_details SET gross_weight = batch_weight WHERE gross_weight IS NULL');

        Schema::table('flock_end_details', function (Blueprint $table) {
            $table->dropColumn('batch_weight');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flock_end_details', function (Blueprint $table) {
            $table->decimal('batch_weight', 10, 2)->nullable()->after('batch_number');
        });

        DB::statement('UPDATE flock_end_details SET batch_weight = gross_weight WHERE batch_weight IS NULL');

        Schema::table('flock_end_details', function (Blueprint $table) {
            $table->dropColumn('batch_weights');
            $table->dropColumn('gross_weight');
        });
    }
};
