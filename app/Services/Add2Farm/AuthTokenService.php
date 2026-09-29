<?php

namespace App\Services\Add2Farm;

use App\Models\Admin;
use App\Models\UserDevice;
use App\Models\RefreshSession;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AuthTokenService
{
    const ACCESS_TOKEN_EXPIRY_MINUTES = 15;
    const REFRESH_TOKEN_EXPIRY_DAYS = 30;

    public function generateTokens(Admin $admin, string $deviceId, string $platform, ?string $appVersion = null): array
    {
        // Deactivate any existing active device for this user (one device per user)
        $admin->userDevices()->where('is_active', true)->update(['is_active' => false]);

        // Create or update device
        $device = UserDevice::updateOrCreate(
            ['device_id' => $deviceId],
            [
                'user_id' => $admin->id,
                'platform' => $platform,
                'app_version' => $appVersion,
                'is_active' => true,
                'last_seen_at' => now(),
            ]
        );

        // Revoke old refresh tokens for this device
        $device->refreshSessions()->where('is_revoked', false)->update([
            'is_revoked' => true,
            'revoked_at' => now(),
        ]);

        // Generate short-lived access token using Sanctum
        $accessToken = $admin->createToken('add2farm-access-token', ['*'])->plainTextToken;

        // Set access token expiry manually (Sanctum doesn't set it by default)
        // We'll track this in the token name and validate in middleware
        $sanctumToken = $admin->tokens()->latest()->first();
        $sanctumToken->update([
            'expires_at' => now()->addMinutes(self::ACCESS_TOKEN_EXPIRY_MINUTES),
        ]);

        // Generate long-lived refresh token
        $refreshTokenPlain = Str::random(80);
        $refreshToken = RefreshSession::create([
            'user_id' => $admin->id,
            'device_id' => $device->id,
            'token' => Hash::make($refreshTokenPlain),
            'expires_at' => now()->addDays(self::REFRESH_TOKEN_EXPIRY_DAYS),
        ]);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshTokenPlain,
            'token_type' => 'Bearer',
            'expires_in' => self::ACCESS_TOKEN_EXPIRY_MINUTES * 60, // in seconds
            'refresh_expires_in' => self::REFRESH_TOKEN_EXPIRY_DAYS * 24 * 60 * 60, // in seconds
        ];
    }

    public function refreshAccessToken(string $refreshTokenPlain, Admin $admin): ?array
    {
        // Find the refresh session
        $refreshSession = RefreshSession::where('user_id', $admin->id)
            ->where('is_revoked', false)
            ->where('expires_at', '>', now())
            ->get()
            ->first(function ($session) use ($refreshTokenPlain) {
                return Hash::check($refreshTokenPlain, $session->token);
            });

        if (!$refreshSession) {
            return null;
        }

        // Revoke old access tokens
        $admin->tokens()->delete();

        // Generate new access token
        $accessToken = $admin->createToken('add2farm-access-token', ['*'])->plainTextToken;

        // Set access token expiry
        $sanctumToken = $admin->tokens()->latest()->first();
        $sanctumToken->update([
            'expires_at' => now()->addMinutes(self::ACCESS_TOKEN_EXPIRY_MINUTES),
        ]);

        return [
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => self::ACCESS_TOKEN_EXPIRY_MINUTES * 60, // in seconds
        ];
    }

    public function revokeAllTokens(Admin $admin): void
    {
        // Revoke Sanctum access tokens
        $admin->tokens()->delete();

        // Revoke refresh sessions
        $admin->refreshSessions()->where('is_revoked', false)->update([
            'is_revoked' => true,
            'revoked_at' => now(),
        ]);

        // Deactivate all devices
        $admin->userDevices()->update(['is_active' => false]);
    }

    public function revokeDeviceTokens(UserDevice $device): void
    {
        // Revoke all refresh sessions for this device
        $device->refreshSessions()->where('is_revoked', false)->update([
            'is_revoked' => true,
            'revoked_at' => now(),
        ]);

        // Deactivate device
        $device->deactivate();
    }
}
