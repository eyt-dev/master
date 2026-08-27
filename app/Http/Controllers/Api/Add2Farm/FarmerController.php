<?php

namespace App\Http\Controllers\Api\Add2Farm;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        // Get all farmers for counting
        $allFarmers = Admin::where('type', self::ADMIN_TYPE)
            ->where('created_by', auth()->id())
            ->get();

        // Count total, active (with farm), and inactive (without farm)
        $totalFarmers = $allFarmers->count();
        $activeFarmers = 0;
        $inactiveFarmers = 0;

        foreach ($allFarmers as $farmer) {
            $farm = \App\Models\Farm::where('assigned_to', $farmer->id)->first();
            if ($farm) {
                $activeFarmers++;
            } else {
                $inactiveFarmers++;
            }
        }

        $farmers = Admin::where('type', self::ADMIN_TYPE)
            ->where('created_by', auth()->id())
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
            'total_farmers' => $totalFarmers,
            'active' => $activeFarmers,
            'inactive' => $inactiveFarmers,
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
            ->where('created_by', auth()->id())
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
     * Default password: "Password123" (will be set automatically, no need to provide)
     *
     * @authenticated
     * @bodyParam name string required Farmer's full name. Example: John Farmer
     * @bodyParam phone_code string required Phone country code. Example: +1
     * @bodyParam mobile_number string required Unique mobile number (without country code). Example: 1987654321
     * @bodyParam email string optional Email address. Example: john@example.com
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
            'phone_code'      => 'required|string|max:10',
            'mobile_number'   => 'required|string|max:20|unique:admins,mobile_number',
            'email'           => 'nullable|email|max:255|unique:admins,email',
            'notes'           => 'nullable|string|max:1000',
            'image'           => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
            'farm_id'         => 'nullable|integer|exists:farms,id',
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
                $uploadDir = 'uploads/farmers';
                if (!Storage::disk('public')->exists($uploadDir)) {
                    Storage::disk('public')->makeDirectory($uploadDir, 0755, true);
                }
                $file->storeAs($uploadDir, $filename, 'public');
                $imagePath = $uploadDir . '/' . $filename;
            }

            // Create farmer (Type 4)
            $admin = Admin::create([
                'name'           => $request->name,
                'phone_code'     => $request->phone_code,
                'mobile_number'  => $request->mobile_number,
                'email'          => $request->email ?? 'farmer-' . uniqid() . '@add2farm.local',
                'password'       => Hash::make('Password123'),
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

            // Auto-assign to Add2Farm project with Active status
            $add2FarmProjectId = \App\Helpers\ProjectHelper::getAdd2FarmProjectId();
            if ($add2FarmProjectId) {
                AdminProjectStatus::create([
                    'admin_id'   => $admin->id,
                    'project_id' => $add2FarmProjectId,
                    'status'     => 'Active',
                ]);
            }

            // Assign farmer to farm if farm_id provided
            if ($request->filled('farm_id')) {
                \App\Models\Farm::findOrFail($request->farm_id)->update([
                    'assigned_to' => $admin->id,
                ]);
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
     * Update farmer details including name, email, status and project assignments.
     * Type cannot be changed. Password is not updatable.
     *
     * @authenticated
     * @urlParam id integer required The farmer ID. Example: 2
     * @bodyParam name string required Farmer's full name. Example: Jane Farmer Updated
     * @bodyParam phone_code string optional Phone country code. Example: +1
     * @bodyParam mobile_number string optional Mobile number (without country code). Example: 1987654321
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
        $admin = Admin::where('type', self::ADMIN_TYPE)
            ->where('created_by', auth()->id())
            ->find($id);

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => $this->translationService->get('farmer_not_found'),
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'    => 'sometimes|required|string|max:255',
            'phone_code' => 'nullable|string|max:10',
            'mobile_number' => 'nullable|string|max:20|unique:admins,mobile_number,' . $admin->id,
            'email'   => 'nullable|email|max:255|unique:admins,email,' . $admin->id,
            'status'  => 'sometimes|required|in:Active,Inactive,Disable',
            'notes'   => 'nullable|string|max:1000',
            'image'   => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
            'farm_id' => 'nullable|integer|exists:farms,id',
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

            // Build update data with only provided fields
            $updateData = [];

            // Add name if provided
            if ($request->filled('name')) {
                $updateData['name'] = $request->name;
            }

            // Add phone_code if provided
            if ($request->filled('phone_code')) {
                $updateData['phone_code'] = $request->phone_code;
            }

            // Add mobile_number if provided
            if ($request->filled('mobile_number')) {
                $updateData['mobile_number'] = $request->mobile_number;
            }

            // Add email if provided
            if ($request->filled('email')) {
                $updateData['email'] = $request->email;
            }

            // Add status if provided
            if ($request->filled('status')) {
                $updateData['status'] = $request->status;
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
                $uploadDir = 'uploads/farmers';
                if (!Storage::disk('public')->exists($uploadDir)) {
                    Storage::disk('public')->makeDirectory($uploadDir, 0755, true);
                }
                $file->storeAs($uploadDir, $filename, 'public');
                $updateData['image'] = $uploadDir . '/' . $filename;
            }

            // Update farmer details (excluding type and mobile_number)
            $admin->update($updateData);

            // Handle farm assignment
            if ($request->filled('farm_id')) {
                \App\Models\Farm::findOrFail($request->farm_id)->update([
                    'assigned_to' => $admin->id,
                ]);
            }

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
        $admin = Admin::where('type', self::ADMIN_TYPE)
            ->where('created_by', auth()->id())
            ->find($id);

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
        $imageUrl = null;
        if ($admin->image) {
            $imageUrl = route('api.files', ['path' => $admin->image]);
        }

        // Load assigned farm if exists
        $farm = \App\Models\Farm::where('assigned_to', $admin->id)->first();

        // Status is Active if farm assigned, otherwise Inactive
        $displayStatus = $farm ? 'Active' : 'Inactive';

        return [
            'id'            => $admin->id,
            'name'          => $admin->name,
            'mobile_number' => $admin->getFullPhoneNumber(),
            'email'         => $admin->email,
            'type'          => $admin->type,
            'type_label'    => $this->getTypeLabel($admin->type),
            'status'        => $displayStatus,
            'status_label'  => $this->getStatusLabel($displayStatus),
            'assignment'    => $admin->hasAssignment(),
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
