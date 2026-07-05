<?php

namespace Database\Seeders;

use App\Models\ProgramLevel;
use App\Models\Student;
use App\Models\StudyProgram;
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
            [
                'name' => 'Mahasiswa',
                'email' => 'student@horizon-unistock.test',
                'role' => 'student',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => bcrypt('password'),
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }
        }

        $studentUser = User::where('email', '=', 'student@horizon-unistock.test', 'and')->first();

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nim' => '1234567890123456',
                'name' => 'Mahasiswa',
                'email_kampus' => 'mahasiswa@krw.horizon.ac.id',
                'email_pribadi' => 'student@horizon-unistock.test',
                'study_program_id' => StudyProgram::first()->id ?? 1,
                'program_level_id' => ProgramLevel::first()->id ?? 1,
                'student_type' => 'freshman',
            ]
        );

        // Seed eligibility record for test student
        \App\Models\EligibilityRecord::firstOrCreate(
            [
                'student_id' => $student->id,
            ],
            [
                'is_eligible' => true,
                'payment_status' => 'Paid',
            ]
        );
    }
}
