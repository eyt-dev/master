<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_records', function (Blueprint $table) {
            // Existing decimal values are rounded by the database (e.g. 10.12 becomes 10)
            $table->integer('feed_kg')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('daily_records', function (Blueprint $table) {
            $table->decimal('feed_kg', 10, 2)->change();
        });
    }
};
