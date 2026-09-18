<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminProjectStatus;
use App\Helpers\ProjectHelper;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class SetupController extends Controller
{
    /**
     * Fix permissions and roles for type 3 and 4 users
     *
     * This is a one-time setup endpoint to ensure:
     * 1. All type 3 (PrivateVendor/Supervisor) and type 4 (User/Farmer) users have Add2Farm project assignments
     * 2. Type 4 users have the correct "User" role
     * 3. Type 3 users have the correct "PrivateVendor" role
     *
     * @authenticated
     * @return \Illuminate\Http\JsonResponse
     */
    public function fixPermissions(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();

        // Only SuperAdmin can run this
        if ($user->role !== 'SuperAdmin') {
            return response()->json(['success' => false, 'message' => 'Only SuperAdmin can run setup'], 403);
        }

        $fixes = [
            'adminProjectStatus' => $this->fixAdminProjectStatus(),
            'userRoles' => $this->fixUserRoles(),
            'supervisorRoles' => $this->fixSupervisorRoles(),
        ];

        $summary = [
            'success' => true,
            'message' => 'Setup completed successfully',
            'fixes' => $fixes,
            'timestamp' => now(),
        ];

        return response()->json($summary);
    }

    /**
     * Ensure all type 3 and 4 users have Add2Farm project assignment
     */
    private function fixAdminProjectStatus()
    {
        $result = [
            'created' => 0,
            'skipped' => 0,
            'errors' => [],
            'details' => [],
        ];

        // Get Add2Farm project ID using ProjectHelper
        $projectId = ProjectHelper::getAdd2FarmProjectId();
        if (!$projectId) {
            $result['errors'][] = 'Add2Farm project not found';
            return $result;
        }

        // Type 3 (PrivateVendor/Supervisor) and Type 4 (User/Farmer) users
        $users = Admin::whereIn('type', [3, 4])->get();

        foreach ($users as $admin) {
            try {
                $existing = AdminProjectStatus::where('admin_id', $admin->id)
                    ->where('project_id', $projectId)
                    ->first();

                if ($existing) {
                    $result['skipped']++;
                    $result['details'][] = "User {$admin->id} ({$admin->name}) already has project assignment";
                } else {
                    AdminProjectStatus::create([
                        'admin_id' => $admin->id,
                        'project_id' => $projectId,
                        'status' => 'Active',
                    ]);
                    $result['created']++;
                    $result['details'][] = "User {$admin->id} ({$admin->name}) - project assignment created";
                }
            } catch (\Exception $e) {
                $result['errors'][] = "Error for user {$admin->id}: {$e->getMessage()}";
            }
        }

        return $result;
    }

    /**
     * Ensure all type 4 users (Farmers) have the "User" role
     */
    private function fixUserRoles()
    {
        $result = [
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
            'details' => [],
        ];

        // Get the User role
        $userRole = Role::where('name', 'User')->first();
        if (!$userRole) {
            $result['errors'][] = 'User role not found. Please ensure roles are created.';
            return $result;
        }

        // Type 4 (User/Farmer) users
        $farmers = Admin::where('type', 4)->get();

        foreach ($farmers as $farmer) {
            try {
                // Check current role
                $currentRole = $farmer->roles->first()?->name;

                if ($currentRole === 'User') {
                    $result['skipped']++;
                    $result['details'][] = "Farmer {$farmer->id} ({$farmer->name}) already has User role";
                } else {
                    // Remove old roles and assign User role
                    $farmer->syncRoles([$userRole->id]);
                    $result['updated']++;
                    $result['details'][] = "Farmer {$farmer->id} ({$farmer->name}) - role updated to User (was: {$currentRole})";
                }
            } catch (\Exception $e) {
                $result['errors'][] = "Error for farmer {$farmer->id}: {$e->getMessage()}";
            }
        }

        return $result;
    }

    /**
     * Ensure all type 3 users (Supervisors) have the "PrivateVendor" role
     */
    private function fixSupervisorRoles()
    {
        $result = [
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
            'details' => [],
        ];

        // Get the PrivateVendor role
        $supervisorRole = Role::where('name', 'PrivateVendor')->first();
        if (!$supervisorRole) {
            $result['errors'][] = 'PrivateVendor role not found. Please ensure roles are created.';
            return $result;
        }

        // Type 3 (PrivateVendor/Supervisor) users
        $supervisors = Admin::where('type', 3)->get();

        foreach ($supervisors as $supervisor) {
            try {
                // Check current role
                $currentRole = $supervisor->roles->first()?->name;

                if ($currentRole === 'PrivateVendor') {
                    $result['skipped']++;
                    $result['details'][] = "Supervisor {$supervisor->id} ({$supervisor->name}) already has PrivateVendor role";
                } else {
                    // Remove old roles and assign PrivateVendor role
                    $supervisor->syncRoles([$supervisorRole->id]);
                    $result['updated']++;
                    $result['details'][] = "Supervisor {$supervisor->id} ({$supervisor->name}) - role updated to PrivateVendor (was: {$currentRole})";
                }
            } catch (\Exception $e) {
                $result['errors'][] = "Error for supervisor {$supervisor->id}: {$e->getMessage()}";
            }
        }

        return $result;
    }
}
