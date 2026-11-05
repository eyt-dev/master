<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\TypeMapping;
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

    public function showRegistrationForm()
    {
        $countries = CountryRegion::orderBy('name')->get();
        $userTypeIds = [
            'admin' => TypeMapping::defaultIdForParent(TypeMapping::PARENT_ADMIN),
            'public_vendor' => TypeMapping::defaultIdForParent(TypeMapping::PARENT_PUBLIC_VENDOR),
            'private_vendor' => TypeMapping::defaultIdForParent(TypeMapping::PARENT_PRIVATE_VENDOR),
        ];

        return view('auth.register', compact('countries', 'userTypeIds'));
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'userType' => ['required', 'exists:type_mappings,id'],
        ]);
    }

    protected function create(array $data)
    {
        $userName = request()->segment(1);
        $host = request()->getHost();
        $setting = Setting::where('admin_domain', $host)->first();
        $parent = $setting->created_by;

        $type = $data['userType'];
        $parentType = TypeMapping::find($type)?->parent_type;
        if($host === config('domains.admin_subdomain')){
            if($userName != 'register') {
                $parent = Admin::where('username', $userName)->first()?->id;
            }
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
            $role = match ($parentType) {
                TypeMapping::PARENT_ADMIN => 'Admin',
                TypeMapping::PARENT_PUBLIC_VENDOR => 'PublicVendor',
                default => 'PrivateVendor',
            };
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
