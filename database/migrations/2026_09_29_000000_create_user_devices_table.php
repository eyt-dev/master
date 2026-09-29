<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')
                ->comment('Reference to the user (admin)');
            $table->string('device_id')
                ->comment('Unique device identifier from mobile app');
            $table->text('fcm_token')
                ->nullable()
                ->comment('Firebase Cloud Messaging token for push notifications');
            $table->enum('platform', ['android', 'ios'])
                ->comment('Device platform: android or ios');
            $table->string('app_version')
                ->nullable()
                ->comment('Version of the mobile app');
            $table->boolean('is_active')
                ->default(true)
                ->comment('Whether the device is currently active');
            $table->timestamp('last_seen_at')
                ->nullable()
                ->comment('Last time this device accessed the API');
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')
                ->references('id')
                ->on('admins')
                ->onDelete('cascade');

            // Unique constraint: one device per user enforced via device_id uniqueness
            $table->unique('device_id');

            // Index for finding active devices by user
            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
