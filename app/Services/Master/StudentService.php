<?php

namespace App\Services\Master;

use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentService
{
    public function store(array $data): Student
    {
        if (empty($data['generation_id']) && ! empty($data['nim'])) {
            $data['generation_id'] = app(GenerationResolverService::class)
                ->resolveFromNim($data['nim'])?->id;
        }

        $student = Student::create($data);

        $this->refreshEntitlementCode($student);

        AuditService::log('create', 'student', $student->id, null, $student->toArray());

        return $student;
    }

    public function update(Student $student, array $data): Student
    {
        if (empty($data['generation_id']) && ! empty($data['nim'])) {
            $data['generation_id'] = app(GenerationResolverService::class)
                ->resolveFromNim($data['nim'])?->id;
        }

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
                if (! empty($data['password'])) {
                    $userUpdates['password'] = Hash::make($data['password']);
                    $userUpdates['must_change_password'] = true;
                }
                if (! empty($userUpdates)) {
                    $user->update($userUpdates);
                }
            }
        }

        if (isset($data['study_program_id'], $data['generation_id'])
            || isset($data['student_level'])
            || isset($data['study_program_id'])
        ) {
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

            app(NotificationService::class)->sendStudentAccount($student, $password);

            return [$user, $password];
        });
    }

    public function resetPassword(Student $student): array
    {
        return DB::transaction(function () use ($student) {
            $password = Str::random(12);

            $user = User::findOrFail($student->user_id);

            $user->update([
                'password' => Hash::make($password),
                'must_change_password' => true,
            ]);

            AuditService::log('reset_password', 'student_account', $user->id, null, [
                'student_id' => $student->id,
                'nim' => $student->nim,
            ]);

            return [$user, $password];
        });
    }

    public function promoteStudents(array $ids, ?int $newGenerationId = null, ?int $newStudyProgramId = null): int
    {
        return DB::transaction(function () use ($ids, $newGenerationId, $newStudyProgramId) {
            $students = Student::whereIn('id', $ids)->lockForUpdate()->get();
            $count = 0;

            foreach ($students as $student) {
                $oldValues = $student->toArray();

                $currentLevel = strtoupper(trim((string) ($student->student_level ?? '')));
                $currentSem = strtoupper(trim((string) ($student->current_semester ?? '')));

                $effective = $currentLevel !== '' ? $currentLevel : $currentSem;

                if ($effective === 'graduated') {
                    continue;
                }

                $next = match ($effective) {
                    'Y1S1', '' => [
                        'student_level' => 'Y1S2',
                        'current_semester' => 'Y1S2',
                    ],
                    'Y1S2' => [
                        'student_level' => 'Y2S1',
                        'current_semester' => 'Y2S1',
                    ],
                    'Y2S1' => [
                        'student_level' => 'Y2S2',
                        'current_semester' => 'Y2S2',
                    ],
                    'Y2S2' => [
                        'student_level' => 'Y3S1',
                        'current_semester' => 'Y3S1',
                    ],
                    'Y3S1' => [
                        'student_level' => 'Y3S2',
                        'current_semester' => 'Y3S2',
                    ],
                    'Y3S2' => [
                        'student_level' => 'Y4S1',
                        'current_semester' => 'Y4S1',
                    ],
                    'Y4S1' => [
                        'student_level' => 'Y4S2',
                        'current_semester' => 'Y4S2',
                    ],
                    'Y4S2' => [
                        'student_level' => 'graduated',
                        'current_semester' => 'GRADUATED',
                    ],
                    default => [
                        'student_level' => 'Y1S1',
                        'current_semester' => 'Y1S1',
                    ],
                };

                $updates = [
                    'student_level' => $next['student_level'],
                    'current_semester' => $next['current_semester'],
                ];

                if ($next['student_level'] === 'graduated') {
                    $updates['status'] = 'graduated';
                }

                if ($newGenerationId) {
                    $updates['generation_id'] = $newGenerationId;
                }

                if ($newStudyProgramId) {
                    $updates['study_program_id'] = $newStudyProgramId;
                }

                $student->update($updates);

                $shouldRefresh = $newGenerationId || $newStudyProgramId
                    || ($oldValues['student_level'] ?? '') !== $student->fresh()->student_level
                    || ($oldValues['study_program_id'] ?? null) !== $student->fresh()->study_program_id;

                if ($shouldRefresh) {
                    $this->refreshEntitlementCode($student);
                }

                AuditService::log('promote', Student::class, $student->id, $oldValues, $student->fresh()->toArray());
                $count++;
            }

            return $count;
        });
    }

    public function verifyEmailKampus(Student $student, string $email): void
    {
        $old = $student->toArray();
        $student->update([
            'email_kampus' => $email,
            'email_verified_at' => now(),
        ]);

        if ($student->user_id) {
            $user = User::find($student->user_id);
            if ($user) {
                $user->update(['email' => $email]);
            }
        }

        AuditService::log('email_kampus.verified', Student::class, $student->id, $old, $student->fresh()->toArray());
    }

    public function updateEmailPribadi(Student $student, string $email): void
    {
        $old = $student->toArray();
        $student->update(['email_pribadi' => $email]);

        AuditService::log('email_pribadi.updated', Student::class, $student->id, $old, $student->fresh()->toArray());
    }

    public function refreshEntitlementCode(Student $student): void
    {
        $student->loadMissing(['studyProgram.faculty']);

        $code = Student::generateEntitlementCode($student);

        $student->update(['entitlement_code' => $code]);
    }
}
