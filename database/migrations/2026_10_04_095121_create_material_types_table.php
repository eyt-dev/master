<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('material_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('value')->unique();
            $table->boolean('hangar_allocation')->default(false);
            $table->timestamps();
        });

        // Insert default material types
        DB::table('material_types')->insert([
            [
                'name' => 'Pelleted Feed',
                'value' => 'pelleted_feed',
                'hangar_allocation' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mash Feed',
                'value' => 'mash_feed',
                'hangar_allocation' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Feed Ingredient',
                'value' => 'feed_ingredient',
                'hangar_allocation' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premix',
                'value' => 'premix',
                'hangar_allocation' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_types');
    }
};
