<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\UserAccessScope;
use App\Models\Company;
use App\Models\CompanyUser;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\RoleSchedulerSetting;
use App\Models\Permission;
use Carbon\Carbon;
use App\Models\GeneralSechdulerSetting;
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
    public function index()
    {
        if(request()->ajax()) {
            return datatables()->of(User::where('id', '<>', auth()->user()->id)->select('*'))
            ->addColumn('action', function($row){
                $btn = '';
                // if(Auth::user()->can('edit.user')){                    
                    $btn = '<a class="edit-user edit_form btn btn-icon btn-success mr-1 white" data-path="'.route('users.edit', ['user' => $row->id]).'" data-name="'.$row->name.'" data-id='.$row->id.' title="Edit"> <i class="fa fa-edit"></i> </a>';                                        
                // }
                // if(Auth::user()->can('delete.user')){                    
                    $btn = $btn.'<a class="btn btn-icon btn-danger mr-1 white delete-user" data-id="'.$row->id.'" title="Delete"> <i class="fa fa-trash-o"></i> </a>';                       
                // }               

                return $btn;
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('users.index', ['user' => new User()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create', ['user' => new User()]);
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
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
      
        if(empty($user)) {
            Session::flash('errorMSg', 'Somethig went wrong.');
        
            return redirect()->route('users.index');
        }

        Session::flash('successMsg', 'User inserted successfully.');
        
        return redirect()->route('users.index');
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
        return view('users.create', ['user' => $user]);
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
            return response()->json(['msg' => 'User deleted successfully!']);

        return response()->json(['msg' => 'Something went wrong, Please try again'],500);
    }

}