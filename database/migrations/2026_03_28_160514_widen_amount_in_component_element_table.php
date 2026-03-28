<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('component_element', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change(); // supports up to 9,999,999,999,999.99
        });
    }

    public function down(): void
    {
        Schema::table('component_element', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
    }
};
