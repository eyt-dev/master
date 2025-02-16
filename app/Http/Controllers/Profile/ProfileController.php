<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index($id = null)
    {
        $admin_id = $id == null ? Auth::id() : $id;
        $admin = Admin::find( $admin_id );

        return view('profile.index', compact('admin'));
    }

    public function update(Request $request, $id = null)
    {
        $admin_id = $id == null ? Auth::id() : $id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:admins,email,' . $admin_id,
        ]);
        if (count($validator->errors()) > 0) {
            return redirect()->route('profile.index')->withErrors($validator->errors());
        }

        $admin = Admin::where('id', $admin_id)->first();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->save();

        Session::flash('success', 'Profile updated successfully.');
        return redirect()->back();
    }

    public function changePassword(Request $request, $id = null)
    {
        $admin_id = $id == null ? Auth::id() : $id;
        $validator = Validator::make($request->all(), [
            'old_password'=>'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);
        if (count($validator->errors()) > 0) {
            return redirect()->route('profile.index')->withErrors($validator->errors());
        }

        $admin = Admin::where('id', $admin_id)->first();
        $admin->password = Hash::make($request->password);
        $admin->save();

        Session::flash('success', 'Password has been changed successfully.');
        return redirect()->route('profile.index');
    }
}