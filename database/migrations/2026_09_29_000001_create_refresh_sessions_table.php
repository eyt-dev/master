<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refresh_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')
                ->comment('Reference to the user (admin)');
            $table->unsignedBigInteger('device_id')
                ->nullable()
                ->comment('Reference to the device (linked when device is registered)');
            $table->text('token')
                ->unique()
                ->comment('Refresh token (hashed for security)');
            $table->timestamp('expires_at')
                ->comment('When this refresh token expires');
            $table->boolean('is_revoked')
                ->default(false)
                ->comment('Whether this refresh token has been revoked');
            $table->timestamp('revoked_at')
                ->nullable()
                ->comment('When this refresh token was revoked');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade');

            $table->foreign('device_id')
                ->references('id')
                ->on('user_devices')
                ->onDelete('cascade');

            // Indexes
            $table->index(['user_id', 'is_revoked']);
            $table->index(['expires_at', 'is_revoked']);
            $table->index(['device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refresh_sessions');
    }
};
