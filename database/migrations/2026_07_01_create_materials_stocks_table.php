<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('material_stocks', function (Blueprint $table) {
            $table->id();
            $table->date('stock_date');
            $table->string('name');
            $table->integer('quantity');
            $table->bigInteger('supplier_id')->unsigned();
            $table->bigInteger('farm_id')->unsigned();
            $table->bigInteger('created_by')->unsigned();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('material_stocks');
    }
};
