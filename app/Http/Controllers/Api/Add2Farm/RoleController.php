<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\Admin;
use App\Models\UpgradeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * @group Add2Farm Role Management
 * APIs for upgrading and downgrading the logged-in user's Add2Farm role
 */
class RoleController extends BaseController
{
    const FARMER_TYPE = 4;

    /**
     * Upgrade own role
     *
     * Upgrades the authenticated user based on their current type:
     * - Farm Owner (2): submits an upgrade request to Farm Admin. Stays Farm Owner until approved.
     * - Supervisor (3): upgraded to Farm Owner immediately.
     * - Farmer (4): upgraded to Supervisor immediately.
     * - Farm Admin (1) and others: not allowed.
     *
     * The current role is taken from the Bearer token, not from the request body.
     *
     * @authenticated
     * @response 201 {
     *   "success": true,
     *   "message": "Upgrade request submitted successfully. Waiting for Farm Admin approval.",
     *   "data": {
     *     "id": 1,
     *     "status": "Pending",
     *     "created_at": "2026-10-15T10:00:00Z"
     *   }
     * }
     * @response 200 {
     *   "success": true,
     *   "message": "Role upgraded successfully.",
     *   "type": 2,
     *   "type_label": "Farm Owner"
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Your current role is not allowed to perform this action."
     * }
     * @response 409 {
     *   "success": false,
     *   "message": "You already have a pending upgrade request."
     * }
     */
    public function upgrade(Request $request)
    {
        $user = $request->user();

        switch ((int) $user->type) {
            case Admin::PUBLIC_VENDOR:
                return $this->submitFarmAdminUpgradeRequest($user);

            case Admin::PRIVATE_VENDOR:
                return $this->changeRole($user, Admin::PUBLIC_VENDOR, 'PublicVendor');

            case self::FARMER_TYPE:
                return $this->changeRole($user, Admin::PRIVATE_VENDOR, 'PrivateVendor');

            default:
                return $this->notAllowed();
        }
    }

    /**
     * Downgrade own role
     *
     * Only Supervisors (type 3) can downgrade, to Farmer (type 4).
     *
     * @authenticated
     * @response 200 {
     *   "success": true,
     *   "message": "Role downgraded successfully.",
     *   "type": 4,
     *   "type_label": "Farmer"
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "Your current role is not allowed to perform this action."
     * }
     */
    public function downgrade(Request $request)
    {
        $user = $request->user();

        if ((int) $user->type !== Admin::PRIVATE_VENDOR) {
            return $this->notAllowed();
        }

        return $this->changeRole($user, self::FARMER_TYPE, 'User', 'role_downgraded_successfully');
    }

