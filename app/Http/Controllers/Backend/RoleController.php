<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteUrl)
    {
        
        if(request()->ajax()) {
            return datatables()->of(Role::select('*'))
            ->addColumn('action', 'company-action')
            ->addColumn('action', function($row){
                $btn = '';
                    $btn = $btn.'<a class="edit-role edit_form btn btn-sm btn-success btn-icon mr-1 white" data-path="'.route('role.edit', ['username' => request()->segment(1), 'role' => $row->id]).'" data-name="'.$row->name.'" data-id='.$row->id.' title="Edit"> <i class="fa fa-edit fa-1x"></i> </a>';
                    $btn = $btn.'<a class="btn btn-sm btn-icon btn-danger mr-1 white delete-role" data-id="'.$row->id.'" title="Delete"> <i class="fa fa-trash fa-1x"></i> </a>';
                return $btn;
            })
            ->addIndexColumn()
            ->make(true);
        }
        $allPermission = Permission::all();
        $groupPermission = $allPermission->groupBy('module');
       
        return view('backend.role.index', ['role' => new Role(), 'allPermission' => $allPermission, 'groupPermission' => $groupPermission]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($siteUrl)
    {
        $allPermission = Permission::get();
        $groupPermission = $allPermission->groupBy('module');
        // dd($groupPermission[2][0]->permissionModule->name);
        return view('backend.role.create', ['role' => new Role(), 'allPermission' => $allPermission, 'groupPermission' => $groupPermission]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteUrl)
    {
        $inputData = $request->all();
        // dd($inputData);
        $permission_data=$inputData['permission_data'];
        $permission_module=$inputData['permission_module'];
        $request->validate([
            'name' => ['required',
                        Rule::unique('roles')->where(function ($query) use ($request){
                            return $query->where('guard_name', $request['guard_name']);
                        })
                    ],
            'guard_name' => 'required|max:255'
        ]);
        $createRoleData=array('name' => $inputData['name'], 'guard_name' => $inputData['guard_name']);
        // dd($createRoleData,$permission_data);
        $role = Role::create($createRoleData);
        if(empty($role)){
            return redirect()->route('role.index', ['username' => request()->segment(1)]);
        }
        if($request->has('permission_data') && $role){
            $permissions = Permission::whereIn('id', $permission_data)->get();
            // dd($permission_data);
            $role->syncPermissions($permissions);
        }
        return redirect()->route('role.index', ['username' => request()->segment(1)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($siteUrl, Role $role)
    {
        if(empty($role)){
            return view('backend.role.index');
        }
        $allPermission = Permission::all();
        // dd($allPermission);
        $groupPermission = $allPermission->groupBy('module');
        return view('backend.role.create', ['role' => $role, 'allPermission' => $allPermission, 'groupPermission' => $groupPermission]);
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
        $role = Role::find($id);
        if(empty($role)){
            return redirect()->route('role.index', ['username' => request()->segment(1)]);
        }
        $inputData = $request->all();
        $permission_data=$request->permission_data ?? [];
        $permission_module=$request->permission_module ?? [];

        $request->validate([
            'name' => ['required',
                        Rule::unique('roles')->ignore($id)->where(function ($query) use ($request){
                            return $query->where('guard_name', $request['guard_name']);
                        })],
            'guard_name' => 'required|max:255'
        ]);
        $role->update(['name' => $request->name, 'guard_name' => $request->guard_name]);

        // $permission_data = $request->get('permission_data'); // Fetch permission IDs
        $permissions=array();
        if($permission_data){
            $permissions = Permission::whereIn('id', $permission_data)->get(); // Fetch permission objects
        }
        $role->syncPermissions($permissions);
        return redirect()->route('role.index', ['username' => request()->segment(1)]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteUrl, $id)
    {
        $roleDelete = Role::find($id)->delete();
        if($roleDelete)
        {
            return response()->json(['msg' => 'Role deleted successfully!']);
        }
        return response()->json(['msg' => 'Something went wrong, Please try again'],500);
    }
}
