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
        Schema::table('formulations', function (Blueprint $table) {
            $table->integer('total_volume')->nullable()->after('inclusion_percentage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('formulations', function (Blueprint $table) {
            $table->dropColumn('total_volume');
        });
    }
};
