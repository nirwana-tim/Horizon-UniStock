<?php

namespace Database\Seeders;

use App\Models\ProgramLevel;
use App\Models\Student;
use App\Models\StudyProgram;
use App\Models\User;
use App\Models\EligibilityRecord;
use App\Models\StudentSizeProfile;
use App\Models\StudentSizeItem;
use App\Models\Entitlement;
use Illuminate\Database\Seeder;

class UserTestSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Core Role Users
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

        // 2. Fetch Master Records for Student Association
        $level2026 = ProgramLevel::where('code', '=', '2627', 'and')->first(['*']) ?? ProgramLevel::first(['*']);
        $level2025 = ProgramLevel::where('code', '=', '2526', 'and')->first(['*']) ?? ProgramLevel::first(['*']);

        $progS1Kep = StudyProgram::where('code', '=', 'S1-KEP', 'and')->first(['*']) ?? StudyProgram::first(['*']);
        $progD3Keb = StudyProgram::where('code', '=', 'D3-KEB', 'and')->first(['*']) ?? StudyProgram::first(['*']);
        $progS1Inf = StudyProgram::where('code', '=', 'S1-INF', 'and')->first(['*']) ?? StudyProgram::first(['*']);
        $progS1Mnj = StudyProgram::where('code', '=', 'S1-MNJ', 'and')->first(['*']) ?? StudyProgram::first(['*']);

        // 3. Define Test Students representing different cases
        $studentsData = [
            [
                'nim' => '20260001',
                'name' => 'Andi (Keperawatan)',
                'email_pribadi' => 'andi@horizon-unistock.test',
                'email_kampus' => 'andi.20260001@krw.horizon.ac.id',
                'study_program_id' => $progS1Kep->id,
                'program_level_id' => $level2026->id,
                'student_type' => 'freshman',
                'is_eligible' => true,
                'payment_status' => 'Paid',
                'fill_sizes' => false, // Case: Eligible, has not filled sizes yet
            ],
            [
                'nim' => '20260002',
                'name' => 'Budi (Kebidanan)',
                'email_pribadi' => 'budi@horizon-unistock.test',
                'email_kampus' => 'budi.20260002@krw.horizon.ac.id',
                'study_program_id' => $progD3Keb->id,
                'program_level_id' => $level2026->id,
                'student_type' => 'freshman',
                'is_eligible' => true,
                'payment_status' => 'Paid',
                'fill_sizes' => true, // Case: Eligible, has filled sizes (ready to collect/scan)
            ],
            [
                'nim' => '20260003',
                'name' => 'Cici (Informatika)',
                'email_pribadi' => 'cici@horizon-unistock.test',
                'email_kampus' => 'cici.20260003@krw.horizon.ac.id',
                'study_program_id' => $progS1Inf->id,
                'program_level_id' => $level2026->id,
                'student_type' => 'freshman',
                'is_eligible' => false,
                'payment_status' => 'Unpaid', // Case: Not Eligible/Unpaid, has filled sizes
                'fill_sizes' => true,
            ],
            [
                'nim' => '20250001',
                'name' => 'Dewi (Manajemen)',
                'email_pribadi' => 'dewi@horizon-unistock.test',
                'email_kampus' => 'dewi.20250001@krw.horizon.ac.id',
                'study_program_id' => $progS1Mnj->id,
                'program_level_id' => $level2025->id,
                'student_type' => 'continuing', // Case: Mahasiswa Lama / Continuing Student
                'is_eligible' => true,
                'payment_status' => 'Paid',
                'fill_sizes' => true,
            ],
        ];

        foreach ($studentsData as $data) {
            // Create user account
            $user = User::firstOrCreate(
                ['email' => $data['email_pribadi']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                    'must_change_password' => false,
                ]
            );

            if (!$user->hasRole('student')) {
                $user->assignRole('student');
            }

            // Create student record
            $student = Student::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'nim' => $data['nim'],
                    'name' => $data['name'],
                    'email_pribadi' => $data['email_pribadi'],
                    'email_kampus' => $data['email_kampus'],
                    'study_program_id' => $data['study_program_id'],
                    'program_level_id' => $data['program_level_id'],
                    'student_type' => $data['student_type'],
                    'qr_token' => \Illuminate\Support\Str::uuid()->toString(),
                    'qr_generated_at' => now(),
                ]
            );

            // Compute & save entitlement code
            $entCode = Student::generateEntitlementCode($student);
            $student->update(['entitlement_code' => $entCode]);

            // Create payment eligibility record
            EligibilityRecord::firstOrCreate(
                ['student_id' => $student->id],
                [
                    'is_eligible' => $data['is_eligible'],
                    'payment_status' => $data['payment_status'],
                ]
            );

            // If requested, pre-fill sizes for this student based on their entitlement
            if ($data['fill_sizes'] && $entCode) {
                $entitlement = Entitlement::where('code', '=', $entCode, 'and')
                    ->with('items.item.variants')
                    ->first();

                if ($entitlement) {
                    $profile = StudentSizeProfile::firstOrCreate(
                        ['student_id' => $student->id],
                        ['is_filled' => true]
                    );

                    foreach ($entitlement->items as $entItem) {
                        $item = $entItem->item;
                        if (!$item) {
                            continue;
                        }

                        // Determine a default size from variants
                        $firstVariant = $item->variants->first();
                        $defaultSize = $firstVariant ? $firstVariant->size : '01';

                        StudentSizeItem::firstOrCreate(
                            [
                                'size_profile_id' => $profile->id,
                                'item_id' => $item->id,
                            ],
                            [
                                'size' => $defaultSize,
                                'change_count' => 0,
                            ]
                        );
                    }
                }
            }
        }
    }
}
