<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Admin;
use App\Models\Setting;
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
        $userName = request()->segment(1);
        $host = request()->getHost();
        $setting = Setting::where('admin_domain', $host)->first();
        $parent = $setting->created_by;
        
        $type = $data['userType'];
        if($host === config('domains.admin_subdomain')){
            if($type == 1) {
                $role = 'Admin';
            } elseif($type==2) {
                $role = 'PublicVendor';
            }

            if($userName != 'register') {
                $parent = Admin::where('username', $userName)->first()?->id;
            }
        } else {
            $role = 'PrivateVendor';
        }
        
        $adminCreateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'type' => $type, 
            'parent_id' => $parent,
            'vat_country_code' => $data['vat_country_code'],
            'vat_number' => $data['vat_number'],
            'created_from' => 2,
            'url' => $data['url'],
        ];
        $admin = Admin::create($adminCreateData);

        // Assign the User role
        if($host === config('domains.admin_subdomain')){
            if($type == 1) {
                $role = 'Admin';
            } elseif($type==2) {
                $role = 'PublicVendor';
            } else {
                $role = 'PrivateVendor';
            }
        } else {
            $role = 'PrivateVendor';
        }

        $role = Role::where('name', $role)->first();
        if ($role) {
            $admin->assignRole($role);
        }

        Contact::updateOrCreate(
            ['email' => $data['email']], // condition (unique key)
            [
                'name' => $data['name'],
                'formal_name' => $data['name'],
                'vat_country_code' => $data['vat_country_code'],
                'vat_number' => $data['vat_number'],
                'created_by' => $admin->id,
            ]
        );

        return $admin;
    }
}