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
        Schema::table('wheels', function (Blueprint $table) {
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
        });

        Schema::table('store_views', function (Blueprint $table) {
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wheels', function (Blueprint $table) {
            //
        });
    }
};
