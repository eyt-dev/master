<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminProjectStatus;
use App\Models\Contact;
use App\Models\UserDevice;
use App\Models\RefreshSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\PersonalAccessToken;
use App\Services\Add2Farm\TranslationService;
use App\Services\Add2Farm\AuthTokenService;
use App\Helpers\ProjectHelper;

/**
 * @group Add2Farm Authentication
 * APIs for Add2Farm user authentication with 2FA (OTP-based)
 */
class AuthController extends Controller
{
    protected TranslationService $translationService;
    protected AuthTokenService $tokenService;

    public function __construct(TranslationService $translationService, AuthTokenService $tokenService)
    {
        $this->translationService = $translationService;
        $this->tokenService = $tokenService;
    }
    /**
     * Register a new Add2Farm user
     *
     * Create a new Add2Farm account using mobile number and password.
     * User is created with Inactive status. OTP is generated and must be verified.
     *
     * @unauthenticated
     * @bodyParam phone_code string optional Country phone code. Example: +91
     * @bodyParam mobile_number string required User's mobile number. Example: 9033487938
     * @bodyParam password string required Password (min 8 characters). Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Example: password123
     * @bodyParam type integer required User type (1=Farm Admin, 2=Farm Owner). Example: 1
     * @bodyParam name string optional User's full name. Example: John Doe
     * @bodyParam email string optional User's email address. Example: john@example.com
     *
     * @response 201 {
     *   "success": true,
     *   "message": "OTP sent successfully."
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "mobile_number": ["The mobile number has already been taken."]
     *   }
     * }
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_code'    => 'nullable|string|max:5',
            'mobile_number' => 'required|string|max:20|unique:admins,mobile_number',
            'password'      => 'required|string|min:8|confirmed',
            'type'          => 'required|integer|in:1,2',
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|email|max:255|unique:admins,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Normalize phone_code to include + prefix if provided
            $phoneCode = null;
            if ($request->phone_code) {
                $phoneCode = strpos($request->phone_code, '+') === 0 ? $request->phone_code : '+' . $request->phone_code;
            }

            // Create admin account with Inactive status
            $admin = Admin::create([
                'phone_code'    => $phoneCode,
                'mobile_number' => $request->mobile_number,
                'password'      => Hash::make($request->password),
                'type'          => $request->type,
                'status'        => 'Inactive',
                'name'          => $request->name ?? 'Add2Farm User',
                'created_from'  => 3,
                'email'         => $request->email,
            ]);

            // Assign role based on type (1=Admin, 2=PublicVendor)
            $rolePrefix = match (intval($request->type)) {
                1 => 'Admin',
                2 => 'PublicVendor',
                default => 'PublicVendor',
            };
            $role = Role::where('name', $rolePrefix)->first();
            if ($role) {
                $admin->assignRole($role);
            }

            // Auto-assign to Add2Farm project with Active status
            $add2FarmProjectId = ProjectHelper::getAdd2FarmProjectId();
            if ($add2FarmProjectId) {
                AdminProjectStatus::create([
                    'admin_id'   => $admin->id,
                    'project_id' => $add2FarmProjectId,
                    'status'     => 'Active',
                ]);
            }

            // Generate OTP
            $otp = $admin->generateOtp();

            DB::commit();

            // TODO: Send OTP via SMS provider

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('otp_sent_successfully'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add2Farm registration error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('registration_failed'),
            ], 500);
        }
    }

    /**
     * Login user with mobile number and password
     *
     * Authenticate using mobile number and password.
     * Requires:
     * - Valid credentials (mobile number + password)
     * - Account status must be Active (not Disable)
     * - User must have Active project assignment to Add2Farm
     *
     * OTP is generated and sent. User must verify OTP to obtain auth token.
     *
     * Mobile number format:
     * - Can include phone code: "+1 1234567890" or "+11234567890"
     * - Or just the number: "1234567890"
     * - System will automatically parse and match both formats
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number (with or without phone code). Example: +1 1234567890
     * @bodyParam password string required User's password. Example: password123
     *
     * @response 200 {
     *   "success": true,
     *   "message": "OTP sent successfully."
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Invalid credentials."
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Your account has been disabled. OR Your access to Add2Farm project is not active."
     * }
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|max:50',
            'password'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Parse mobile number (could be "+1 1234567890" or "+11234567890" or "1234567890")
        $admin = $this->findAdminByMobileNumber($request->mobile_number);

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('invalid_credentials'),
            ], 401);
        }

        // Check account status
        if ($admin->status === 'Disable') {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('account_disabled'),
            ], 403);
        }

        // Check if admin has active project assignment to Add2Farm (skip for SuperAdmin)
        if ($admin->role !== 'SuperAdmin') {
            $add2FarmProjectId = ProjectHelper::getAdd2FarmProjectId();

            if (!$add2FarmProjectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Add2Farm project configuration not found. Please contact administrator.',
                ], 500);
            }

            $projectStatus = \App\Models\AdminProjectStatus::where('admin_id', $admin->id)
                ->where('project_id', $add2FarmProjectId)
                ->where('status', 'Active')
                ->first();

            if (!$projectStatus) {
                return response()->json([
                    'success' => false,
                    'message' => $this->translationService->get('project_access_not_active'),
                ], 403);
            }
        }

        // Generate new OTP
        $otp = $admin->generateOtp();

        // TODO: Send OTP via SMS provider

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('otp_sent_successfully'),
        ]);
    }

    /**
     * Verify OTP and obtain authentication tokens
     *
     * Verify the 6-digit OTP sent to user's mobile number.
     * On successful verification:
     * - Account status is set to Active (if Inactive)
     * - Short-lived access token and long-lived refresh token are generated
     * - OTP is cleared from database
     *
     * Device registration is a SEPARATE step (call /device-token after login).
     *
     * Development Override: OTP '000000' is accepted for testing.
     *
     * Mobile number format:
     * - Can include phone code: "+91 09033487938" or "+9109033487938"
     * - Or just the number: "09033487938"
     * - System will automatically parse and match both formats
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number (with or without phone code). Example: +91 09033487938
     * @bodyParam otp string required 6-digit OTP code. Example: 123456
     * @bodyParam context string optional Flow context (registration or forgot_password). Example: registration
     *
     * @response 200 {
     *   "success": true,
     *   "message": "OTP verified successfully.",
     *   "access_token": "1|add2farm-access-token|...",
     *   "refresh_token": "long-random-token-string",
     *   "token_type": "Bearer",
     *   "expires_in": 900,
     *   "refresh_expires_in": 2592000,
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "mobile_number": "+1234567890",
     *     "type": 3,
     *     "status": "Active"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "User not found."
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "OTP has expired. Please request a new OTP."
     * }
     * @response 400 {
     *   "success": false,
     *   "message": "Invalid OTP."
     * }
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|max:50',
            'otp'           => 'required|string|size:6|regex:/^\d+$/',
            'context'       => 'nullable|string|in:registration,forgot_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Find user by mobile number (supports both "+91 09033487938" and "09033487938" formats)
        $admin = $this->findAdminByMobileNumber($request->mobile_number);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_found'),
            ], 404);
        }

        // Check if OTP exists
        if (!$admin->otp) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('no_otp_found'),
            ], 400);
        }

        // Check if OTP has expired
        if ($admin->isOtpExpired()) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('otp_expired'),
            ], 422);
        }

        // Verify OTP
        if (!$admin->isOtpValid($request->otp)) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('invalid_otp'),
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Mark OTP as verified and clear it
            $admin->markOtpVerified();

            $context = $request->input('context', 'registration');

            // For forgot password flow: return only verification token, don't login user
            if ($context === 'forgot_password') {
                // Delete old password-reset tokens for this user
                $admin->tokens()->where('name', 'password-reset-token')->delete();

                // Generate a temporary verification token for password reset
                $token = $admin->createToken('password-reset-token')->plainTextToken;

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => $this->translationService->get('otp_verified_password_reset'),
                    'token'   => $token,
                ]);
            }

            // For registration flow: login the user
            // Update account status to Active if Inactive
            if ($admin->status === 'Inactive') {
                $admin->update(['status' => 'Active']);
            }

            // Generate access and refresh tokens (device registration is separate)
            $tokens = $this->tokenService->generateTokens($admin);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('otp_verified_successfully'),
                'access_token' => $tokens['access_token'],
                'refresh_token' => $tokens['refresh_token'],
                'token_type' => $tokens['token_type'],
                'expires_in' => $tokens['expires_in'],
                'refresh_expires_in' => $tokens['refresh_expires_in'],
                'user'    => $this->formatUser($admin->fresh()),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('OTP verification error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('otp_verification_failed'),
            ], 500);
        }
    }

    /**
     * Resend OTP to user's mobile number
     *
     * Generate a new OTP and send it to the user's registered mobile number.
     * This endpoint resets the OTP expiry to 10 minutes from current time.
     *
     * Mobile number format:
     * - Can include phone code: "+91 09033487938" or "+9109033487938"
     * - Or just the number: "09033487938"
     * - System will automatically parse and match both formats
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number (with or without phone code). Example: +91 09033487938
     *
     * @response 200 {
     *   "success": true,
     *   "message": "OTP sent successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "User not found."
     * }
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Find user by mobile number (supports both "+91 09033487938" and "09033487938" formats)
        $admin = $this->findAdminByMobileNumber($request->mobile_number);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_found'),
            ], 404);
        }

        try {
            // Generate new OTP
            $otp = $admin->generateOtp();

            // TODO: Send OTP via SMS provider

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('otp_sent_successfully'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Resend OTP error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('failed_to_resend_otp'),
            ], 500);
        }
    }

    /**
     * Forgot Password - Step 1
     *
     * Initiate forgot password flow by providing mobile number.
     * OTP is generated and sent to verify identity.
     *
     * Mobile number format:
     * - Can include phone code: "+91 09033487938" or "+9109033487938"
     * - Or just the number: "09033487938"
     * - System will automatically parse and match both formats
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number (with or without phone code). Example: +91 09033487938
     *
     * @response 200 {
     *   "success": true,
     *   "message": "OTP sent successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "User not found."
     * }
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Find user by mobile number (supports both "+91 09033487938" and "09033487938" formats)
        $admin = $this->findAdminByMobileNumber($request->mobile_number);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_found'),
            ], 404);
        }

        try {
            // Generate OTP
            $otp = $admin->generateOtp();

            // TODO: Send OTP via SMS provider

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('otp_sent_successfully'),
            ]);

        } catch (\Exception $e) {
            \Log::error('Forgot password error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('failed_to_send_otp'),
            ], 500);
        }
    }

    /**
     * Reset Password - Step 2
     *
     * Reset password after OTP verification in forgot password flow.
     * OTP must be valid and verified before password can be reset.
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number. Example: +1234567890
     * @bodyParam otp string required 6-digit OTP code. Example: 123456
     * @bodyParam password string required New password (min 8 characters). Example: newpassword123
     * @bodyParam password_confirmation string required Password confirmation. Example: newpassword123
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Password reset successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "User not found."
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "OTP has expired. Please request a new OTP."
     * }
     * @response 400 {
     *   "success": false,
     *   "message": "Invalid OTP."
     * }
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Trim token to remove any whitespace
        $tokenInput = trim($request->token);

        // Verify token and get user
        $personalAccessToken = PersonalAccessToken::findToken($tokenInput);

        if (!$personalAccessToken) {
            // Log for debugging
            \Log::warning('Password reset token not found', [
                'token_length' => strlen($tokenInput),
                'token_preview' => substr($tokenInput, 0, 20) . '...'
            ]);

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('invalid_or_expired_token_otp'),
            ], 401);
        }

        // Get the admin user from the token
        $admin = $personalAccessToken->tokenable;

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_found'),
            ], 404);
        }

        // Check if token is a password-reset-token
        if ($personalAccessToken->name !== 'password-reset-token') {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('invalid_token_use_otp'),
            ], 401);
        }

        try {
            DB::beginTransaction();

            // Update password
            $admin->update([
                'password' => Hash::make($request->password),
                'otp' => null,
                'otp_expires_at' => null,
                'otp_verified_at' => null,
            ]);

            // Revoke all tokens for security (user must login again)
            $admin->tokens()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('password_reset_successfully'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Reset password error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('password_reset_failed'),
            ], 500);
        }
    }

    /**
     * Logout user
     *
     * Revoke the current access token, refresh tokens, and deactivate the device.
     * The user will need to login again with OTP to obtain new tokens.
     *
     * @authenticated
     * @response 200 {
     *   "success": true,
     *   "message": "Logged out successfully."
     * }
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('user_not_authenticated'),
            ], 401);
        }

        try {
            DB::beginTransaction();

            // Revoke access token
            $token = $user->currentAccessToken();
            if ($token) {
                $token->delete();
            }

            // Revoke all refresh tokens for this user
            $user->refreshSessions()->where('is_revoked', false)->update([
                'is_revoked' => true,
                'revoked_at' => now(),
            ]);

            // Deactivate all devices
            $user->userDevices()->update(['is_active' => false]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('logged_out_successfully'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Logout error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('logout_failed'),
            ], 500);
        }
    }

    /**
     * Get type label based on type value
     */
    private function getTypeLabel($type): string
    {
        return $this->translationService->getTypeLabel($type);
    }

