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
use App\Models\Contact;
use App\Models\Project;
use App\Models\AdminProjectStatus;
use Illuminate\Support\Facades\DB;
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
            return $this->handleAdminList(1);
        }
        $projects = $this->getAllProjects();
        $type = 1;
        return view('backend.admins.index', compact('type', 'projects'));
    }

    public function publicVendor()
    {
        if (request()->ajax()) {
            return $this->handleAdminList(2);
        }
        $projects = $this->getAllProjects();
        $type = 2;
        return view('backend.admins.index', compact('type', 'projects'));
    }

    public function privateVendor()
    {
        if (request()->ajax()) {
            return $this->handleAdminList(3);
        }
        $projects = $this->getAllProjects();
        $type = 3;
        return view('backend.admins.index', compact('type', 'projects'));
    }

    private function handleAdminList($type)
    {
        if (request()->ajax()) {
            $admin = auth()->user();
            $query = Admin::query()->with(['creator', 'project', 'projectStatuses.project']);
            if ($admin->type == 0) {
                $query->where('type', $type);
                // Super Admin can see all type of admins
                if ($type == 1 || $type == 2) {
                    // $query->where('created_by', $admin->id);
                }
            } elseif ($admin->type == 1) {
                // Admin can only see type 3 admins they created
                if ($type == 3) {
                    // $query->where('type', 3)
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
                    return ($row->url ?? '');
                })
                ->addColumn('action', function ($row) use ($admin) {
                    $btn = '';

                    // Admin can edit & delete only Private Vendors they created  $admin->type == 1 && $row->type == 3 &&
                    if (($row->created_by == $admin->id || $row->parent_id == $admin->id) || $admin->type == 0) {
                        $btn .= $this->getActionButtons($row);
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'created_by_name', 'project_statuses_json'])
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
        $projects = $this->getProjectsForAdmin();
        return view('backend.admins.create', ['admin' => new Admin(), 'type' => $type, 'countries' => $countries, 'projects' => $projects]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {      
        $request->validate([
            'name' => 'required',
            'username' => 'nullable|string',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6',
            'mobile_number' => 'nullable|string|max:20|unique:admins,mobile_number',
            'vat_country_code' => 'nullable',
            'vat_number' => 'nullable',
            'phone_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'project_id' => 'nullable|exists:projects,id',
            'project_rows' => 'nullable|array',
            'project_rows.*.project_id' => 'nullable|exists:projects,id',
            'project_rows.*.status' => 'nullable|in:Active,Inactive,Pending',
        ]);

        $this->validateProjectRows($request);
        
        // Get admin type from request or default to Admin (1)
        $adminType = $request->type ?? 1;
    
        $prefix = match (intval($adminType)) {
            1 => 'Admin',
            2 => 'PublicVendor',
            3 => 'PrivateVendor',
            4 => 'Farmer',
            default => 'Admin',
        };

        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $uploadDir = $adminType == 4 ? 'uploads/farmers' : 'uploads/admins';
                Storage::disk('public')->makeDirectory($uploadDir, 0755, true);
                $file->storeAs($uploadDir, $filename, 'public');
                $imagePath = $uploadDir . '/' . $filename;
            }
    
            $addData = [
                'name' => $request->name,
                'email' => $request->email,
                'username' => $request->username ?? 'farmer-' . uniqid(),
                'password' => Hash::make($request->password),
                'type' => $adminType,
                'created_by' => auth()->user()->id,
                'parent_id' => auth()->user()->id,
                'created_from' => 1,
            ];

            // Add fields only for non-farmer types or when provided for farmers
            if ($adminType != 4) {
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
            if ($adminType != 4) {
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
            $routeName = match ($adminType) {
                2 => 'admins.publicVendor',
                3 => 'admins.privateVendor',
                4 => 'admins.users',
                default => 'admins.index',
            };

            return redirect()->route($routeName, ['username' => request()->get('username', request()->segment(1))]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Admin creation error: ' . $e->getMessage());
            Session::flash('errorMsg', 'Failed to create ' . strtolower($prefix) . '.');
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
        $projects = $this->getProjectsForAdmin();
        return view('backend.admins.create', ['admin' => $admin, 'roles' => $roles, 'type' => $admin->type, 'countries' => $countries, 'projects' => $projects]);
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
            return redirect()->route('admins.index', ['username' => request()->get('username', request()->segment(1))]);
        }

        $request->validate([
            'name' => 'required',
            'username' => 'nullable|string',
            'email' => 'required|email|unique:admins,email,' . $id,
            'vat_country_code' => 'nullable',
            'vat_number' => 'nullable',
            'phone_code' => 'nullable|string|max:10',
            'password' => 'nullable|min:6',
            'mobile_number' => 'nullable|string|max:20|unique:admins,mobile_number,' . $id,
            'notes' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,gif|max:2048',
            'project_id' => 'nullable|exists:projects,id',
            'project_rows' => 'nullable|array',
            'project_rows.*.project_id' => 'nullable|exists:projects,id',
            'project_rows.*.status' => 'nullable|in:Active,Inactive,Pending',
        ]);

        $this->validateProjectRows($request);

        try {
            DB::beginTransaction();

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->filled('password') ? Hash::make($request->password) : $admin->password,
            ];

            // Add fields for non-farmer types
            if ($admin->type != 4) {
                $updateData['username'] = $request->username;
                $updateData['vat_country_code'] = $request->vat_country_code;
                $updateData['vat_number'] = $request->vat_number;
            }

            // Add optional fields for all types
            $updateData['phone_code'] = $request->phone_code;
            $updateData['mobile_number'] = $request->mobile_number;
            $updateData['notes'] = $request->notes ?? $admin->notes;

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($admin->image && Storage::disk('public')->exists($admin->image)) {
                    Storage::disk('public')->delete($admin->image);
                }

                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $uploadDir = $admin->type == 4 ? 'uploads/farmers' : 'uploads/admins';
                Storage::disk('public')->makeDirectory($uploadDir, 0755, true);
                $file->storeAs($uploadDir, $filename, 'public');
                $updateData['image'] = $uploadDir . '/' . $filename;
            }

            if ($request->filled('project_id')) {
                $updateData['project_id'] = $request->project_id;
            }

            // Update admin
            $admin->update($updateData);

            // Sync project statuses if provided
            if ($request->has('project_rows')) {
                $admin->syncProjectStatuses($this->normalizeProjectRows($request));
            }

            // Sync contact information for non-farmer types
            if ($admin->type != 4) {
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
            
            return redirect()->route('admins.edit', ['username' => request()->segment(1), 'admin' => $id])
                ->with('error', 'Failed to update admin. Please try again.');
        }

        // Determine redirect route based on admin type
        $routeName = match ($admin->type) {
            2 => 'admins.publicVendor',
            3 => 'admins.privateVendor',
            4 => 'admins.users',
            default => 'admins.index',
        };

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
        $adminDelete = Admin::find($id)->delete();
        if($adminDelete)
            return response()->json(['msg' => 'Deleted successfully!']);

        return response()->json(['msg' => 'Something went wrong, Please try again'],500);
    }

    public function users(Request $request, $siteUrl)
    {
        if ($request->ajax()) {
            $admin = auth()->user();
            $query = Admin::query()->where('type', 4)->with(['creator', 'projectStatuses.project']);

            // Super Admin sees all farmers, others see only created farmers
            if ($admin->type != Admin::SUPER_ADMIN) {
                $query->where(function ($q) use ($admin) {
                    $q->where('created_by', $admin->id)
                        ->orWhere('parent_id', $admin->id);
                });
            }

            return datatables()->of($query->select('*'))
                ->addColumn('mobile_number', function($row) {
                    return $row->mobile_number ?? 'N/A';
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
                    if ($row->created_by == $admin->id || $admin->type == Admin::SUPER_ADMIN) {
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
                ->rawColumns(['action', 'status', 'image_thumbnail'])
                ->addIndexColumn()
                ->make(true);
        }

        $countries = CountryRegion::orderBy('name')->get();
        $projects = $this->getAllProjects();
        return view('backend.admins.user', compact('countries', 'projects'));
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
        return collect($request->input('project_rows', []))
            ->filter(function ($row) {
                return filled($row['project_id'] ?? null) && filled($row['status'] ?? null);
            })
            ->values()
            ->all();
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

    private function getProjectsForAdmin()
    {
        $query = Project::query()->orderBy('project_name');

        if (auth()->user()?->type != Admin::SUPER_ADMIN) {
            $query->where('created_by', auth()->id());
        }

        return $query->get();
    }

    private function getAllProjects()
    {
        return Project::orderBy('project_name')->get();
    }

}