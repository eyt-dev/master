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
     * Supports login via email or mobile number (with flexible phone code formats).
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required_without:mobile_number|email|nullable',
            'mobile_number' => 'required_without:email|string|nullable',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $admin = null;

        if ($request->filled('email')) {
            $admin = Admin::where('email', $request->email)->first();
        } else {
            $admin = $this->findAdminByMobileNumber($request->mobile_number);
        }

        // Apply type and project filters
        if ($admin) {
            $isAllowed = $admin->type == 0 || $admin->projectStatuses()
                ->whereHas('project', function ($query) {
                    $query->where('url', 'LIKE', '%add2mix.eyt.app%');
                })->exists();

            if (!$isAllowed) {
                $admin = null;
            }
        }

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
     * Find admin by mobile number with flexible format support.
     * Handles: "+91 9876543210", "+919876543210", "9876543210"
     */
    private function findAdminByMobileNumber($input): ?Admin
    {
        $input = trim($input);

        // First, try exact match
        $admin = Admin::where('mobile_number', $input)->first();
        if ($admin) {
            return $admin;
        }

        // Check if input starts with + (international format)
        if (strpos($input, '+') === 0) {
            // Remove spaces
            $cleaned = str_replace(' ', '', $input);
            $digitsOnly = ltrim($cleaned, '+');

            // Try different phone code lengths (1-3 digits)
            for ($codeLength = 1; $codeLength <= 3; $codeLength++) {
                if (strlen($digitsOnly) <= $codeLength) {
                    continue;
                }

                $phoneCode = substr($digitsOnly, 0, $codeLength);
                $mobileNumber = substr($digitsOnly, $codeLength);

                // Try without + prefix
                $admin = Admin::where('phone_code', $phoneCode)
                    ->where('mobile_number', $mobileNumber)
                    ->first();
                if ($admin) {
                    return $admin;
                }

                // Try with + prefix
                $admin = Admin::where('phone_code', '+' . $phoneCode)
                    ->where('mobile_number', $mobileNumber)
                    ->first();
                if ($admin) {
                    return $admin;
                }
            }

            // Fallback: try full number as mobile_number
            $admin = Admin::where('mobile_number', $digitsOnly)->first();
            if ($admin) {
                return $admin;
            }
        }

        return null;
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
