<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('flock_hangars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('flock_id')->unsigned();
            $table->bigInteger('hangar_id')->unsigned();
            $table->integer('quantity');
            $table->timestamps();
            
            $table->unique(['flock_id', 'hangar_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('flock_hangars');
    }
};
