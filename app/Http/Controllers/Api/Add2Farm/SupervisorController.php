<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

/**
 * @group Add2Farm Supervisors (Type 3)
 * CRUD APIs for managing Type 3 (Supervisors) in Add2Farm
 */
class SupervisorController extends Controller
{
    const ADMIN_TYPE = 3;

    /**
     * List all supervisors (Type 3)
     *
     * Get paginated list of all supervisors with search and filtering.
     *
     * @authenticated
     * @queryParam page integer optional Pagination page number. Example: 1
     * @queryParam per_page integer optional Items per page. Default: 15. Example: 20
     * @queryParam search string optional Search by name or mobile_number. Example: John
     * @queryParam status string optional Filter by status (Active, Inactive, Disable). Example: Active
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Supervisors retrieved successfully.",
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "name": "Supervisor Name",
     *         "mobile_number": "+1234567890",
     *         "email": "supervisor@add2farm.local",
     *         "type": 3,
     *         "type_label": "Supervisor",
     *         "status": "Active",
     *         "created_by_name": "Admin Name",
     *         "created_at": "2026-08-07T10:30:00Z"
     *       }
     *     ],
     *     "total": 10,
     *     "last_page": 1
     *   }
     * }
     */
    public function index(Request $request)
    {
        $supervisors = Admin::where('type', self::ADMIN_TYPE)
            ->when($request->search, function ($q) use ($request) {
                return $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('mobile_number', 'like', "%{$request->search}%");
            })
            ->when($request->status, function ($q) use ($request) {
                return $q->where('status', $request->status);
            })
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        $supervisors->setCollection($supervisors->getCollection()->map(function ($admin) {
            return $this->formatAdmin($admin);
        }));

        return response()->json([
            'success' => true,
            'message' => 'Supervisors retrieved successfully.',
            'data' => $supervisors,
        ]);
    }

