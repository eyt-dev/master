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
        Schema::create('formulations', function (Blueprint $table) {
            $table->id();

            $table->string('formulation_code')->unique(); // ID field
            $table->string('name');

            $table->enum('target_animal', [
                'broiler',
                'layer',
                'breeder',
                'swine',
                'cattle'
            ]);

            $table->decimal('inclusion_percentage', 8, 2);

            $table->text('indication_of_use')->nullable();
            $table->string('reference')->nullable();

            // Self reference for template
            $table->foreignId('template_id')
                ->nullable()
                ->constrained('formulations')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->constrained('admins')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulations');
    }
};
