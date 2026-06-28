<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('chicks_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('breed');
            $table->text('location')->nullable();
            $table->text('address')->nullable();
            $table->string('contact_person');
            $table->string('mobile_number');
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('chicks_suppliers');
    }
};
