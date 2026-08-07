<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add 2FA (Two-Factor Authentication) fields to admins table.
     * These fields are used for OTP-based authentication in Add2Farm APIs.
     * 
     * - mobile_number: User's phone number for Add2Farm authentication
     * - otp: 6-digit one-time password
     * - otp_expires_at: Timestamp when OTP expires (10 minutes from generation)
     * - otp_verified_at: Timestamp when OTP was verified
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Mobile number for Add2Farm authentication
            $table->string('mobile_number')->unique()->nullable()->after('email')
                ->comment('Mobile number for Add2Farm authentication');

            // OTP fields for 2FA
            $table->string('otp')->nullable()->after('password')
                ->comment('6-digit OTP for two-factor authentication');
            
            $table->timestamp('otp_expires_at')->nullable()->after('otp')
                ->comment('Timestamp when OTP expires (10 minutes from generation)');
            
            $table->timestamp('otp_verified_at')->nullable()->after('otp_expires_at')
                ->comment('Timestamp when OTP was verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropUnique(['mobile_number']);
            $table->dropColumn(['mobile_number', 'otp', 'otp_expires_at', 'otp_verified_at']);
        });
    }
};
