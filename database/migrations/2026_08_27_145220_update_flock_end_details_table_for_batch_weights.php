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
        Schema::table('flock_end_details', function (Blueprint $table) {
            $table->dropForeign(['hangar_id']);
            $table->dropColumn(['hangar_id', 'initial_qty', 'final_qty', 'mortality_qty', 'end_weight', 'loss_reason']);

            $table->integer('batch_number')->after('flock_end_id');
            $table->decimal('batch_weight', 10, 2)->after('batch_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flock_end_details', function (Blueprint $table) {
            $table->dropColumn(['batch_number', 'batch_weight']);

            $table->unsignedBigInteger('hangar_id');
            $table->integer('initial_qty');
            $table->integer('final_qty');
            $table->integer('mortality_qty');
            $table->decimal('end_weight', 10, 2)->nullable();
            $table->string('loss_reason')->nullable();

            $table->foreign('hangar_id')->references('id')->on('hangars')->onDelete('cascade');
        });
    }
};
