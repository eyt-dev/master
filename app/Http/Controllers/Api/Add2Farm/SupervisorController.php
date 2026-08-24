<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Models\Admin;
use App\Models\AdminProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use App\Helpers\ProjectHelper;

/**
 * @group Add2Farm Supervisors (Type 3)
 * CRUD APIs for managing Type 3 (Supervisors) in Add2Farm
 */
class SupervisorController extends BaseController
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
            'message' => $this->translationService->get('supervisors_retrieved_successfully'),
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
            ->with('creator')
            ->find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('supervisor_not_found'),
            ], 404);
        }

        $data = $this->formatAdmin($admin);

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('supervisor_retrieved_successfully'),
            'data' => $data,
        ]);
    }

    /**
     * Create a new supervisor
     *
     * Create a new Type 3 (Supervisor) admin account.
     * Type is automatically set to 3 and cannot be changed.
     * Supervisor is automatically assigned to Add2Farm project.
     *
     * @authenticated
     * @bodyParam name string required Supervisor's full name. Example: John Supervisor
     * @bodyParam mobile_number string required Unique mobile number with country code. Example: +1234567890
     * @bodyParam email string optional Email address. Example: john@example.com
     * @bodyParam password string required Password (min 8 characters). Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Example: password123
     * @bodyParam notes string optional Optional notes about the supervisor. Example: Senior supervisor with 10 years experience
     * @bodyParam image file optional Profile image (jpeg, png, gif). Max 2MB. Example: (binary)
     * @bodyParam farm_id integer optional Farm ID to assign this supervisor to. Example: 1
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
     *     "notes": "Senior supervisor with 10 years experience",
     *     "image": "supervisor_image_123.jpg",
     *     "farm_id": 1,
     *     "farm_name": "Main Farm",
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
            'notes'           => 'nullable|string|max:1000',
            'image'           => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'farm_id'         => 'nullable|integer|exists:farms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Ensure directory exists
                $uploadDir = 'uploads/supervisors';
                if (!Storage::disk('public')->exists($uploadDir)) {
                    Storage::disk('public')->makeDirectory($uploadDir, 0755, true);
                }
                $file->storeAs($uploadDir, $filename, 'public');
                $imagePath = $uploadDir . '/' . $filename;
            }

            // Create supervisor (Type 3)
            $admin = Admin::create([
                'name'           => $request->name,
                'mobile_number'  => $request->mobile_number,
                'email'          => $request->email ?? 'supervisor-' . uniqid() . '@add2farm.local',
                'password'       => Hash::make($request->password),
                'type'           => self::ADMIN_TYPE,
                'status'         => 'Active',
                'notes'          => $request->notes ?? null,
                'image'          => $imagePath,
                'created_by'     => auth()->id(),
                'created_from'   => 3,
            ]);

            // Assign Supervisor role if exists
            $role = Role::where('name', 'Supervisor')->first();
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

            // Assign supervisor to farm if farm_id provided
            if ($request->filled('farm_id')) {
                \App\Models\Farm::findOrFail($request->farm_id)->update([
                    'assigned_to' => $admin->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('supervisor_created_successfully'),
                'data'    => $this->formatAdmin($admin),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Supervisor creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('operation_failed'),
            ], 500);
        }
    }

    /**
     * Update a supervisor
     *
     * Update supervisor details including name, email, status and project assignments.
     * Mobile number and type cannot be changed.
     *
     * @authenticated
     * @urlParam id integer required The supervisor ID. Example: 1
     * @bodyParam name string required Supervisor's full name. Example: John Supervisor Updated
     * @bodyParam email string optional Email address. Example: john.updated@example.com
     * @bodyParam status string required Status (Active, Inactive, Disable). Example: Active
     * @bodyParam notes string optional Optional notes about the supervisor. Example: Updated notes
     * @bodyParam image file optional Profile image (jpeg, png, gif). Max 2MB. Example: (binary)
     * @bodyParam project_rows array optional Array of project assignments. Example: [{"project_id": 1, "status": "Active"}]
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Supervisor updated successfully.",
     *   "data": {
     *     "id": 1,
     *     "name": "John Supervisor Updated",
     *     "mobile_number": "+1234567890",
     *     "email": "john.updated@example.com",
     *     "type": 3,
     *     "type_label": "Supervisor",
     *     "status": "Active",
     *     "notes": "Updated notes",
     *     "image": "supervisor_image_456.jpg",
     *     "created_by_name": "Admin Name",
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Supervisor not found."
     * }
     * @response 422 {
     *   "success": false,
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::where('type', self::ADMIN_TYPE)->find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('supervisor_not_found'),
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'mobile_number'   => 'nullable|string|max:20|unique:admins,mobile_number,' . $admin->id,
            'email'           => 'nullable|email|max:255|unique:admins,email,' . $admin->id,
            'status'          => 'required|in:Active,Inactive,Disable',
            'notes'           => 'nullable|string|max:1000',
            'image'           => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'farm_id'         => 'nullable|integer|exists:farms,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Handle image upload
            $updateData = [
                'name'   => $request->name,
                'email'  => $request->email ?? $admin->email,
                'status' => $request->status,
            ];

            // Add mobile_number if provided
            if ($request->filled('mobile_number')) {
                $updateData['mobile_number'] = $request->mobile_number;
            }

            // Add notes if provided
            if ($request->filled('notes')) {
                $updateData['notes'] = $request->notes;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($admin->image && Storage::disk('public')->exists($admin->image)) {
                    Storage::disk('public')->delete($admin->image);
                }

                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                // Ensure directory exists
                $uploadDir = 'uploads/supervisors';
                if (!Storage::disk('public')->exists($uploadDir)) {
                    Storage::disk('public')->makeDirectory($uploadDir, 0755, true);
                }
                $file->storeAs($uploadDir, $filename, 'public');
                $updateData['image'] = $uploadDir . '/' . $filename;
            }

            // Update supervisor details (excluding type and mobile_number)
            $admin->update($updateData);

            // Assign supervisor to farm if farm_id provided
            if ($request->filled('farm_id')) {
                \App\Models\Farm::findOrFail($request->farm_id)->update([
                    'assigned_to' => $admin->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $this->translationService->get('supervisor_updated_successfully'),
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
                'message' => $this->translationService->get('supervisor_not_found'),
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
                'message' => $this->translationService->get('supervisor_deleted_successfully'),
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
        $imageUrl = null;
        if ($admin->image) {
            $imageUrl = route('api.files', ['path' => $admin->image]);
        }

        // Load assigned farm if exists
        $farm = \App\Models\Farm::where('assigned_to', $admin->id)->first();

        return [
            'id'            => $admin->id,
            'name'          => $admin->name,
            'mobile_number' => $admin->mobile_number,
            'email'         => $admin->email,
            'type'          => $admin->type,
            'type_label'    => $this->getTypeLabel($admin->type),
            'status'        => $admin->status,
            'status_label'  => $this->getStatusLabel($admin->status),
            'notes'         => $admin->notes ?? null,
            'image'         => $admin->image ?? null,
            'image_url'     => $imageUrl,
            'farm_id'       => $farm?->id ?? null,
            'farm_name'     => $farm?->name ?? null,
            'created_by_name' => $admin->creator?->name ?? null,
            'created_at'    => $admin->created_at,
        ];
    }
}
