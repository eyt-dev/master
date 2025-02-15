<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use Carbon\Carbon;
use App\Models\Module;
use Log;

use Illuminate\Support\Facades\Session;

class UserController extends Controller
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
            // dd($type);
            $query = User::query()->with('creator'); // Exclude logged-in user
            if (auth()->user()->type == 0) {
                // Super Admin can see all type 3 users
                if ($type == 3) {
                    $query->where('type', 3);
                }
                if ($type == 1) {
                    $query->where('type', 1)->where('created_by', auth()->user()->id);
                }
                if ($type == 2) {
                    $query->where('type', 2)->where('created_by', auth()->user()->id);
                }
            } elseif (auth()->user()->type == 1) {
                // Admin can only see type 3 users they created
                if ($type == 3) {
                    $query->where('type', 3)->where('created_by', auth()->user()->id);
                }
            }
            
            // Public Vendor (2) or Private Vendor (3) - Cannot manage users
            else {
                abort(403, "Unauthorized access");
            }

            return datatables()->of($query->select('*'))
            ->addColumn('created_by_name', function ($row) {
                return $row->creator ? $row->creator->username : 'N/A'; // Get creator's name
            })
                ->addColumn('action', function ($row) {
                    $btn = '';

                    // Super Admin can edit and delete Admins & Public Vendors
                    if (auth()->user()->type == 0 && in_array($row->type, [1, 2])) {
                        $btn .= '<a class="edit-user edit_form btn btn-icon btn-success mr-1 white" 
                                    data-path="' . route('users.edit', ['user' => $row->id]) . '" 
                                    data-name="' . $row->name . '" 
                                    data-id=' . $row->id . ' title="Edit"> 
                                    <i class="fa fa-edit"></i> 
                                </a>';
                        $btn .= '<a class="btn btn-icon btn-danger mr-1 white delete-user" 
                                    data-id="' . $row->id . '" title="Delete"> 
                                    <i class="fa fa-trash-o"></i> 
                                </a>';
                    }

                    // Admin can edit & delete only Private Vendors they created
                    if (auth()->user()->type == 1 && $row->type == 3 && $row->created_by == auth()->user()->id) {
                        $btn .= '<a class="edit-user edit_form btn btn-icon btn-success mr-1 white" 
                                    data-path="' . route('users.edit', ['user' => $row->id]) . '" 
                                    data-name="' . $row->name . '" 
                                    data-id=' . $row->id . ' title="Edit"> 
                                    <i class="fa fa-edit"></i> 
                                </a>';
                        $btn .= '<a class="btn btn-icon btn-danger mr-1 white delete-user" 
                                    data-id="' . $row->id . '" title="Delete"> 
                                    <i class="fa fa-trash-o"></i> 
                                </a>';
                    }

                    return $btn;
                })
                ->addIndexColumn()
                ->make(true);
        }
       
        return view('users.index', compact('type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($type = null)
    {
       
        $roles = Role::all();
        return view('users.create', ['user' => new User(), 'roles' => $roles, 'type' => $type]);
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
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
    
        $loggedInUser = auth()->user();
        
        // Get user type from request or default to Admin (1)
        $userType = $request->type ?? 1;
    
        $prefix = match (intval($userType)) {
            1 => 'admin',
            2 => 'publicvendor',
            3 => 'privatevendor',
            default => 'user',
        };
        // dd($prefix);
        $latestUser = User::where('type', $userType)->latest('id')->first();
        $increment = $latestUser ? $latestUser->id + 1 : 1;
    
        $username = $prefix . $increment;
    
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $username,
            'password' => Hash::make($request->password),
            'type' => $userType,
            'created_by' => $loggedInUser->id,
        ]);
    
      
        if(empty($user)) {
            Session::flash('errorMSg', 'Somethig went wrong.');
        
            return redirect()->route('users.index');
        }

        $user->assignRole($request->role);
        Session::flash('successMsg', 'Admin inserted successfully.');
        
        return redirect()->route('users.index', ['type' => $userType]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);     
     
       if(empty($user)){
            return redirect()->route('users.index');
        }

        $roles = Role::all();
        return view('users.create', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            // 'role' => 'required'
        ]);

        $user = User::find($id);

        if(empty($user)){
            return redirect()->route('users.index');
        }

        // Update the user's name and email
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);
        $user->assignRole($request->role);
        
        return redirect()->route('users.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $userDelete = User::find($id)->delete();
        if($userDelete)
            return response()->json(['msg' => 'Admin deleted successfully!']);

        return response()->json(['msg' => 'Something went wrong, Please try again'],500);
    }

}