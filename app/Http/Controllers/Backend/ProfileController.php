<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index($siteUrl, $id = null)
    {
        $admin_id = $id ?? Auth::id();
        $admin = Admin::find( $admin_id );
        return view('backend.profile.index', compact('admin'));
    }

    public function update(Request $request, $siteUrl, $id = null)
    {
        $admin_id = $id == null ? Auth::id() : $id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:admins,email,' . $admin_id,
            'username' => 'required'
        ]);
        if (count($validator->errors()) > 0) {
            return redirect()->route('profile.index', ['username' => request()->segment(1)])->withErrors($validator->errors());
        }

        $admin = Admin::where('id', $admin_id)->first();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->username = $request->username;
        $admin->save();

        Session::flash('success', 'Profile updated successfully.');
        return redirect()->back();
    }

    public function changePassword(Request $request, $siteUrl, $id = null)
    {
        $admin_id = $id == null ? Auth::id() : $id;
        $validator = Validator::make($request->all(), [
            'old_password'=>'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);
        if (count($validator->errors()) > 0) {
            return redirect()->route('profile.index', ['username' => request()->segment(1)])->withErrors($validator->errors());
        }

        $admin = Admin::where('id', $admin_id)->first();
        $admin->password = Hash::make($request->password);
        $admin->save();

        Session::flash('success', 'Password has been changed successfully.');
        return redirect()->route('profile.index', ['username' => request()->segment(1)]);
    }
}