    /**
     * Format user data for API responses
     */
    private function formatUser(Admin $admin): array
    {
        return [
            'id'            => $admin->id,
            'name'          => $admin->name,
            'mobile_number' => $admin->getFullPhoneNumber(),
            'type'          => $admin->type,
            'type_label'    => $this->getTypeLabel($admin->type),
            'status'        => $admin->status,
            'status_label'  => $this->getStatusLabel($admin->status),
            'assignment'    => $admin->hasAssignment(),
            'created_by_name' => $admin->creator?->name ?? null,
            'created_at'    => $admin->created_at,
        ];
    }

    /**
     * Get status label based on status value
     */
    private function getStatusLabel($status): string
    {
        return $this->translationService->getStatusLabel($status);
    }

    /**
     * Refresh access token
     *
     * Use the refresh token to obtain a new access token without re-entering OTP.
     * The refresh token must be valid and not expired.
     *
     * @unauthenticated
     * @bodyParam refresh_token string required The refresh token received during login. Example: long-random-token-string
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Access token refreshed successfully.",
     *   "access_token": "1|add2farm-access-token|...",
     *   "token_type": "Bearer",
     *   "expires_in": 900
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Invalid or expired refresh token."
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {"refresh_token": ["The refresh token field is required."]}
     * }
     */
    public function refreshToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            // Find the refresh session with the given token
            $refreshSessions = RefreshSession::where('is_revoked', false)
                ->where('expires_at', '>', now())
                ->get();

