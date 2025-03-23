<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_view_id'); // Foreign Key
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('store_view_id')->references('id')->on('store_views')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
