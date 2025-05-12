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
        Schema::create('component_element', function (Blueprint $table) {
            $table->foreignId('component_id')->constrained()->onDelete('cascade');
            $table->foreignId('element_id')->constrained()->onDelete('cascade');
            $table->foreignId('element_unit_id')->constrained('units')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('component_element');
    }
};
