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
        Schema::table('hangars', function (Blueprint $table) {
            $table->integer('layer_hens')->nullable()->change();
            $table->integer('broiler_hens')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hangars', function (Blueprint $table) {
            $table->integer('layer_hens')->nullable(false)->change();
            $table->integer('broiler_hens')->nullable(false)->change();
        });
    }
};
