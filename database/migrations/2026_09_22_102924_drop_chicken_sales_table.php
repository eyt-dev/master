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
        Schema::dropIfExists('chicken_sales');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('chicken_sales', function (Blueprint $table) {
            $table->id();
            $table->date('sale_date');
            $table->bigInteger('farm_id')->unsigned();
            $table->bigInteger('flock_id')->unsigned();
            $table->bigInteger('hangar_id')->unsigned();
            $table->bigInteger('slaughter_id')->unsigned();
            $table->integer('quantity');
            $table->decimal('total_weight', 10, 2);
            $table->decimal('gross_weight', 10, 2);
            $table->integer('no_of_cages');
            $table->integer('no_of_birds')->unique();
            $table->decimal('net_weight', 10, 2);
            $table->decimal('avg_weight_per_bird', 10, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
            $table->timestamps();
        });
    }
};
