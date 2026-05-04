<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        $admin = $request->user();

        return response()->json([
            'success' => true,
            'user'    => $this->formatUser($admin),
        ]);
    }

    /**
     * Update name, email, username, url.
     */
    public function update(Request $request)
    {
        $admin = $request->user();

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'username' => 'required|string|max:255|unique:admins,username,' . $admin->id,
            'url'      => 'nullable|url|max:255',
            'vat_country_code' => 'nullable|string|max:4',
            'vat_number'       => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $admin->update($request->only([
            'name', 'email', 'username', 'url', 'vat_country_code', 'vat_number',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user'    => $this->formatUser($admin->fresh()),
        ]);
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $admin = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password'         => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        if (! Hash::check($request->current_password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
            ], 422);
        }

        $admin->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.',
        ]);
    }

    // -------------------------------------------------------------------------

    private function formatUser($admin): array
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
            'updated_at'       => $admin->updated_at,
        ];
    }
}
