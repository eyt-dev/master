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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            $table->string('domain')->nullable();
            $table->string('admin_domain')->nullable();

            $table->string('dark_logo')->nullable();
            $table->string('light_logo')->nullable();
            $table->string('footer_logo')->nullable();
            $table->string('favicon')->nullable();

            $table->string('primary_text_color')->nullable();
            $table->string('secondary_text_color')->nullable();            
            $table->string('primary_button_background')->nullable();
            $table->string('secondary_button_background')->nullable();
            $table->string('primary_button_text_color')->nullable();
            $table->string('secondary_button_text_color')->nullable();

            $table->foreignId('created_by')->constrained('admins')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
