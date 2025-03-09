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
        Schema::create('wheels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->timestamps();

            $table->foreign('game_id')
                  ->references('id')->on('games')
                  ->onDelete('cascade');
        });

        Schema::create('wheel_clips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wheel_id');
            $table->string('text');
            $table->timestamps();

            $table->foreign('wheel_id')
                  ->references('id')->on('wheels')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheel_clip');
    }
};
