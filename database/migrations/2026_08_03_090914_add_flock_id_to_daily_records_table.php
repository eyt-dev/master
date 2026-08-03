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
        Schema::table('daily_records', function (Blueprint $table) {
            $table->bigInteger('flock_id')->unsigned()->nullable()->after('hangar_id');
            $table->foreign('flock_id')->references('id')->on('flocks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_records', function (Blueprint $table) {
            $table->dropForeign(['flock_id']);
            $table->dropColumn('flock_id');
        });
    }
};