    /**
     * Get a single supervisor
     *
     * Retrieve detailed information of a specific supervisor.
     *
     * @authenticated
     * @urlParam id integer required The supervisor ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Supervisor retrieved successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "Supervisor Name",
     *     "mobile_number": "+1234567890",
     *     "email": "supervisor@add2farm.local",
     *     "type": 3,
     *     "type_label": "Supervisor",
     *     "status": "Active",
     *     "created_by_name": "Admin Name",
     *     "project_statuses": [
     *       {
     *         "id": 1,
     *         "project_id": 1,
     *         "status": "Active"
     *       }
     *     ],
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Supervisor not found."
     * }
     */
    public function show($id)
    {
        $admin = Admin::where('type', self::ADMIN_TYPE)
            ->with('creator', 'projectStatuses')
            ->find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Supervisor not found.',
            ], 404);
        }

        $data = $this->formatAdmin($admin);
        $data['project_statuses'] = $admin->projectStatuses->map(function ($ps) {
            return [
                'id' => $ps->id,
                'project_id' => $ps->project_id,
                'status' => $ps->status,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Supervisor retrieved successfully.',
            'data' => $data,
        ]);
    }

    /**
     * Create a new supervisor
     *
     * Create a new Type 3 (Supervisor) admin account.
     * Type is automatically set to 3 and cannot be changed.
     *
     * @authenticated
     * @bodyParam name string required Supervisor's full name. Example: John Supervisor
     * @bodyParam mobile_number string required Unique mobile number with country code. Example: +1234567890
     * @bodyParam email string optional Email address. Example: john@example.com
     * @bodyParam password string required Password (min 8 characters). Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Example: password123
     * @bodyParam project_rows array optional Array of project assignments. Example: [{"project_id": 1, "status": "Active"}]
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Supervisor created successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "John Supervisor",
     *     "mobile_number": "+1234567890",
     *     "type": 3,
     *     "type_label": "Supervisor",
     *     "status": "Active",
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "mobile_number": ["The mobile number has already been taken."]
     *   }
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'mobile_number'   => 'required|string|max:20|unique:admins,mobile_number',
            'email'           => 'nullable|email|max:255|unique:admins,email',
            'password'        => 'required|string|min:8|confirmed',
            'project_rows'    => 'nullable|array',
            'project_rows.*.project_id' => 'nullable|exists:projects,id',
            'project_rows.*.status' => 'nullable|in:Active,Inactive,Pending',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Create supervisor (Type 3)
            $admin = Admin::create([
                'name'           => $request->name,
                'mobile_number'  => $request->mobile_number,
                'email'          => $request->email ?? 'supervisor-' . uniqid() . '@add2farm.local',
                'password'       => Hash::make($request->password),
                'type'           => self::ADMIN_TYPE,
                'status'         => 'Active',
                'created_by'     => auth()->id(),
                'created_from'   => 3,
            ]);

            // Assign Supervisor role if exists
            $role = Role::where('name', 'Supervisor')->first();
            if ($role) {
                $admin->assignRole($role);
            }

            // Create project status records if provided
            if ($request->filled('project_rows')) {
                $projectRows = array_filter($request->project_rows, function ($row) {
                    return !empty($row['project_id']);
                });

                foreach ($projectRows as $row) {
                    AdminProjectStatus::create([
                        'admin_id'   => $admin->id,
                        'project_id' => $row['project_id'],
                        'status'     => $row['status'] ?? 'Active',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supervisor created successfully.',
                'data'    => $this->formatAdmin($admin),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Supervisor creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create supervisor.',
            ], 500);
        }
    }

    /**
     * Update a supervisor
     *
     * Update supervisor information. Type cannot be changed.
     *
     * @authenticated
     * @urlParam id integer required The supervisor ID. Example: 1
     * @bodyParam name string required Supervisor's full name. Example: John Supervisor
     * @bodyParam email string optional Email address. Example: john@example.com
     * @bodyParam status string required Account status (Active, Inactive, Disable). Example: Active
     * @bodyParam project_rows array optional Array of project assignments. Example: [{"project_id": 1, "status": "Active"}]
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Supervisor updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "John Supervisor Updated",
     *     "mobile_number": "+1234567890",
     *     "type": 3,
     *     "type_label": "Supervisor",
     *     "status": "Active"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Supervisor not found."
     * }
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::where('type', self::ADMIN_TYPE)->find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Supervisor not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255|unique:admins,email,' . $admin->id,
            'status'  => 'required|in:Active,Inactive,Disable',
            'project_rows' => 'nullable|array',
            'project_rows.*.project_id' => 'nullable|exists:projects,id',
            'project_rows.*.status' => 'nullable|in:Active,Inactive,Pending',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Update supervisor details (excluding type and mobile_number)
            $admin->update([
                'name'   => $request->name,
                'email'  => $request->email ?? $admin->email,
                'status' => $request->status,
            ]);

            // Update project statuses if provided
            if ($request->filled('project_rows')) {
                // Delete existing project statuses
                $admin->projectStatuses()->delete();

                // Create new ones
                $projectRows = array_filter($request->project_rows, function ($row) {
                    return !empty($row['project_id']);
                });

                foreach ($projectRows as $row) {
                    AdminProjectStatus::create([
                        'admin_id'   => $admin->id,
                        'project_id' => $row['project_id'],
                        'status'     => $row['status'] ?? 'Active',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supervisor updated successfully.',
                'data'    => $this->formatAdmin($admin->fresh()),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Supervisor update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update supervisor.',
            ], 500);
        }
    }

    /**
     * Delete a supervisor
     *
     * Delete a supervisor and their project status records.
     *
     * @authenticated
     * @urlParam id integer required The supervisor ID. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Supervisor deleted successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Supervisor not found."
     * }
     */
    public function destroy($id)
    {
        $admin = Admin::where('type', self::ADMIN_TYPE)->find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Supervisor not found.',
            ], 404);
        }

        try {
            DB::beginTransaction();

            // Delete project status records
            $admin->projectStatuses()->delete();

            // Delete tokens
            $admin->tokens()->delete();

            // Delete the admin
            $admin->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Supervisor deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Supervisor deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete supervisor.',
            ], 500);
        }
    }

    /**
     * Format admin data for responses
     */
    private function formatAdmin(Admin $admin): array
    {
        $typeLabels = [
            1 => 'Farm Admin',
            2 => 'Farm Owner',
            3 => 'Supervisor',
            4 => 'Farmers',
        ];

        return [
            'id'            => $admin->id,
            'name'          => $admin->name,
            'mobile_number' => $admin->mobile_number,
            'email'         => $admin->email,
            'type'          => $admin->type,
            'type_label'    => $typeLabels[$admin->type] ?? 'Unknown',
            'status'        => $admin->status,
            'created_by_name' => $admin->creator?->name ?? null,
            'created_at'    => $admin->created_at,
        ];
    }
}
