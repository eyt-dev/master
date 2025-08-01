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

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($type = null)
    {
            
        if (request()->ajax()) {
            $type = empty($type) ? intval(request('type')) : $type;
            $admin = auth()->user();

            $query = Admin::query()->with('creator'); // Exclude logged-in admin
            if ($admin->type == 0) {
                // Super Admin can see all type 3 admins
                if ($type == 3) {
                    $query->where('type', 3);
                }
                if ($type == 1) {
                    $query->where('type', 1)->where('created_by', $admin->id);
                }
                if ($type == 2) {
                    $query->where('type', 2)->where('created_by', $admin->id);
                }
            } elseif ($admin->type == 1) {
                // Admin can only see type 3 admins they created
                if ($type == 3) {
                    $query->where('type', 3)->where('created_by', $admin->id);
                }
            }
            
            // Public Vendor (2) or Private Vendor (3) - Cannot manage admins
            else {
                abort(403, "Unauthorized access");
            }

            return datatables()->of($query->select('*'))
            ->addColumn('created_by_name', function ($row) {
                return $row->creator ? $row->creator->username : 'N/A'; // Get creator's name
            })
                ->addColumn('action', function ($row) use ($admin) {
                    $btn = '';

                    // Super Admin can edit and delete Admins & Public Vendors
                    if ($admin->type == 0 && in_array($row->type, [1, 2])) {
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

                    // Admin can edit & delete only Private Vendors they created
                    if ($admin->type == 1 && $row->type == 3 && $row->created_by == $admin->id) {
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
                ->addIndexColumn()
                ->make(true);
        }
       
        return view('backend.admins.index', compact('type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type = null)
    {       
        return view('backend.admins.create', ['admin' => new Admin(), 'type' => $type]);
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
            'username' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6',
        ]);
        
        // Get admin type from request or default to Admin (1)
        $adminType = $request->type ?? 1;
    
        $prefix = match (intval($adminType)) {
            1 => 'Admin',
            2 => 'PublicVendor',
            3 => 'PrivateVendor',
            default => 'Admin',
        };
    
        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'type' => $adminType,
            'created_by' => auth()->user()->id,
        ]);

        if(empty($admin)) {
            Session::flash('errorMSg', 'Somethig went wrong.');
        
            return redirect()->url("/e/admins/{$request->type}");
        }

        $role = Role::where(['name' => $prefix])->first();
        $admin->assignRole([$role->id]);

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

        Session::flash('successMsg', '{$prefix} inserted successfully.');
        
        return redirect()->route('admins.index', ['username' => request()->get('username', request()->segment(1)), 'type' => $adminType]);
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
     
       if(empty($admin)){
            return redirect()->route('admins.index', ['username' => request()->get('username', request()->segment(1))]);
        }

        $roles = Role::all();
        return view('backend.admins.create', ['admin' => $admin, 'roles' => $roles]);
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
        $request->validate([
            'name' => 'required',
            'username' => 'required',
        ]);

        $admin = Admin::find($id);

        if(empty($admin)){
            return redirect()->route('admins.index', ['username' => request()->get('username', request()->segment(1))]);
        }

        // Update the admin's name and email
        $admin->update([
            'name' => $request->name,
            'username' => $request->username,
            'password' => $request->filled('password') ? Hash::make($request->password) : $admin->password,
        ]);
        // $admin->assignRole($request->role);
        
        return redirect()->route('admins.index', ['username' => request()->get('username', request()->segment(1))]);
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

}