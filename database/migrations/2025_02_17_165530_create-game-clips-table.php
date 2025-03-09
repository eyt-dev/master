<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('game_clip', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('game_id');
            $table->integer('text_length');
            // Using enum to restrict values to 'H' or 'V'
            $table->string('text_orientation');
            $table->string('color')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            // Define the foreign key constraint referencing games table
            $table->foreign('game_id')
                  ->references('id')->on('games')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('game_clip');
    }
};
