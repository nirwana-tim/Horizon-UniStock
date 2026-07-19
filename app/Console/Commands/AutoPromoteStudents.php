<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\ProgramLevel;
use App\Services\AuditService;
use Illuminate\Console\Command;

class AutoPromoteStudents extends Command
{
    protected $signature = 'students:auto-promote
        {--dry-run : Preview changes without saving}
        {--semester= : Target semester e.g. Y1S2, Y2S3}
        {--level-id= : Target program_level_id for promoted students}';

    protected $description = 'Promote all students to the next semester';

    public function handle(): int
    {
        $semesterMap = [
            'Y1S1' => ['new_type' => 'year_1_sem_2', 'new_semester' => 'Y1S2'],
            'Y1S2' => ['new_type' => 'year_2_sem_3', 'new_semester' => 'Y2S3'],
            'Y2S3' => ['new_type' => 'year_2_sem_4', 'new_semester' => 'Y2S4'],
            'Y2S4' => ['new_type' => 'continuing', 'new_semester' => 'Y2S4'],
            '' => ['new_type' => 'year_1_sem_2', 'new_semester' => 'Y1S2'],
        ];

        $targetSemester = $this->option('semester');
        $targetLevelId = $this->option('level-id');
        $dryRun = $this->option('dry-run');

        $students = Student::query();
        if ($targetSemester) {
            $students->where('current_semester', $targetSemester);
        } else {
            $students->whereIn('current_semester', ['Y1S1', 'Y1S2', 'Y2S3', 'Y2S4'])
                ->orWhereNull('current_semester')
                ->orWhere('current_semester', '');
        }

        $count = 0;
        $students->chunk(100, function ($chunk) use ($semesterMap, $targetSemester, $targetLevelId, $dryRun, &$count) {
            foreach ($chunk as $student) {
                $sem = $student->current_semester ?? '';
                $map = $semesterMap[$sem] ?? $semesterMap[''];

                $newLevelId = $targetLevelId;
                if (!$newLevelId) {
                    $nextYear = ((int) substr($student->programLevel?->code ?? '2500', 0, 2)) + 1;
                    $nextCode = sprintf('%02d%02d', $nextYear, $nextYear + 1);
                    $nextLevel = ProgramLevel::where('code', $nextCode)->first();
                    $newLevelId = $nextLevel?->id;
                }

                if ($dryRun) {
                    $this->line(sprintf(
                        '[DRY-RUN] %s (%s): %s → %s, sem %s → %s',
                        $student->nim,
                        $student->name,
                        $student->student_type,
                        $map['new_type'],
                        $student->current_semester ?? '-',
                        $map['new_semester']
                    ));
                } else {
                    $oldValues = $student->toArray();
                    $updates = [
                        'student_type' => $map['new_type'],
                        'current_semester' => $map['new_semester'],
                    ];
                    if ($newLevelId) {
                        $updates['program_level_id'] = $newLevelId;
                    }
                    $student->update($updates);
                    AuditService::log('auto_promote', Student::class, $student->id, $oldValues, $student->fresh()->toArray());
                }
                $count++;
            }
        });

        $this->info("{$count} students processed.");

        return self::SUCCESS;
    }
}
