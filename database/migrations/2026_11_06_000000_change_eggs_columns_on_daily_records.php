<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_records', function (Blueprint $table) {
            $table->integer('eggs_weight')->nullable()->change();
            $table->integer('eggs_tray_30')->nullable()->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('daily_records', function (Blueprint $table) {
            $table->decimal('eggs_weight', 10, 2)->nullable()->change();
        });
    }
};
