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
        Schema::create('flock_ends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('flock_id');
            $table->unsignedBigInteger('slaughter_id')->nullable();
            $table->date('end_date');
            $table->integer('total_initial_qty');
            $table->integer('total_final_qty');
            $table->integer('total_mortality');
            $table->decimal('mortality_rate', 5, 2);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('ended_by');
            $table->timestamps();

            $table->foreign('flock_id')->references('id')->on('flocks')->onDelete('cascade');
            $table->foreign('slaughter_id')->references('id')->on('slaughters')->onDelete('set null');
            $table->foreign('ended_by')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flock_ends');
    }
};
