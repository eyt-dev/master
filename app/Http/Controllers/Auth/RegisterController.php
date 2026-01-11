<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\CountryRegion;
use App\Traits\RegistersAdmins;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    use RegistersAdmins;

    protected $redirectTo = '/e/dashboard';

    public function __construct()
    {
        $this->middleware('guest:admin');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    protected function create(array $data)
    {
        $admin = Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'type' => 4, // User type
        ]);

        // Assign the User role
        $role = Role::where('name', 'User')->first();
        if ($role) {
            $admin->assignRole($role);
        }

        // Create contact information
        Contact::create([
            'name' => $data['name'],
            'formal_name' => $data['name'],
            'email' => $data['email'],
            'vat_country_code' => $data['vat_country_code'],
            'vat_number' => $data['vat_number'],
            'created_by' => $admin->id,
        ]);

        return $admin;
    }
}