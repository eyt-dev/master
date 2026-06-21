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
        Schema::create('formulation_components', function (Blueprint $table) {
            $table->id();

            $table->foreignId('formulation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('component_id')
                ->constrained('components')
                ->cascadeOnDelete();

            $table->enum('component_type', [
                'liquid',
                'powder'
            ]);

            $table->decimal('quantity', 12, 3);

            $table->decimal('price', 12, 2)
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulation_components');
    }
};
