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
        Schema::create('flock_end_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flock_end_id');
            $table->unsignedBigInteger('hangar_id');
            $table->integer('initial_qty');
            $table->integer('final_qty');
            $table->integer('mortality_qty');
            $table->decimal('end_weight', 10, 2)->nullable();
            $table->string('loss_reason')->nullable();
            $table->timestamps();

            $table->foreign('flock_end_id')->references('id')->on('flock_ends')->onDelete('cascade');
            $table->foreign('hangar_id')->references('id')->on('hangars')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flock_end_details');
    }
};
