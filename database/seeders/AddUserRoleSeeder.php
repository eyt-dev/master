<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AddUserRoleSeeder extends Seeder
{
    public function run()
    {
        $date = Carbon::now()->format('Y-m-d H:i:s');

        // Create User role if it doesn't exist
        $userRole = Role::firstOrCreate(
            ['name' => 'User'],
            [
                'name' => 'User', 
                'guard_name' => 'web',
                'created_at' => $date,
                'updated_at' => $date
            ]
        );
    }
}