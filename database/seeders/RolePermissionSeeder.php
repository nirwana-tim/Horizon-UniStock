<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate(['name' => 'manage-students']);
        Permission::firstOrCreate(['name' => 'manage-distributions']);
        Permission::firstOrCreate(['name' => 'manage-finance']);

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(['manage-finance', 'manage-distributions']);

        $staff = Role::firstOrCreate(['name' => 'staff']);
        $staff->givePermissionTo('manage-students');

        Role::firstOrCreate(['name' => 'student']);
    }
}
