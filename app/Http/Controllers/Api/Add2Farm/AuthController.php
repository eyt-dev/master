<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @group Add2Farm Authentication
 * APIs for Add2Farm user authentication with 2FA (OTP-based)
 */
class AuthController extends Controller
{
    /**
     * Register a new Add2Farm user
     *
     * Create a new Add2Farm account using mobile number and password.
     * User is created with Inactive status. OTP is generated and must be verified.
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number. Example: +1234567890
     * @bodyParam password string required Password (min 8 characters). Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Example: password123
     * @bodyParam type integer required User type (1=Farm Admin, 2=Farm Owner). Example: 1
     * @bodyParam name string optional User's full name. Example: John Doe
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
            'mobile_number' => 'required|string|max:20|unique:admins,mobile_number',
            'password'      => 'required|string|min:8|confirmed',
            'type'          => 'required|integer|in:1,2',
            'name'          => 'nullable|string|max:255',
            'email'         => 'required|email|max:255|unique:admins,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create admin account with Inactive status
            $admin = Admin::create([
                'mobile_number' => $request->mobile_number,
                'password'      => Hash::make($request->password),
                'type'          => Admin::PRIVATE_VENDOR,
                'status'        => 'Inactive',
                'name'          => $request->name ?? 'Add2Farm User',
                'created_from'  => 3,
                'email'         => $request->email,
            ]);

            // Assign PrivateVendor role
            $role = Role::where('name', 'PrivateVendor')->first();
            if ($role) {
                $admin->assignRole($role);
            }

            // Generate OTP
            $otp = $admin->generateOtp();

            DB::commit();

            // TODO: Send OTP via SMS provider

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add2Farm registration error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Registration failed.',
            ], 500);
        }
    }

    /**
     * Login user with mobile number and password
     *
     * Authenticate using mobile number and password.
     * OTP is generated and sent. User must verify OTP to obtain auth token.
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number. Example: +1234567890
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
     *   "message": "Your account has been disabled."
     * }
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|string|max:20',
            'password'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Find user by mobile number
        $admin = Admin::where('mobile_number', $request->mobile_number)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Check account status
        if ($admin->status === 'Disable') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been disabled.',
            ], 403);
        }

        // Generate new OTP
        $otp = $admin->generateOtp();

        // TODO: Send OTP via SMS provider

        return response()->json([
            'success' => true,
            'message' => 'OTP sent successfully.',
        ]);
    }

    /**
     * Verify OTP and obtain authentication token
     *
     * Verify the 6-digit OTP sent to user's mobile number.
     * On successful verification:
     * - Account status is set to Active (if Inactive)
     * - Sanctum auth token is generated
     * - OTP is cleared from database
     *
     * Development Override: OTP '000000' is accepted for testing.
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number. Example: +1234567890
     * @bodyParam otp string required 6-digit OTP code. Example: 123456
     *
     * @response 200 {
     *   "success": true,
     *   "message": "OTP verified successfully.",
     *   "token": "1|add2farm-token|...",
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
            'mobile_number' => 'required|string|max:20',
            'otp'           => 'required|string|size:6|regex:/^\d+$/',
            'context'       => 'nullable|string|in:registration,forgot_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Find user by mobile number
        $admin = Admin::where('mobile_number', $request->mobile_number)->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Check if OTP exists
        if (!$admin->otp) {
            return response()->json([
                'success' => false,
                'message' => 'No OTP found. Please request OTP first.',
            ], 400);
        }

        // Check if OTP has expired
        if ($admin->isOtpExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new OTP.',
            ], 422);
        }

        // Verify OTP
        if (!$admin->isOtpValid($request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP.',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Mark OTP as verified and clear it
            $admin->markOtpVerified();

            $context = $request->input('context', 'registration');

            // For forgot password flow: return only verification token, don't login user
            if ($context === 'forgot_password') {
                // Generate a temporary verification token for password reset
                $token = $admin->createToken('password-reset-token')->plainTextToken;

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'OTP verified successfully. You can now reset your password.',
                    'token'   => $token,
                ]);
            }

            // For registration flow: login the user
            // Update account status to Active if Inactive
            if ($admin->status === 'Inactive') {
                $admin->update(['status' => 'Active']);
            }

            // Revoke previous tokens
            $admin->tokens()->delete();

            // Generate new token
            $token = $admin->createToken('add2farm-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'OTP verified successfully.',
                'token'   => $token,
                'user'    => $this->formatUser($admin->fresh()),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('OTP verification error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed.',
            ], 500);
        }
    }

    /**
     * Resend OTP to user's mobile number
     *
     * Generate a new OTP and send it to the user's registered mobile number.
     * This endpoint resets the OTP expiry to 10 minutes from current time.
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number. Example: +1234567890
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
            'mobile_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Find user by mobile number
        $admin = Admin::where('mobile_number', $request->mobile_number)->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        try {
            // Generate new OTP
            $otp = $admin->generateOtp();

            // TODO: Send OTP via SMS provider

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Resend OTP error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to resend OTP.',
            ], 500);
        }
    }

    /**
     * Forgot Password - Step 1
     *
     * Initiate forgot password flow by providing mobile number.
     * OTP is generated and sent to verify identity.
     *
     * @unauthenticated
     * @bodyParam mobile_number string required User's mobile number. Example: +1234567890
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
            'mobile_number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Find user by mobile number
        $admin = Admin::where('mobile_number', $request->mobile_number)->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        try {
            // Generate OTP
            $otp = $admin->generateOtp();

            // TODO: Send OTP via SMS provider

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully.',
            ]);

        } catch (\Exception $e) {
            \Log::error('Forgot password error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP.',
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

        // Verify token and get user
        $personalAccessToken = PersonalAccessToken::findToken($request->token);

        if (!$personalAccessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token. Please verify OTP first.',
            ], 401);
        }

        // Get the admin user from the token
        $admin = $personalAccessToken->tokenable;

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Check if token is a password-reset-token
        if ($personalAccessToken->name !== 'password-reset-token') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token. Please use token from OTP verification.',
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
                'message' => 'Password reset successfully. Please login with new password.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Reset password error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Password reset failed.',
            ], 500);
        }
    }

    /**
     * Logout user
     *
     * Revoke the current access token and logout the authenticated user.
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
                'message' => 'User not authenticated.',
            ], 401);
        }

        $token = $user->currentAccessToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired token. Please login again.',
            ], 401);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Get type label based on type value
     */
    private function getTypeLabel($type): string
    {
        $types = [
            1 => 'Farm Admin',
            2 => 'Farm Owner',
            3 => 'Supervisor',
            4 => 'Farmers',
        ];
        return $types[$type] ?? 'Unknown';
    }

    /**
     * Format user data for API responses
     */
    private function formatUser(Admin $admin): array
    {
        return [
            'id'            => $admin->id,
            'name'          => $admin->name,
            'mobile_number' => $admin->mobile_number,
            'type'          => $admin->type,
            'type_label'    => $this->getTypeLabel($admin->type),
            'status'        => $admin->status,
            'created_by_name' => $admin->creator?->name ?? null,
            'created_at'    => $admin->created_at,
        ];
    }
}
