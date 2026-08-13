<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPERADMIN_EMAIL', 'admin@horizon-unistock.ac.id');
        $password = env('SUPERADMIN_PASSWORD', 'SuperAdmin!123');

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => $password,
                'must_change_password' => false,
                'is_active' => true,
            ]
        );

        if (!$user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
        }

        $this->command->info('Super admin siap digunakan!');
        $this->command->info('Email    : ' . $email);
        $this->command->info('Password : ' . $password);
        $this->command->warn('Ganti password setelah login pertama di halaman profil.');
    }
}
