<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_records', function (Blueprint $table) {
            // Feed is entered in grams and stored in kg, so 1 g = 0.001 kg needs 3 decimals
            $table->decimal('feed_kg', 10, 3)->change();
        });
    }

    public function down(): void
    {
        Schema::table('daily_records', function (Blueprint $table) {
            $table->decimal('feed_kg', 10, 2)->change();
        });
    }
};
