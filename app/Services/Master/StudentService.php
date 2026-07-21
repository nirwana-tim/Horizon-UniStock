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

    public function promoteStudents(array $ids, ?int $newLevelId = null, ?int $newStudyProgramId = null): int
    {
        return DB::transaction(function () use ($ids, $newLevelId, $newStudyProgramId) {
            $students = Student::whereIn('id', $ids)->lockForUpdate()->get();
            $count = 0;

            foreach ($students as $student) {
                $oldValues = $student->toArray();

                $currentType = strtolower(trim((string) ($student->student_type ?? '')));
                $currentSem = strtoupper(trim((string) ($student->current_semester ?? '')));

                $next = match (true) {
                    $currentType === 'Y1S1' || $currentType === 'freshman' || $currentSem === 'Y1S1' => [
                        'student_type' => 'Y1S2',
                        'current_semester' => 'Y1S2',
                    ],
                    $currentType === 'Y1S2' || $currentSem === 'Y1S2' => [
                        'student_type' => 'Y2S1',
                        'current_semester' => 'Y2S1',
                    ],
                    $currentType === 'Y2S1' || $currentSem === 'Y2S1' => [
                        'student_type' => 'Y2S2',
                        'current_semester' => 'Y2S2',
                    ],
                    $currentType === 'Y2S2' || $currentSem === 'Y2S2' => [
                        'student_type' => 'Y3S1',
                        'current_semester' => 'Y3S1',
                    ],
                    $currentType === 'Y3S1' || $currentSem === 'Y3S1' => [
                        'student_type' => 'Y3S2',
                        'current_semester' => 'Y3S2',
                    ],
                    $currentType === 'Y3S2' || $currentSem === 'Y3S2' => [
                        'student_type' => 'Y4S1',
                        'current_semester' => 'Y4S1',
                    ],
                    $currentType === 'Y4S1' || $currentSem === 'Y4S1' => [
                        'student_type' => 'Y4S2',
                        'current_semester' => 'Y4S2',
                    ],
                    $currentType === 'Y4S2' || $currentSem === 'Y4S2' => [
                        'student_type' => 'graduated',
                        'current_semester' => 'GRADUATED',
                    ],
                    default => [
                        'student_type' => 'graduated',
                        'current_semester' => 'GRADUATED',
                    ],
                };

                $updates = [
                    'student_type' => $next['student_type'],
                    'current_semester' => $next['current_semester'],
                ];

                if ($newLevelId) {
                    $updates['program_level_id'] = $newLevelId;
                }

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
