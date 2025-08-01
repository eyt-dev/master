<?php
namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Permission;
use Illuminate\Validation\Rule;
use App\Models\Module;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($siteUrl)
    {
        if(request()->ajax()) {
            return datatables()->of(Permission::select('*'))
            ->addColumn('module', function($row){
                return ($row->permissionModule->name ?? $row->module);
            })
            ->addColumn('action', function($row){
                $btn = '<a data-path="'.route('permission.edit', ['username' => request()->segment(1), 'permission' => $row->id]).'" data-name="'.$row->name.'" data-id='.$row->id.' class="btn btn-sm btn-success btn-icon edit-permission edit_form" data-name="'.$row->name.'" data-id='.$row->id.'> <i class="fa fa-edit fa-1x"></i> </a>';
                $btn = $btn.'<a class="btn btn-sm btn-danger btn-icon ml-1 white delete-permission" data-id="'.$row->id.'" title="Delete"> <i class="fa fa-trash fa-1x"></i> </a>';
                return $btn;
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('backend.permission.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($siteUrl)
    {
        $moduleList = Module::all();
        $permission = Permission::whereNull('id')->get();
       
        return view('backend.permission.create', ['permission' => $permission, 'moduleList' => $moduleList]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $siteUrl)
    {
        $input = $request->all();
        $rules = [];
        foreach($input['name'] as $key => $val)
        {
            $rules['name.'.$key] = ['required', Rule::unique('permissions','name')->where(function ($query) use ($request){
                                            return $query->where('guard_name', $request['guard_name']);
                                        })
                                    ];
            $messages['name.'.$key.'.unique'] = 'The Permission Name has already been taken.';
        }
        $rules['module'] = 'required|max:255';
        $rules['guard_name'] = 'required|max:255';
        $validator = Validator::make($request->all(),$rules,$messages);
        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator)->withInput();
        }
        $permissionData = array();
        $now = Carbon::now('utc')->toDateTimeString();
        foreach($request->name as $value){
            $permissionData[] = [
                'name' => $value,
                'guard_name' => $request->guard_name,
                'module' => $request->module,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        $permission = Permission::insert($permissionData);
        return redirect()->route('permission.index', ['username' => request()->segment(1)]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($siteUrl, $id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($siteUrl, Permission $permission)
    {
        $moduleList = Module::all();
        if(empty($permission)){
            return view('backend.permission.index');
        }
        $permission = Permission::where('module',$permission->module)->get();
        return view('backend.permission.edit', ['permission' => $permission, 'moduleList' => $moduleList]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $siteUrl)
    {
        $input = $request->all();
        $permissionData = array();
        $inputData = array();
        for ($i=0; $i < count($input['name']); $i++) {
            if(isset($input['id'][$i])){
                $permissionData[$i]['id'] = $input['id'][$i];
                $permissionData[$i]['name'] = $input['name'][$i];
                $inputData['name'][] = $input['name'][$i];
                $inputData['id'][] = $input['id'][$i];
            }else{
                $permissionData[$i]['id'] = null;
                $permissionData[$i]['name'] = $input['name'][$i];
                $inputData['name'][] = $input['name'][$i];
                $inputData['id'][] = null;
            }
            $permissionData[$i]['guard_name'] = 'web';
            $permissionData[$i]['module'] = $input['module'];
        }
        $inputData['guard_name'] = 'web';
        $inputData['module'] = $request->module;
        $rules = [];
        foreach($inputData['name'] as $key => $val){
            if($inputData['id'][$key]){
                $rules['name.'.$key] = ['required', Rule::unique('permissions','name')->ignore($inputData['id'][$key])->where(function ($query) use ($request){
                        return $query->where('guard_name', $request['guardName']);
                    })
                ];
            }else{
                $rules['name.'.$key] = ['required', Rule::unique('permissions','name')->where(function ($query) use ($request){
                        return $query->where('guard_name', $request['guardName']);
                    })
                ];
            }
            $messages['name.'.$key.'.unique'] = 'The Permission Name has already been taken.';
        }
        $rules['module'] = 'required|max:255';
        $validator = Validator::make($inputData,$rules,$messages);
        if ($validator->fails()) {
            return response()->json( array( 'errors' => $validator->getMessageBag()->toArray() ), 400);
        }else{
            for ($i=0; $i <count($permissionData) ; $i++) {
                if(empty($permissionData[$i]['id'])){
                     Permission::insert($permissionData[$i]);
                }else{
                    $model = Permission::find($permissionData[$i]['id']);
                    $model->update($permissionData[$i]);
                }
            }
            return redirect()->route('permission.index', ['username' => request()->segment(1)]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($siteUrl, $id)
    {
        $permissionDelete = Permission::find($id)->delete();
        if($permissionDelete)
        {
            return response()->json(['msg' => 'Permission deleted successfully!']);
        }
        return response()->json(['msg' => 'Something went wrong, Please try again'],500);
    }
    
    public function moduleStore(Request $request, $siteUrl){
        $request->validate([
            'name' => 'required|unique:modules,name',
        ]);
        $group = Module::create([
            'name' => $request->name,
        ]);
        if ($request->name == trim($request->name) && str_contains($request->name, ' ')) {
            $module_name = str_replace(' ', '', $request->name);
        } else {
            $module_name = strtolower($request->name);
        }
        $module_name = strtolower($module_name);
        $permission_array = array(
            'create.'.$module_name,
            'edit.'.$module_name,
            'delete.'.$module_name,
            'view.'.$module_name
        );
        $permissionData = array();
        $now = Carbon::now('utc')->toDateTimeString();
        foreach($permission_array as $value){
            $permissionData[] = [
                'name' => $value,
                'guard_name' => 'web',
                'module' => $group->id,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }
        $permission = Permission::insert($permissionData);
        if(empty($permission)){
            return redirect()->route('permission.index', ['username' => request()->segment(1)]);
        }
        if($group){
            return response($group);
        }
    }
}
