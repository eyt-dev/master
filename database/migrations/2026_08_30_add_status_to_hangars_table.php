<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hangars', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Inactive'])->default('Active')->after('broiler_hens');
        });
    }

    public function down(): void
    {
        Schema::table('hangars', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
