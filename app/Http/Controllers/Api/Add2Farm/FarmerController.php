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
 * @group Add2Farm Farmers (Type 4)
 * CRUD APIs for managing Type 4 (Farmers) in Add2Farm
 */
class FarmerController extends BaseController
{
    const ADMIN_TYPE = 4;

    /**
     * List all farmers (Type 4)
     *
     * Get paginated list of all farmers with search and filtering.
     *
     * @authenticated
     * @queryParam page integer optional Pagination page number. Example: 1
     * @queryParam per_page integer optional Items per page. Default: 15. Example: 20
     * @queryParam search string optional Search by name or mobile_number. Example: John
     * @queryParam status string optional Filter by status (Active, Inactive, Disable). Example: Active
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farmers retrieved successfully.",
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 2,
     *         "name": "Farmer Name",
     *         "mobile_number": "+1987654321",
     *         "email": "farmer@add2farm.local",
     *         "type": 4,
     *         "type_label": "Farmers",
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
        $farmers = Admin::where('type', self::ADMIN_TYPE)
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

        $farmers->setCollection($farmers->getCollection()->map(function ($admin) {
            return $this->formatAdmin($admin);
        }));

        return response()->json([
            'success' => true,
            'message' => $this->translationService->get('farmers_retrieved_successfully'),
            'data' => $farmers,
        ]);
    }

    /**
     * Get a single farmer
     *
     * Retrieve detailed information of a specific farmer.
     *
     * @authenticated
     * @urlParam id integer required The farmer ID. Example: 2
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farmer retrieved successfully.",
     *   "data": {
     *     "id": 2,
     *     "name": "Farmer Name",
     *     "mobile_number": "+1987654321",
     *     "email": "farmer@add2farm.local",
     *     "type": 4,
     *     "type_label": "Farmers",
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
     *   "message": "Farmer not found."
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
                'message' => $this->translationService->get('farmer_not_found'),
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
            'message' => $this->translationService->get('farmer_retrieved_successfully'),
            'data' => $data,
        ]);
    }

    /**
     * Create a new farmer
     *
     * Create a new Type 4 (Farmer) admin account.
     * Type is automatically set to 4 and cannot be changed.
     * Type 2 (Farm Owner) can only assign 1 project per farmer.
     *
     * @authenticated
     * @bodyParam name string required Farmer's full name. Example: John Farmer
     * @bodyParam mobile_number string required Unique mobile number with country code. Example: +1987654321
     * @bodyParam email string optional Email address. Example: john@example.com
     * @bodyParam password string required Password (min 8 characters). Example: password123
     * @bodyParam password_confirmation string required Password confirmation. Example: password123
     * @bodyParam notes string optional Optional notes about the farmer. Example: Experienced farmer with 5 years background
     * @bodyParam image file optional Profile image (jpeg, png, gif). Max 2MB. Example: (binary)
     * @bodyParam project_rows array optional Array of project assignments. Type 2 can assign max 1. Example: [{"project_id": 1, "status": "Active"}]
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Farmer created successfully.",
     *   "data": {
     *     "id": 2,
     *     "name": "John Farmer",
     *     "mobile_number": "+1987654321",
     *     "type": 4,
     *     "type_label": "Farmers",
     *     "status": "Active",
     *     "notes": "Experienced farmer with 5 years background",
     *     "image": "farmer_image_123.jpg",
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

        // Type 2 (Farm Owner) can only assign 1 project per farmer
        $user = auth()->user();
        if ($user->type == 2 && $request->filled('project_rows')) {
            $projectRows = array_filter($request->project_rows, function ($row) {
                return !empty($row['project_id']);
            });
            if (count($projectRows) > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Farm Owner can only assign 1 project per farmer.',
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Ensure directory exists
                \Storage::disk('public')->makeDirectory('uploads/farmers', 0755, true);
                $file->storeAs('uploads/farmers', $filename, 'public');
                $imagePath = 'uploads/farmers/' . $filename;
            }

            // Create farmer (Type 4)
            $admin = Admin::create([
                'name'           => $request->name,
                'mobile_number'  => $request->mobile_number,
                'email'          => $request->email ?? 'farmer-' . uniqid() . '@add2farm.local',
                'password'       => Hash::make($request->password),
                'type'           => self::ADMIN_TYPE,
                'status'         => 'Active',
                'notes'          => $request->notes ?? null,
                'image'          => $imagePath,
                'created_by'     => auth()->id(),
                'created_from'   => 3,
            ]);

            // Assign Farmer role if exists
            $role = Role::where('name', 'Farmer')->first();
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
                'message' => $this->translationService->get('farmer_created_successfully'),
                'data'    => $this->formatAdmin($admin),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Farmer creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create farmer.',
            ], 500);
        }
    }

    /**
     * Update a farmer
     *
     * Update farmer information. Type cannot be changed.
     * Type 2 (Farm Owner) can only assign 1 project per farmer.
     *
     * @authenticated
     * @urlParam id integer required The farmer ID. Example: 2
     * @bodyParam name string required Farmer's full name. Example: John Farmer
     * @bodyParam email string optional Email address. Example: john@example.com
     * @bodyParam status string required Account status (Active, Inactive, Disable). Example: Active
     * @bodyParam project_rows array optional Array of project assignments. Type 2 can assign max 1. Example: [{"project_id": 1, "status": "Active"}]
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farmer updated successfully.",
     *   "data": {
     *     "id": 2,
     *     "name": "John Farmer Updated",
     *     "mobile_number": "+1987654321",
     *     "type": 4,
     *     "type_label": "Farmers",
     *     "status": "Active"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Farmer not found."
     * }
     */
    /**
     * Update a farmer
     *
     * Update farmer details including name, email, status and project assignments.
     * Mobile number and type cannot be changed.
     *
     * @authenticated
     * @urlParam id integer required The farmer ID. Example: 2
     * @bodyParam name string required Farmer's full name. Example: Jane Farmer Updated
     * @bodyParam email string optional Email address. Example: jane.updated@example.com
     * @bodyParam status string required Status (Active, Inactive, Disable). Example: Active
     * @bodyParam notes string optional Optional notes about the farmer. Example: Updated notes
     * @bodyParam image file optional Profile image (jpeg, png, gif). Max 2MB. Example: (binary)
     * @bodyParam project_rows array optional Array of project assignments. Example: [{"project_id": 1, "status": "Active"}]
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farmer updated successfully.",
     *   "data": {
     *     "id": 2,
     *     "name": "Jane Farmer Updated",
     *     "mobile_number": "+0987654321",
     *     "email": "jane.updated@example.com",
     *     "type": 4,
     *     "type_label": "Farmer",
     *     "status": "Active",
     *     "notes": "Updated notes",
     *     "image": "farmer_image_456.jpg",
     *     "created_by_name": "Farm Owner Name",
     *     "created_at": "2026-08-07T10:30:00Z"
     *   }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Farmer not found."
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
                'message' => $this->translationService->get('farmer_not_found'),
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255|unique:admins,email,' . $admin->id,
            'status'  => 'required|in:Active,Inactive,Disable',
            'notes'   => 'nullable|string|max:1000',
            'image'   => 'nullable|image|mimes:jpeg,png,gif|max:2048',
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

        // Type 2 (Farm Owner) can only assign 1 project per farmer
        $user = auth()->user();
        if ($user->type == 2 && $request->filled('project_rows')) {
            $projectRows = array_filter($request->project_rows, function ($row) {
                return !empty($row['project_id']);
            });
            if (count($projectRows) > 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Farm Owner can only assign 1 project per farmer.',
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Handle image upload
            $updateData = [
                'name'   => $request->name,
                'email'  => $request->email ?? $admin->email,
                'status' => $request->status,
            ];

            // Add notes if provided
            if ($request->filled('notes')) {
                $updateData['notes'] = $request->notes;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($admin->image && \Storage::disk('public')->exists($admin->image)) {
                    \Storage::disk('public')->delete($admin->image);
                }

                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Ensure directory exists
                \Storage::disk('public')->makeDirectory('uploads/farmers', 0755, true);
                $file->storeAs('uploads/farmers', $filename, 'public');
                $updateData['image'] = 'uploads/farmers/' . $filename;
            }

            // Update farmer details (excluding type and mobile_number)
            $admin->update($updateData);

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
                'message' => $this->translationService->get('farmer_updated_successfully'),
                'data'    => $this->formatAdmin($admin->fresh()),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Farmer update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update farmer.',
            ], 500);
        }
    }

    /**
     * Delete a farmer
     *
     * Delete a farmer and their project status records.
     *
     * @authenticated
     * @urlParam id integer required The farmer ID. Example: 2
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Farmer deleted successfully."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Farmer not found."
     * }
     */
    public function destroy($id)
    {
        $admin = Admin::where('type', self::ADMIN_TYPE)->find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('farmer_not_found'),
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
                'message' => $this->translationService->get('farmer_deleted_successfully'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Farmer deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete farmer.',
            ], 500);
        }
    }

    /**
     * Format admin data for responses
     */
    private function formatAdmin(Admin $admin): array
    {
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
            'created_by_name' => $admin->creator?->name ?? null,
            'created_at'    => $admin->created_at,
        ];
    }
}
