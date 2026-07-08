<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('material_stock_hangars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('material_stock_id')->unsigned();
            $table->bigInteger('hangar_id')->unsigned();
            $table->integer('quantity');
            $table->timestamps();
            
            $table->unique(['material_stock_id', 'hangar_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_stock_hangars');
    }
};
