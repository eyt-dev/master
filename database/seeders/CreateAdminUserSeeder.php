<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Hash;
use Carbon\Carbon;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Admin',
            'username' => 'johndoe',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin@123'),
            'type' => User::SUPER_ADMIN, // Default is 0 (Super Admin)
            'email_verified_at' => null,
            'remember_token' => null,
            'created_by' => 0,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        
        $role = Role::where(['name' => 'Admin'])->first();
        $user->assignRole([$role->id]);
    }
}