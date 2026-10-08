<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breed_category_material_type', function (Blueprint $table) {
            $table->id();
            $table->string('breed_category'); // 'Broiler' or 'Layer'
            $table->foreignId('material_type_id')->constrained('material_types')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['breed_category', 'material_type_id'], 'unique_breed_material');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breed_category_material_type');
    }
};
