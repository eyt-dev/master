<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('daily_records', function (Blueprint $table) {
            $table->id();
            $table->date('record_date');
            $table->foreignId('farm_id')->constrained('farms')->onDelete('cascade');
            $table->foreignId('hangar_id')->constrained('hangars')->onDelete('cascade');
            $table->decimal('feed_kg', 10, 2);
            $table->integer('eggs_tray_30');
            $table->integer('eggs_count');
            $table->integer('mortality');
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_records');
    }
};
