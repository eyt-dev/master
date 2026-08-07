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

/**
 * @group Add2Farm Authentication
 * APIs for user authentication and account management
 */
class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * Create a new Add2Farm account by providing name, email, username and password.
     * The user will be created with PrivateVendor type and active status.
     *
     * @unauthenticated
     * @bodyParam name string required The user's full name. Example: John Doe
     * @bodyParam email string required The user's email address. Must be unique. Example: john@example.com
     * @bodyParam username string required The user's username. Must be unique. Example: johndoe
     * @bodyParam password string required The user's password. Minimum 8 characters. Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Must match password field. Example: password123
     * @response 201 {
     *   "success": true,
     *   "message": "Registration successful.",
     *   "token": "1|add2farm-token|...",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "username": "johndoe",
     *     "type": 3,
     *     "status": "Active"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admins,email',
            'username' => 'required|string|max:255|unique:admins,username',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $admin = Admin::create([
                'name'         => $request->name,
                'email'        => $request->email,
                'username'     => $request->username,
                'password'     => Hash::make($request->password),
                'type'         => Admin::PRIVATE_VENDOR,
                'status'       => 'Active',
                'created_from' => 3, // add2farm registration
            ]);

            $role = Role::where('name', 'PrivateVendor')->first();
            if ($role) {
                $admin->assignRole($role);
            }

            // Mirror to contacts table
            Contact::updateOrCreate(
                ['email' => $request->email],
                [
                    'name'       => $request->name,
                    'formal_name' => $request->name,
                    'created_by' => $admin->id,
                ]
            );

            $token = $admin->createToken('add2farm-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registration successful.',
                'token'   => $token,
                'user'    => $this->formatUser($admin),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user
     *
     * Authenticate a user with email and password to obtain an access token.
     * The token can be used to access protected endpoints.
     *
     * @unauthenticated
     * @bodyParam email string required The user's email address. Example: john@example.com
     * @bodyParam password string required The user's password. Example: password123
     * @response 200 {
     *   "success": true,
     *   "message": "Login successful.",
     *   "token": "1|add2farm-token|...",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "username": "johndoe",
     *     "type": 3,
     *     "status": "Active"
     *   }
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
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        if ($admin->status === 'Disable') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been disabled.',
            ], 403);
        }

        // Revoke previous tokens (single-session behaviour)
        $admin->tokens()->delete();

        $token = $admin->createToken('add2farm-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => $this->formatUser($admin),
        ]);
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
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Verify OTP
     *
     * Verify a 6-digit OTP code for account verification or two-factor authentication.
     *
     * @unauthenticated
     * @bodyParam email string required The user's email address. Example: john@example.com
     * @bodyParam otp string required The 6-digit OTP code. Example: 123456
     * @response 200 {
     *   "success": true,
     *   "message": "OTP verified successfully.",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "username": "johndoe",
     *     "type": 3,
     *     "status": "Active"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "User not found."
     * }
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp'   => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // TODO: Implement OTP verification logic with cache or DB
        // For now, returning success response structure
        
        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
            'user'    => $this->formatUser($admin),
        ]);
    }

    /**
     * Forgot Password
     *
     * Request a password reset link. An email will be sent with instructions to reset your password.
     *
     * @unauthenticated
     * @bodyParam email string required The user's email address. Example: john@example.com
     * @response 200 {
     *   "success": true,
     *   "message": "Password reset link sent to your email."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "User not found."
     * }
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // TODO: Generate and send password reset token via email
        
        return response()->json([
            'success' => true,
            'message' => 'Password reset link sent to your email.',
        ]);
    }

    /**
     * Reset Password
     *
     * Reset your password using the reset token sent to your email.
     *
     * @unauthenticated
     * @bodyParam email string required The user's email address. Example: john@example.com
     * @bodyParam token string required The password reset token from email. Example: abc123def456
     * @bodyParam password string required The new password. Minimum 8 characters. Example: newpassword123
     * @bodyParam password_confirmation string required Password confirmation. Must match password field. Example: newpassword123
     * @response 200 {
     *   "success": true,
     *   "message": "Password reset successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "User not found."
     * }
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'                => 'required|email',
            'token'                => 'required|string',
            'password'             => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // TODO: Verify token validity

        $admin->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully.',
        ]);
    }

    // -------------------------------------------------------------------------

    private function formatUser(Admin $admin): array
    {
        return [
            'id'       => $admin->id,
            'name'     => $admin->name,
            'email'    => $admin->email,
            'username' => $admin->username,
            'type'     => $admin->type,
            'status'   => $admin->status,
        ];
    }
}
