<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use Carbon\Carbon;
use App\Models\Module;
use App\Models\Setting;
use Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\CountryRegion;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use App\Models\Flock;
use App\Models\Farm;
use App\Models\Hangar;
use App\Models\DailyRecord;
use App\Models\Contact;
use App\Models\Project;
use App\Models\AdminProjectStatus;
use App\Models\TypeMapping;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return $this->handleAdminList(TypeMapping::PARENT_ADMIN);
        }
        $projects = $this->getAllProjects();
        $type = TypeMapping::PARENT_ADMIN;
        return view('backend.admins.index', compact('type', 'projects'));
    }

    public function publicVendor()
    {
        if (request()->ajax()) {
            return $this->handleAdminList(TypeMapping::PARENT_PUBLIC_VENDOR);
        }
        $projects = $this->getAllProjects();
        $type = TypeMapping::PARENT_PUBLIC_VENDOR;
        return view('backend.admins.index', compact('type', 'projects'));
    }

    public function privateVendor()
    {
        if (request()->ajax()) {
            return $this->handleAdminList(TypeMapping::PARENT_PRIVATE_VENDOR);
        }
        $projects = $this->getAllProjects();
        $type = TypeMapping::PARENT_PRIVATE_VENDOR;
        return view('backend.admins.index', compact('type', 'projects'));
    }

    private function handleAdminList($type)
    {
        if (request()->ajax()) {
            $admin = auth()->user();
            $query = Admin::query()->with(['creator', 'project', 'projectStatuses.project', 'typeMapping']);
            if ($admin->isSuperAdmin()) {
                // Super Admin can see all type of admins
                $query->ofParentType($type);
            } elseif ($admin->hasParentType(TypeMapping::PARENT_ADMIN)) {
                // Admin can only see Private Vendors they created
                if ($type == TypeMapping::PARENT_PRIVATE_VENDOR) {
                    $query->where(function ($q) use ($admin) {
                        $q->where('created_by', $admin->id)
                            ->orWhere('parent_id', $admin->id);
                    });
                }
            } else {
                abort(403, "Unauthorized access");
            }

            // Get all projects for dynamic columns
            $allProjects = $this->getAllProjects();

            return datatables()->of($query->select('*'))
                ->addColumn('type_name', function ($row) {
                    return $row->typeMapping?->type ?? 'Super Admin';
                })
                ->addColumn('mobile_with_code', function ($row) {
                    $phoneCode = $row->phone_code ?? '';
                    $mobileNumber = $row->mobile_number ?? '';

                    if ($phoneCode && $mobileNumber) {
                        // Normalize phone code to always have + prefix
                        $normalizedPhoneCode = $phoneCode;
                        if (!str_starts_with($phoneCode, '+')) {
                            $normalizedPhoneCode = '+' . $phoneCode;
                        }
                        return $normalizedPhoneCode . ' ' . $mobileNumber;
                    } elseif ($mobileNumber) {
                        return $mobileNumber;
                    } elseif ($phoneCode) {
                        // Normalize phone code to always have + prefix
                        if (!str_starts_with($phoneCode, '+')) {
                            return '+' . $phoneCode;
                        }
                        return $phoneCode;
                    }
                    return 'N/A';
                })
                ->addColumn('created_by_name', function ($row) {
                    return ucfirst($row->parent_id != null ? ($row->parent->username ?? 'N/A') :  ($row->creator->username ?? 'N/A'));
                })
                ->addColumn('project_statuses_json', function ($row) use ($allProjects) {
                    try {
                        $statuses = [];
                        foreach ($allProjects as $project) {
                            $projectStatus = $row->projectStatuses->firstWhere('project_id', $project->id);
                            $statuses[$project->id] = $projectStatus?->status ?? null;
                        }
                        $json = json_encode($statuses);
                        // Ensure it's valid JSON
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            \Log::warning('JSON encoding error: ' . json_last_error_msg(), ['statuses' => $statuses]);
                            return json_encode([]);
                        }
                        return $json;
                    } catch (\Exception $e) {
                        \Log::error('Error generating project_statuses_json: ' . $e->getMessage());
                        return json_encode([]);
                    }
                })
                ->addColumn('url', function ($row) {
                    $url = $row->url ?: ($row->typeMapping?->url ?? '');
                    return $url ? '<a href="' . e($url) . '" target="_blank" rel="noopener">' . e($url) . '</a>' : '';
                })
                ->addColumn('action', function ($row) use ($admin) {
                    $btn = '';

                    // Admin can edit & delete only Private Vendors they created
                    if (($row->created_by == $admin->id || $row->parent_id == $admin->id) || $admin->isSuperAdmin()) {
                        $btn .= $this->getActionButtons($row);
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'created_by_name', 'project_statuses_json', 'mobile_with_code', 'url'])
                ->addIndexColumn()
                ->with('projects', $allProjects)
                ->make(true);
        }

        return view('backend.admins.index', compact('type'));
    }

    private function getActionButtons($row)
    {
        return '
            <a class="edit-admin edit_form btn btn-icon btn-success mr-1 white" 
                data-path="' . route('admins.edit', ['username' => request()->get('username', request()->segment(1)), 'admin' => $row->id]) . '" 
                data-name="' . $row->name . '" 
                data-id=' . $row->id . ' title="Edit"> 
                <i class="fa fa-edit"></i> 
            </a>
            <a class="btn btn-icon btn-danger mr-1 white delete-admin" 
                data-id="' . $row->id . '" title="Delete"> 
                <i class="fa fa-trash-o"></i> 
            </a>';
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($siteUrl, $type = null)
    {
        $countries = Country::orderBy('name')->get();
        $projects = $this->getAllProjects();
        $parentType = in_array($type, TypeMapping::PARENT_TYPES, true) ? $type : TypeMapping::PARENT_ADMIN;
        return view('backend.admins.create', ['admin' => new Admin(), 'parentType' => $parentType, 'typeOptions' => $this->typeOptionsFor($parentType), 'countries' => $countries, 'projects' => $projects, 'vatCountryId' => null]);
    }

    /**
     * Type mappings (id + name) that belong to a parent category, for the Type select.
     */
    private function typeOptionsFor(string $parentType)
    {
        return TypeMapping::where('parent_type', $parentType)->orderBy('id')->get(['id', 'type']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'nullable|email|unique:admins',
                'password' => 'required|min:6',
                'mobile_number' => 'required|string|max:20|unique:admins,mobile_number',
                'vat_country_code' => 'required',
                'vat_number' => 'required|max:50',
                'phone_code' => 'nullable|string|max:10',
                'notes' => 'nullable|string|max:1000',
                'image' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
                'project_id' => 'nullable|exists:projects,id',
                'project_rows' => 'nullable|array',
                'project_rows.*.project_id' => 'nullable|exists:projects,id',
                'project_rows.*.status' => 'nullable|in:Active,Inactive,Pending',
                'type' => 'required|exists:type_mappings,id',
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $this->validateProjectRows($request);

        // $request->type is a type_mappings.id; its parent_type is the category
        $adminType = $request->type;
        $parentType = TypeMapping::findOrFail($adminType)->parent_type;

        $prefix = match ($parentType) {
            TypeMapping::PARENT_ADMIN => 'Admin',
            TypeMapping::PARENT_PUBLIC_VENDOR => 'PublicVendor',
            TypeMapping::PARENT_PRIVATE_VENDOR => 'PrivateVendor',
            TypeMapping::PARENT_USER => 'User',
            default => 'Admin',
        };

        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $uploadDir = $parentType == TypeMapping::PARENT_USER ? 'uploads/farmers' : 'uploads/admins';
                Storage::disk('public')->makeDirectory($uploadDir, 0755, true);
                $file->storeAs($uploadDir, $filename, 'public');
                $imagePath = $uploadDir . '/' . $filename;
            }
    
            $addData = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'type' => $adminType,
                'created_by' => auth()->user()->id,
                'parent_id' => auth()->user()->id,
                'created_from' => 1,
            ];

            // Add fields only for non-farmer types or when provided for farmers
            if ($parentType != TypeMapping::PARENT_USER) {
                $addData['vat_country_code'] = $request->vat_country_code;
                $addData['vat_number'] = $request->vat_number;
            }

            // Add optional fields for all types
            $addData['phone_code'] = $request->phone_code;
            $addData['mobile_number'] = $request->mobile_number;
            $addData['notes'] = $request->notes ?? null;
            $addData['image'] = $imagePath;

            if ($request->filled('project_id')) {
                $addData['project_id'] = $request->project_id;
            }

            $admin = Admin::create($addData);

            if ($request->filled('project_rows')) {
                $admin->syncProjectStatuses($this->normalizeProjectRows($request));
            }

            if(empty($admin)) {
                Session::flash('errorMSg', 'Something went wrong.');
                DB::rollBack();
                return redirect()->back();
            }

            $role = Role::where(['name' => $prefix])->first();
            if ($role) {
                $admin->assignRole([$role->id]);
            }

            // Only create settings for non-farmer types
            if ($parentType != TypeMapping::PARENT_USER) {
                $createData = [
                    'domain' => config('domains.main_domain') . "/" . $request->username,
                    'admin_domain' => config('domains.admin_subdomain') . "/" . $request->username,

                    'dark_logo' => 'dark-logo.png',
                    'light_logo' => 'light-logo.png',
                    'footer_logo' => 'footer-logo.png',
                    'favicon' => 'favicon.ico',

                    'primary_text_color' => '#000000',
                    'secondary_text_color' => '#666666',

                    'primary_button_background' => '#007bff',
                    'secondary_button_background' => '#6c757d',
                    'primary_button_text_color' => '#ffffff',
                    'secondary_button_text_color' => '#ffffff',

                    'created_by' => $admin->id,
                    'theme' => 2,
                ];
                Setting::create($createData);

                Contact::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'name' => $request->name,
                        'formal_name' => $request->name,
                        'vat_country_code' => $request->vat_country_code,
                        'vat_number' => $request->vat_number,
                        'created_by' => $admin->id,
                    ]
                );
            }

            DB::commit();

            Session::flash('successMsg', "$prefix inserted successfully.");
            
            // Determine redirect route based on admin type
            $routeName = match ($parentType) {
                TypeMapping::PARENT_PUBLIC_VENDOR => 'admins.publicVendor',
                TypeMapping::PARENT_PRIVATE_VENDOR => 'admins.privateVendor',
                TypeMapping::PARENT_USER => 'admins.users',
                default => 'admins.index',
            };

            if ($request->ajax()) {
                return response()->json([
                    'message' => "$prefix inserted successfully.",
                    'redirect_url' => route($routeName, ['username' => request()->get('username', request()->segment(1))])
                ]);
            }

            return redirect()->route($routeName, ['username' => request()->get('username', request()->segment(1))]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Admin creation error: ' . $e->getMessage());
            Session::flash('errorMsg', 'Failed to create ' . strtolower($prefix) . '.');

            if ($request->ajax()) {
                return response()->json(['message' => 'Failed to create ' . strtolower($prefix) . '.'], 500);
            }
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($siteUrl, $id)
    {
        $admin = Admin::findOrFail($id);
        $countries = Country::orderBy('name')->get();

       if(empty($admin)){
            return redirect()->route('admins.index', ['username' => request()->get('username', request()->segment(1))]);
        }

        $roles = Role::all();
        $projects = $this->getAllProjects();
        $parentType = $admin->parent_type ?? TypeMapping::PARENT_ADMIN;
        return view('backend.admins.create', ['admin' => $admin, 'roles' => $roles, 'parentType' => $parentType, 'typeOptions' => $this->typeOptionsFor($parentType), 'countries' => $countries, 'projects' => $projects, 'vatCountryId' => $this->resolveVatCountryId($admin->vat_country_code, $countries)]);
    }

    /**
     * Resolve the stored VAT country to a countries.id for the select.
     * Self-registration stores the 2-letter prefix (e.g. "IN") instead of the id.
     */
    private function resolveVatCountryId($vatCountry, $countries)
    {
        if (blank($vatCountry)) {
            return null;
        }

        if (is_numeric($vatCountry) && $countries->contains('id', (int) $vatCountry)) {
            return (int) $vatCountry;
        }

        return $countries->first(fn ($country) => strtoupper(substr($country->name, 0, 2)) === strtoupper($vatCountry))?->id;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $siteUrl, $id)
    {
        $admin = Admin::find($id);

        if(empty($admin)){
            if($request->ajax()) {
                return response()->json(['message' => 'Admin not found'], 404);
            }
            return redirect()->route('admins.index', ['username' => request()->get('username', request()->segment(1))]);
        }

        try {
            $validated = $request->validate([
                'name' => 'required',
                'email' => 'nullable|email|unique:admins,email,' . $id,
                'vat_country_code' => $admin->hasParentType(TypeMapping::PARENT_USER) ? 'nullable' : 'required',
                'vat_number' => $admin->hasParentType(TypeMapping::PARENT_USER) ? 'nullable|max:50' : 'required|max:50',
                'phone_code' => 'nullable|string|max:10',
                'password' => 'nullable|min:6',
                'mobile_number' => 'required|string|max:20|unique:admins,mobile_number,' . $id,
                'type' => ['nullable', Rule::exists('type_mappings', 'id')->where('parent_type', $admin->parent_type)],
                'notes' => 'nullable|string|max:1000',
                'image' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
                'project_id' => 'nullable|exists:projects,id',
                'project_rows' => 'nullable|array',
                'project_rows.*.project_id' => 'nullable|exists:projects,id',
                'project_rows.*.status' => 'nullable|in:Active,Inactive,Pending',
            ]);
        } catch (ValidationException $e) {
            if($request->ajax()) {
                return response()->json(['errors' => $e->errors()], 422);
            }
            throw $e;
        }

        $this->validateProjectRows($request);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->filled('password') ? Hash::make($request->password) : $admin->password,
            ];

            // Add fields for non-farmer types
            if (!$admin->hasParentType(TypeMapping::PARENT_USER)) {
                $updateData['username'] = $request->username;
                $updateData['vat_country_code'] = $request->vat_country_code;
                $updateData['vat_number'] = $request->vat_number;
            }

            // Add optional fields for all types
            $updateData['phone_code'] = $request->phone_code;
            $updateData['mobile_number'] = $request->mobile_number;
            $updateData['notes'] = $request->notes ?? $admin->notes;

            // Type can only move within the same parent category
            if ($request->filled('type')) {
                $updateData['type'] = $request->type;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($admin->image && Storage::disk('public')->exists($admin->image)) {
                    Storage::disk('public')->delete($admin->image);
                }

                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $uploadDir = $admin->hasParentType(TypeMapping::PARENT_USER) ? 'uploads/farmers' : 'uploads/admins';
                Storage::disk('public')->makeDirectory($uploadDir, 0755, true);
                $file->storeAs($uploadDir, $filename, 'public');
                $updateData['image'] = $uploadDir . '/' . $filename;
            }

            if ($request->filled('project_id')) {
                $updateData['project_id'] = $request->project_id;
            }

            // Update admin
            $admin->update($updateData);

            // Sync role based on admin type to ensure consistency
            $rolePrefix = match ($admin->parent_type) {
                TypeMapping::PARENT_PUBLIC_VENDOR => 'PublicVendor',
                TypeMapping::PARENT_PRIVATE_VENDOR => 'PrivateVendor',
                TypeMapping::PARENT_USER => 'Farmer',
                default => 'Admin',
            };
            $roleToAssign = Role::where('name', $rolePrefix)->first();
            if ($roleToAssign) {
                $admin->syncRoles([$roleToAssign->id]);
            }

            // Sync project statuses if provided
            if ($request->has('project_rows')) {
                $admin->syncProjectStatuses($this->normalizeProjectRows($request));
            }

            // Sync contact information for non-farmer types
            if (!$admin->hasParentType(TypeMapping::PARENT_USER)) {
                Contact::updateOrCreate(
                    ['email' => $request->email],
                    [
                        'name' => $request->name,
                        'formal_name' => $request->name,
                        'vat_country_code' => $request->vat_country_code,
                        'vat_number' => $request->vat_number,
                        'created_by' => $admin->id,
                    ]
                );
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Admin update error: ' . $e->getMessage());

            if($request->ajax()) {
                return response()->json(['message' => 'Failed to update admin. Please try again.'], 500);
            }
            return redirect()->route('admins.edit', ['username' => request()->segment(1), 'admin' => $id])
                ->with('error', 'Failed to update admin. Please try again.');
        }

        // Determine redirect route based on admin type
        $routeName = match ($admin->parent_type) {
            TypeMapping::PARENT_PUBLIC_VENDOR => 'admins.publicVendor',
            TypeMapping::PARENT_PRIVATE_VENDOR => 'admins.privateVendor',
            TypeMapping::PARENT_USER => 'admins.users',
            default => 'admins.index',
        };

        if($request->ajax()) {
            return response()->json([
                'message' => 'Admin updated successfully!',
                'redirect_url' => route($routeName, ['username' => request()->get('username', request()->segment(1))])
            ]);
        }

        return redirect()->route($routeName, ['username' => request()->get('username', request()->segment(1))]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $siteUrl, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json(['msg' => 'Admin not found'], 404);
        }

        $dependentRecords = [];

        $flocksCount = \App\Models\Flock::where('created_by', $admin->id)->count();
        if ($flocksCount > 0) {
            $dependentRecords['Flocks Created'] = $flocksCount;
        }

        $farmsCreatedCount = \App\Models\Farm::where('created_by', $admin->id)->count();
        if ($farmsCreatedCount > 0) {
            $dependentRecords['Farms Created'] = $farmsCreatedCount;
        }

        $farmsAssignedCount = \App\Models\Farm::where('assigned_to', $admin->id)->count();
        if ($farmsAssignedCount > 0) {
            $dependentRecords['Farms Assigned'] = $farmsAssignedCount;
        }

        $hangarsCount = \App\Models\Hangar::where('created_by', $admin->id)->count();
        if ($hangarsCount > 0) {
            $dependentRecords['Hangars Created'] = $hangarsCount;
        }

        $dailyRecordsCount = \App\Models\DailyRecord::where('created_by', $admin->id)->count();
        if ($dailyRecordsCount > 0) {
            $dependentRecords['Daily Records Created'] = $dailyRecordsCount;
        }

        if (!empty($dependentRecords)) {
            $message = 'Cannot delete admin "' . $admin->name . '". The following related data exists: ';
            $details = [];
            foreach ($dependentRecords as $type => $count) {
                $details[] = $count . ' ' . $type;
            }
            $message .= implode(', ', $details) . '. Please reassign or remove all related data before deleting this admin.';

            return response()->json([
                'msg' => $message,
                'error' => true,
                'dependentRecords' => $dependentRecords
            ], 422);
        }

        $adminDelete = $admin->delete();
        if ($adminDelete)
            return response()->json(['msg' => 'Deleted successfully!']);

        return response()->json(['msg' => 'Something went wrong, Please try again'], 500);
    }

    public function users(Request $request, $siteUrl)
    {
        if ($request->ajax()) {
            $admin = auth()->user();
            $query = Admin::query()->ofParentType(TypeMapping::PARENT_USER)->with(['creator', 'projectStatuses.project', 'farms', 'typeMapping']);

            // Super Admin sees all farmers, others see only created farmers
            if (!$admin->isSuperAdmin()) {
                $query->where(function ($q) use ($admin) {
                    $q->where('created_by', $admin->id)
                        ->orWhere('parent_id', $admin->id);
                });
            }

            // Get all projects for dynamic columns
            $allProjects = $this->getAllProjects();

            return datatables()->of($query->select('*'))
                ->addColumn('mobile_number', function($row) {
                    return $row->mobile_number ?? 'N/A';
                })
                ->addColumn('type_name', function ($row) {
                    return $row->typeMapping?->type ?? 'N/A';
                })
                ->addColumn('url', function ($row) {
                    $url = $row->url ?: ($row->typeMapping?->url ?? '');
                    return $url ? '<a href="' . e($url) . '" target="_blank" rel="noopener">' . e($url) . '</a>' : '';
                })
                ->addColumn('notes_preview', function($row) {
                    $notes = $row->notes ?? '';
                    return strlen($notes) > 50 ? substr($notes, 0, 50) . '...' : $notes;
                })
                ->addColumn('image_thumbnail', function($row) {
                    if ($row->image && Storage::disk('public')->exists($row->image)) {
                        $url = asset('storage/' . $row->image);
                        return '<img src="' . $url . '" alt="' . $row->name . '" style="height: 40px; width: 40px; border-radius: 50%; object-fit: cover;">';
                    }
                    return '<div style="height: 40px; width: 40px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center;"><i class="fa fa-user"></i></div>';
                })
                ->addColumn('assigned_farms', function($row) {
                    $farms = $row->farms->pluck('name')->toArray();
                    if (empty($farms)) {
                        return '<span class="text-muted">Not assigned</span>';
                    }
                    return implode(', ', $farms);
                })
                ->addColumn('project_statuses_json', function ($row) use ($allProjects) {
                    try {
                        $statuses = [];
                        foreach ($allProjects as $project) {
                            $projectStatus = $row->projectStatuses->firstWhere('project_id', $project->id);
                            $statuses[$project->id] = $projectStatus?->status ?? null;
                        }
                        $json = json_encode($statuses);
                        // Ensure it's valid JSON
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            \Log::warning('JSON encoding error: ' . json_last_error_msg(), ['statuses' => $statuses]);
                            return json_encode([]);
                        }
                        return $json;
                    } catch (\Exception $e) {
                        \Log::error('Error generating project_statuses_json: ' . $e->getMessage());
                        return json_encode([]);
                    }
                })
                ->addColumn('status', function($row) {
                    $statusClass = [
                        'Active' => 'badge-success',
                        'Inactive' => 'badge-danger',
                        'Disable' => 'badge-secondary'
                    ][$row->status] ?? 'badge-secondary';
                    return '<span class="badge ' . $statusClass . '">' . $row->status . '</span>';
                })
                ->addColumn('created_by_name', function ($row) {
                    return $row->creator ? $row->creator->name : ($row->parent ? $row->parent->name : 'N/A');
                })
                ->addColumn('action', function ($row) use ($admin) {
                    $btn = '';

                    // Allow edit/delete if user created the farmer or is super admin
                    if ($row->created_by == $admin->id || $admin->isSuperAdmin()) {
                        $btn .= '<a class="edit-admin edit_form btn btn-icon btn-success mr-1 white"
                                    data-path="' . route('admins.edit', ['username' => request()->get('username', request()->segment(1)), 'admin' => $row->id]) . '"
                                    data-name="' . $row->name . '"
                                    data-id=' . $row->id . ' title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>';
                        $btn .= '<a class="btn btn-icon btn-danger mr-1 white delete-admin"
                                    data-id="' . $row->id . '" title="Delete">
                                    <i class="fa fa-trash-o"></i>
                                </a>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'status', 'image_thumbnail', 'project_statuses_json', 'assigned_farms', 'url'])
                ->addIndexColumn()
                ->with('projects', $allProjects)
                ->make(true);
        }

        $countries = CountryRegion::orderBy('name')->get();
        $projects = $this->getAllProjects();
        $siteSlug = request()->get('username', request()->segment(1));
        return view('backend.admins.user', compact('countries', 'projects', 'siteSlug'));
    }

    private function validateProjectRows(Request $request): void
    {
        $projectRows = $request->input('project_rows', []);

        // Validation is done on the frontend
        // Only ensure status values are valid when provided
        foreach ($projectRows as $row) {
            if (filled($row['status'] ?? null)) {
                $status = $row['status'];
                if (!in_array($status, ['Active', 'Inactive', 'Pending'])) {
                    throw ValidationException::withMessages([
                        'project_rows' => ['Invalid status value. Must be Active, Inactive, or Pending.'],
                    ]);
                }
            }
        }
    }

    private function normalizeProjectRows(Request $request): array
    {
        // Only include rows that have a non-empty status value
        // Projects are optional for all admin types
        return collect($request->input('project_rows', []))
            ->filter(function ($row) {
                return filled($row['project_id'] ?? null) && filled($row['status'] ?? null);
            })
            ->values()
            ->all();
    }

    private function shouldSkipProjectValidation(): bool
    {
        // Skip strict project validation for type 1 (Admin) users
        // They can work without project assignments
        $admin = Auth::user();
        return $admin && $admin->hasParentType(TypeMapping::PARENT_ADMIN);
    }

    public function updateProjectStatus(Request $request, $siteUrl)
    {
        $request->validate([
            'admin_id' => 'required|exists:admins,id',
            'project_id' => 'required|exists:projects,id',
            'status' => 'required|in:Active,Inactive,Pending',
        ]);

        $admin = Admin::findOrFail($request->admin_id);

        if ($request->status === 'Inactive' && $request->input('is_unassign')) {
            AdminProjectStatus::where('admin_id', $admin->id)
                ->where('project_id', $request->project_id)
                ->delete();

            return response()->json(['success' => true, 'message' => 'Project unassigned']);
        }

        AdminProjectStatus::updateOrCreate(
            [
                'admin_id' => $admin->id,
                'project_id' => $request->project_id,
            ],
            [
                'status' => $request->status,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    private function getAllProjects()
    {
        return Project::orderBy('project_name')->get();
    }

}