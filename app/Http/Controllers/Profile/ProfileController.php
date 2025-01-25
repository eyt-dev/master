<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index($id = null)
    {
        $user_id = $id == null ? Auth::id() : $id;
        $user = User::find( $user_id );

        return view('profile.index', compact('user'));
    }

    public function update(Request $request, $id = null)
    {
        $user_id = $id == null ? Auth::id() : $id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user_id,
        ]);
        if (count($validator->errors()) > 0) {
            return redirect()->route('profile.index')->withErrors($validator->errors());
        }

        $user = User::where('id', $user_id)->first();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        Session::flash('success', 'Profile updated successfully.');
        return redirect()->back();
    }

    public function changePassword(Request $request, $id = null)
    {
        $user_id = $id == null ? Auth::id() : $id;
        $validator = Validator::make($request->all(), [
            'old_password'=>'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);
        if (count($validator->errors()) > 0) {
            return redirect()->route('profile.index')->withErrors($validator->errors());
        }

        $user = User::where('id', $user_id)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        Session::flash('success', 'Password has been changed successfully.');
        return redirect()->route('profile.index');
    }
}