            $refreshSession = $refreshSessions->first(function ($session) use ($request) {
                return Hash::check($request->refresh_token, $session->token);
            });

            if (!$refreshSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired refresh token.',
                ], 401);
            }

            $admin = $refreshSession->user;

            if (!$admin) {
                return response()->json([
                    'success' => false,
                    'message' => $this->translationService->get('user_not_found'),
                ], 404);
            }

            // Generate new access token
            $tokenData = $this->tokenService->refreshAccessToken($request->refresh_token, $admin);

            if (!$tokenData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired refresh token.',
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Access token refreshed successfully.',
                'access_token' => $tokenData['access_token'],
                'token_type' => $tokenData['token_type'],
                'expires_in' => $tokenData['expires_in'],
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Token refresh error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token.',
            ], 500);
        }
    }

    /**
     * Register or update device with FCM token
     *
     * Register a mobile device or update its FCM token for push notifications.
     * The authenticated user must be logged in via OAuth token.
     * One user can only have one active device (previous device is deactivated).
     *
     * @authenticated
     * @bodyParam device_id string required Unique device identifier. Example: abc123xyz789
     * @bodyParam fcm_token string required Firebase Cloud Messaging token. Example: eABC123...
     * @bodyParam platform string required Device platform: android or ios. Example: android
     * @bodyParam app_version string optional Mobile app version. Example: 1.0.0
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Device token registered successfully.",
     *   "device": {
     *     "id": 1,
     *     "device_id": "abc123xyz789",
     *     "platform": "android",
     *     "app_version": "1.0.0",
     *     "is_active": true,
     *     "last_seen_at": "2026-09-29T10:30:00Z"
     *   }
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Unauthenticated."
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "device_id": ["The device id field is required."],
     *     "fcm_token": ["The fcm token field is required."]
     *   }
     * }
     */
    public function registerDeviceToken(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'device_id'  => 'required|string|max:255',
            'fcm_token'  => 'required|string|max:500',
            'platform'   => 'required|string|in:android,ios',
            'app_version' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create or update device
            $device = UserDevice::updateOrCreate(
                ['device_id' => $request->device_id],
                [
                    'user_id' => $user->id,
                    'fcm_token' => $request->fcm_token,
                    'platform' => $request->platform,
                    'app_version' => $request->app_version,
                    'is_active' => true,
                    'last_seen_at' => now(),
                ]
            );

            // Register device and link refresh tokens, enforce one-device-per-user
            $this->tokenService->registerDevice($user, $device);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Device token registered successfully.',
                'device' => [
                    'id' => $device->id,
                    'device_id' => $device->device_id,
                    'platform' => $device->platform,
                    'app_version' => $device->app_version,
                    'is_active' => $device->is_active,
                    'last_seen_at' => $device->last_seen_at,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Device registration error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to register device token.',
            ], 500);
        }
    }

    /**
     * Find admin by mobile number
     * Handles formats: "+1 1234567890", "+11234567890", "1234567890"
     * If phone_code exists, it tries to match both phone_code + mobile_number
     */
    private function findAdminByMobileNumber($input): ?Admin
    {
        // First, try exact match (for backwards compatibility)
        $admin = Admin::where('mobile_number', $input)->first();
        if ($admin) {
            return $admin;
        }

        // Check if input starts with + (international format)
        if (strpos($input, '+') === 0) {
            // Remove spaces first
            $cleaned = str_replace(' ', '', $input);

            // Remove the + for easier parsing
            $digitsOnly = ltrim($cleaned, '+');

            // Try different phone code lengths (1, 2, 3 digits)
            // This handles variable-length country codes like +1, +91, +297, etc.
            for ($codeLength = 1; $codeLength <= 3; $codeLength++) {
                if (strlen($digitsOnly) <= $codeLength) {
                    continue; // Skip if remaining would be too short
                }

                $phoneCode = substr($digitsOnly, 0, $codeLength);
                $mobileNumber = substr($digitsOnly, $codeLength);

                // Try to find with this combination
                // Try without + in phone code
                $admin = Admin::where('phone_code', $phoneCode)
                    ->where('mobile_number', $mobileNumber)
                    ->first();
                if ($admin) {
                    return $admin;
                }

                // Also try with + in phone code
                $admin = Admin::where('phone_code', '+' . $phoneCode)
                    ->where('mobile_number', $mobileNumber)
                    ->first();
                if ($admin) {
                    return $admin;
                }
            }

            // If no match found with phone code, try the entire number as mobile_number
            $admin = Admin::where('mobile_number', $digitsOnly)->first();
            if ($admin) {
                return $admin;
            }
        }

        return null;
    }
}
