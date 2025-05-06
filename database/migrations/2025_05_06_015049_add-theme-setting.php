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
        // Schema::create('themes', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->text('description')->nullable();
        //     $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');
        //     $table->timestamps();
        // });

        Schema::table('settings', function (Blueprint $table1) {
            // Add the theme column with default value 1
            $table1->unsignedBigInteger('theme')->default(1)->after('domain');

            // Then add the foreign key constraint
            $table1->foreign('theme')
                ->references('id')
                ->on('themes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('themes');
    }
};
