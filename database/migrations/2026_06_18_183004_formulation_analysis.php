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
        Schema::create('formulation_analysis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('formulation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('element_name');

            $table->decimal('premix_value', 12, 4)
                ->nullable();

            $table->decimal('feed_value', 12, 4)
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulation_analysis');
    }
};