    /**
     * List pending upgrade requests
     *
     * Farm Admin only. Lists Farm Owner requests to upgrade to Farm Admin that are still pending.
     *
     * @authenticated
     * @queryParam page integer optional Pagination page number. Example: 1
     * @queryParam per_page integer optional Items per page. Default: 15. Example: 20
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Upgrade requests retrieved successfully.",
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "status": "Pending",
     *         "created_at": "2026-10-15T10:00:00Z",
     *         "user": {
     *           "id": 5,
     *           "name": "Farm Owner",
     *           "mobile_number": "+1234567890",
     *           "email": "owner@example.com",
     *           "type": 2,
     *           "type_label": "Farm Owner"
     *         }
     *       }
     *     ],
     *     "total": 1,
     *     "last_page": 1
     *   }
     * }
     * @response 403 {
     *   "success": false,
     *   "message": "You do not have permission to access this resource."
     * }
     */
    public function upgradeRequests(Request $request)
    {
        $requests = UpgradeRequest::with('admin')
            ->where('status', UpgradeRequest::STATUS_PENDING)
            ->orderBy('created_at')
            ->paginate($request->per_page ?? 15);

        $requests->setCollection($requests->getCollection()->map(function ($upgradeRequest) {
            return [
                'id'         => $upgradeRequest->id,
                'status'     => $upgradeRequest->status,
                'created_at' => $upgradeRequest->created_at,
                'user'       => [
                    'id'            => $upgradeRequest->admin->id,
                    'name'          => $upgradeRequest->admin->name,
                    'mobile_number' => $upgradeRequest->admin->getFullPhoneNumber(),
                    'email'         => $upgradeRequest->admin->email,
                    'type'          => $upgradeRequest->admin->type,
                    'type_label'    => $this->getTypeLabel($upgradeRequest->admin->type),
                ],
            ];
        }));

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('upgrade_requests_retrieved_successfully'),
            'data'    => $requests,
        ]);
    }

    /**
     * Approve an upgrade request
     *
     * Farm Admin only. Approves a pending Farm Owner request and upgrades that Farm Owner to Farm Admin.
     *
     * @authenticated
     * @urlParam upgradeRequest integer required Upgrade request ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Upgrade request approved successfully."
     * }
     * @response 404 {
     *   "message": "No query results for model [App\\Models\\UpgradeRequest] 99"
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "This upgrade request is no longer pending."
     * }
     */
    public function approveUpgradeRequest(Request $request, UpgradeRequest $upgradeRequest)
    {
        DB::beginTransaction();

        try {
            // Lock the request so it cannot be approved twice at the same time
            $pendingRequest = UpgradeRequest::whereKey($upgradeRequest->id)->lockForUpdate()->first();

            if ($pendingRequest->status !== UpgradeRequest::STATUS_PENDING) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $this->translationService->get('upgrade_request_not_pending'),
                ], 422);
            }

            $farmOwner = Admin::whereKey($pendingRequest->admin_id)->lockForUpdate()->first();

            // Only a Farm Owner can be upgraded to Farm Admin
            if (!$farmOwner || (int) $farmOwner->type !== Admin::PUBLIC_VENDOR) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $this->translationService->get('upgrade_request_invalid'),
                ], 422);
            }

            $adminRole = Role::where('name', 'Admin')->firstOrFail();

            $farmOwner->update(['type' => Admin::ADMIN]);
            $farmOwner->syncRoles([$adminRole->id]);

            $pendingRequest->update([
                'status'      => UpgradeRequest::STATUS_APPROVED,
                'approved_by' => $request->user()->id,
                'approved_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('upgrade_request_approved_successfully'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add2Farm upgrade approval error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('role_change_failed'),
            ], 500);
        }
    }

    /**
     * Create a Farm Admin upgrade request for a Farm Owner.
     * Only one pending request is allowed per user.
     */
    private function submitFarmAdminUpgradeRequest(Admin $user)
    {
        DB::beginTransaction();

        try {
            // Lock the user row so two simultaneous requests cannot both pass the duplicate check
            Admin::whereKey($user->id)->lockForUpdate()->first();

            $pendingExists = UpgradeRequest::where('admin_id', $user->id)
                ->where('status', UpgradeRequest::STATUS_PENDING)
                ->exists();

            if ($pendingExists) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $this->translationService->get('upgrade_request_already_pending'),
                ], 409);
            }

            $upgradeRequest = UpgradeRequest::create([
                'admin_id' => $user->id,
                'status'   => UpgradeRequest::STATUS_PENDING,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('role_upgrade_request_submitted'),
                'data'    => [
                    'id'         => $upgradeRequest->id,
                    'status'     => $upgradeRequest->status,
                    'created_at' => $upgradeRequest->created_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Add2Farm upgrade request error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('role_change_failed'),
            ], 500);
        }
    }

    /**
     * Change the user's type and sync the matching Spatie role.
     */
    private function changeRole(Admin $user, int $newType, string $roleName, string $messageKey = 'role_upgraded_successfully')
    {
        try {
            DB::transaction(function () use ($user, $newType, $roleName) {
                $role = Role::where('name', $roleName)->firstOrFail();

                $user->update(['type' => $newType]);
                $user->syncRoles([$role->id]);
            });

            return response()->json([
                'success'    => true,
                'message'    => $this->translationService->get($messageKey),
                'type'       => $newType,
                'type_label' => $this->getTypeLabel($newType),
            ]);

        } catch (\Exception $e) {
            \Log::error('Add2Farm role change error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('role_change_failed'),
            ], 500);
        }
    }

    private function notAllowed()
    {
        return response()->json([
            'success' => false,
            'message' => $this->translationService->get('role_change_not_allowed'),
        ], 403);
    }
}
