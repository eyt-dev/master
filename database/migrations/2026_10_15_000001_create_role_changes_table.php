<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_changes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id'); // user whose role changed
            $table->string('action', 30); // upgrade, downgrade, approve_upgrade
            $table->unsignedTinyInteger('from_type');
            $table->unsignedTinyInteger('to_type');
            $table->string('from_role', 100)->nullable();
            $table->string('to_role', 100);
            $table->unsignedBigInteger('upgrade_request_id')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable(); // self, or the Farm Admin who approved
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins');
            $table->foreign('changed_by')->references('id')->on('admins');
            $table->foreign('upgrade_request_id')->references('id')->on('upgrade_requests');
            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_changes');
    }
};
