<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('superadmin.email');
        $password = config('superadmin.password');

        if (! $password) {
            $password = Str::password(20);
            $this->command->warn('SUPERADMIN_PASSWORD tidak di-set. Password acak dibuat dan WAJIB diganti.');
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => $password,
                'must_change_password' => true,
                'is_active' => true,
            ]
        );

        if (! $user->hasRole('super_admin')) {
            $user->assignRole('super_admin');
        }

        $this->command->info('Super admin siap digunakan!');
        $this->command->info('Email    : '.$email);

        if (! config('superadmin.password')) {
            $this->command->warn('Password sementara: '.$password.' — ganti segera setelah login.');
        }
    }
}
