<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\Add2Farm\TranslationService;

/**
 * @group Add2Farm User Management
 * APIs for user profile management and account settings
 */
class ProfileController extends Controller
{
    protected TranslationService $translationService;

    public function __construct(TranslationService $translationService)
    {
        $this->translationService = $translationService;
    }
    /**
     * Get user profile
     *
     * Retrieve the authenticated user's profile information.
     *
     * @authenticated
     * @response 200 {
     *   "success": true,
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "username": "johndoe",
     *     "type": 3,
     *     "status": "Active"
     *   }
     * }
     */
    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user'    => $this->formatUser($user),
        ]);
    }

    /**
     * Update user profile
     *
     * Update the authenticated user's profile information including name, email, and username.
     *
     * @authenticated
     * @bodyParam name string The user's full name. Example: Jane Doe
     * @bodyParam email string The user's email address. Must be unique (except current). Example: jane@example.com
     * @bodyParam username string The user's username. Must be unique (except current). Example: janedoe
     * @response 200 {
     *   "success": true,
     *   "message": "Profile updated successfully.",
     *   "user": {
     *     "id": 1,
     *     "name": "Jane Doe",
     *     "email": "jane@example.com",
     *     "username": "janedoe",
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
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'email'    => 'sometimes|required|email|max:255|unique:admins,email,' . $user->id,
            'username' => 'sometimes|required|string|max:255|unique:admins,username,' . $user->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user->update($request->only(['name', 'email', 'username']));

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('profile_updated_successfully'),
            'user'    => $this->formatUser($user),
        ]);
    }

    /**
     * Change user password
     *
     * Change the authenticated user's password. Requires providing the current password for verification.
     *
     * @authenticated
     * @bodyParam current_password string required The user's current password. Example: oldpassword123
     * @bodyParam password string required The new password. Minimum 8 characters. Example: newpassword456
     * @bodyParam password_confirmation string required Password confirmation. Must match password field. Example: newpassword456
     * @response 200 {
     *   "success": true,
     *   "message": "Password changed successfully."
     * }
     * @response 401 {
     *   "success": false,
     *   "message": "Current password is incorrect."
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "password": ["The password must be at least 8 characters."]
     *   }
     * }
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

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

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('current_password_incorrect'),
            ], 401);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('password_changed_successfully'),
        ]);
    }

    // -------------------------------------------------------------------------

    /**
     * Get type label based on type value
     */
    private function getTypeLabel($type): string
    {
        return $this->translationService->getTypeLabel($type);
    }

    private function formatUser($user): array
    {
        return [
            'id'            => $user->id,
            'name'          => $user->name,
            'email'         => $user->email,
            'username'      => $user->username,
            'mobile_number' => $user->mobile_number,
            'type'          => $user->type,
            'type_label'    => $this->getTypeLabel($user->type),
            'status'        => $user->status,
            'status_label'  => $this->getStatusLabel($user->status),
            'created_by_name' => $user->creator?->name ?? null,
        ];
    }

    /**
     * Get status label based on status value
     */
    private function getStatusLabel($status): string
    {
        return $this->translationService->getStatusLabel($status);
    }
}
