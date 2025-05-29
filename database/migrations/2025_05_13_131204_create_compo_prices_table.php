<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('compo_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_id')->constrained();
            $table->foreignId('element_id')->nullable()->constrained();

            $table->date('pricing_date')->nullable();
            $table->decimal('price');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['component_id', 'element_id', 'pricing_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compo_prices');
    }
};
