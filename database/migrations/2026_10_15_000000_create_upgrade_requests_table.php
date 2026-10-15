<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upgrade_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id'); // Farm Owner (type 2) requesting upgrade to Farm Admin
            $table->string('status', 20)->default('Pending'); // Pending, Approved
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins');
            $table->foreign('approved_by')->references('id')->on('admins');
            $table->index(['admin_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upgrade_requests');
    }
};
