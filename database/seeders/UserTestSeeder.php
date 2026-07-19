<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTestSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@horizon-unistock.test',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Finance Admin',
                'email' => 'finance@horizon-unistock.test',
                'role' => 'admin',
            ],
            [
                'name' => 'Staff Gudang',
                'email' => 'staff@horizon-unistock.test',
                'role' => 'staff',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                    'must_change_password' => false,
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }
    }
}
