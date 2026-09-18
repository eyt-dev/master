<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Country;
use Auth;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index($siteUrl, $id = null)
    {
        $admin_id = $id ?? Auth::id();
        $admin = Admin::find($admin_id);
        $countries = Country::orderBy('name')->get();
        return view('backend.profile.index', compact('admin', 'countries'));
    }

    public function update(Request $request, $siteUrl, $id = null)
    {
        $admin_id = $id == null ? Auth::id() : $id;
        $validator = Validator::make($request->all(), [
            'name'                => 'required|string|max:255',
            'email'               => 'required|email|max:255|unique:admins,email,' . $admin_id,
            'username'            => 'required|string|max:255',
            'mobile_number'       => 'required|string|max:20|unique:admins,mobile_number,' . $admin_id,
            'vat_country_code'    => 'required|integer|exists:countries,id',
            'vat_number'          => 'required|string|max:255',
            'phone_code'          => 'required|string|max:10',
            'image'               => 'nullable|image|max:2048|mimes:jpeg,png,gif',
            'notes'               => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.index', ['username' => request()->segment(1)])->withErrors($validator->errors());
        }

        $admin = Admin::where('id', $admin_id)->first();

        // Update required fields
        $admin->name = $request->input('name');
        $admin->email = $request->input('email');
        $admin->username = $request->input('username');
        $admin->mobile_number = $request->input('mobile_number');
        $admin->vat_country_code = $request->input('vat_country_code');
        $admin->vat_number = $request->input('vat_number');
        $admin->phone_code = $request->input('phone_code');

        // Update optional fields
        $admin->notes = $request->input('notes') ?: null;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($admin->image && Storage::disk('public')->exists($admin->image)) {
                Storage::disk('public')->delete($admin->image);
            }
            // Store new image
            $path = $request->file('image')->store('admins', 'public');
            $admin->image = $path;
        }

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