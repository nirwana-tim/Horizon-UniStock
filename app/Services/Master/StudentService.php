<?php

namespace App\Services\Master;

use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentService
{
    public function store(array $data): Student
    {
        $data['qr_token'] = Str::uuid();

        $student = Student::create($data);

        $this->refreshEntitlementCode($student);

        AuditService::log('create', 'student', $student->id, null, $student->toArray());

        return $student;
    }

    public function update(Student $student, array $data): Student
    {
        $old = $student->toArray();
        $student->update($data);

        if (isset($data['study_program_id'], $data['program_level_id'])) {
            $this->refreshEntitlementCode($student);
        }

        AuditService::log('update', 'student', $student->id, $old, $student->fresh()->toArray());

        return $student;
    }

    public function destroy(Student $student): void
    {
        AuditService::log('delete', 'student', $student->id, $student->toArray(), null);
        $student->delete();
    }

    public function generateAccount(Student $student): User
    {
        return DB::transaction(function () use ($student) {
            $password = Str::random(12);

            $user = User::create([
                'name' => $student->name,
                'email' => $student->email_kampus ?? "{$student->nim}@temp.horizon.ac.id",
                'password' => Hash::make($password),
                'must_change_password' => true,
            ]);

            $user->assignRole('student');

            $student->update([
                'user_id' => $user->id,
            ]);

            AuditService::log('create', 'student_account', $user->id, null, [
                'student_id' => $student->id,
                'nim' => $student->nim,
                'name' => $student->name,
            ]);

            return $user;
        });
    }

    public function refreshEntitlementCode(Student $student): void
    {
        $student->loadMissing(['studyProgram.faculty', 'programLevel']);

        $code = Student::generateEntitlementCode($student);

        $student->update(['entitlement_code' => $code]);
    }
}
