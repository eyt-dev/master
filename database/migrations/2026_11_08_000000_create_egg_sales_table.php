<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('egg_sales', function (Blueprint $table) {
            $table->id();
            $table->date('sale_date');
            $table->foreignId('farm_id')->constrained('farms');
            $table->foreignId('flock_id')->constrained('flocks');
            $table->foreignId('egg_wholesaler_id')->constrained('egg_wholesalers');
            $table->unsignedInteger('tray');
            $table->unsignedInteger('total_eggs');
            $table->decimal('box', 12, 2);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('admins');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('egg_sales');
    }
};
