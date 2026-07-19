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
        $student = Student::create($data);

        $this->refreshEntitlementCode($student);

        AuditService::log('create', 'student', $student->id, null, $student->toArray());

        return $student;
    }

    public function update(Student $student, array $data): Student
    {
        $old = $student->toArray();
        $student->update($data);

        if ($student->user_id) {
            $user = User::find($student->user_id);
            if ($user) {
                $userUpdates = [];
                if (isset($data['name'])) {
                    $userUpdates['name'] = $data['name'];
                }
                if (isset($data['email_kampus'])) {
                    $userUpdates['email'] = $data['email_kampus'];
                }
                if (!empty($data['password'])) {
                    $userUpdates['password'] = Hash::make($data['password']);
                }
                if (!empty($userUpdates)) {
                    $user->update($userUpdates);
                }
            }
        }

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

    public function generateAccount(Student $student): array
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

            return [$user, $password];
        });
    }

    public function promoteStudents(array $ids, int $newLevelId, ?int $newStudyProgramId = null): int
    {
        return DB::transaction(function () use ($ids, $newLevelId, $newStudyProgramId) {
            $students = Student::whereIn('id', $ids)->lockForUpdate()->get();
            $count = 0;

            foreach ($students as $student) {
                $oldValues = $student->toArray();
                $nextSemester = match ($student->current_semester) {
                    'Y1S1' => ['student_type' => 'year_1_sem_2', 'current_semester' => 'Y1S2'],
                    'Y1S2' => ['student_type' => 'year_2_sem_3', 'current_semester' => 'Y2S3'],
                    'Y2S3' => ['student_type' => 'year_2_sem_4', 'current_semester' => 'Y2S4'],
                    default => ['student_type' => 'continuing', 'current_semester' => 'Y2S4'],
                };
                $updates = [
                    'program_level_id' => $newLevelId,
                    'student_type' => $nextSemester['student_type'],
                    'current_semester' => $nextSemester['current_semester'],
                ];

                if ($newStudyProgramId) {
                    $updates['study_program_id'] = $newStudyProgramId;
                }

                $student->update($updates);
                $this->refreshEntitlementCode($student);

                AuditService::log('promote', Student::class, $student->id, $oldValues, $student->fresh()->toArray());
                $count++;
            }

            return $count;
        });
    }

    public function refreshEntitlementCode(Student $student): void
    {
        $student->loadMissing(['studyProgram.faculty', 'programLevel']);

        $code = Student::generateEntitlementCode($student);

        $student->update(['entitlement_code' => $code]);
    }
}
