<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * Register a new user (type=4 User by default).
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:admins,email',
            'username'         => 'required|string|max:255|unique:admins,username',
            'password'         => 'required|string|min:8|confirmed',
            'vat_country_code' => 'nullable|string|max:4',
            'vat_number'       => 'nullable|string|max:255',
            'url'              => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $admin = Admin::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'username'         => $request->username,
            'password'         => Hash::make($request->password),
            'type'             => Admin::PRIVATE_VENDOR, // default type for self-registration
            'status'           => 'Pending',
            'vat_country_code' => $request->vat_country_code,
            'vat_number'       => $request->vat_number,
            'url'              => $request->url,
            'created_from'     => 2, // registered via API
        ]);

        $role = Role::where('name', 'PrivateVendor')->first();
        if ($role) {
            $admin->assignRole($role);
        }

        // Mirror to contacts table
        Contact::updateOrCreate(
            ['email' => $request->email],
            [
                'name'             => $request->name,
                'formal_name'      => $request->name,
                'vat_country_code' => $request->vat_country_code,
                'vat_number'       => $request->vat_number,
                'created_by'       => $admin->id,
            ]
        );

        $token = $admin->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful.',
            'token'   => $token,
            'user'    => $this->formatUser($admin),
        ], 201);
    }

    /**
     * Login and return a Sanctum token.
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

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
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

        // Revoke previous tokens (optional: single-session behaviour)
        $admin->tokens()->delete();

        $token = $admin->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => $this->formatUser($admin),
        ]);
    }

    /**
     * Logout — revoke current token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    // -------------------------------------------------------------------------

    private function formatUser(Admin $admin): array
    {
        return [
            'id'               => $admin->id,
            'name'             => $admin->name,
            'email'            => $admin->email,
            'username'         => $admin->username,
            'type'             => $admin->type,
            'status'           => $admin->status,
            'role'             => $admin->role,
            'vat_country_code' => $admin->vat_country_code,
            'vat_number'       => $admin->vat_number,
            'url'              => $admin->url,
            'created_at'       => $admin->created_at,
        ];
    }
}
