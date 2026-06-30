<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::create(['name' => 'manage-students']);
        Permission::create(['name' => 'manage-distributions']);
        Permission::create(['name' => 'manage-finance']);

        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $finance = Role::create(['name' => 'finance']);
        $finance->givePermissionTo('manage-finance');

        $staff = Role::create(['name' => 'staff']);
        $staff->givePermissionTo('manage-students');

        Role::create(['name' => 'student']);

        $user = User::find(1);
        $user->assignRole('super_admin');
    }
}
