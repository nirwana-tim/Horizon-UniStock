<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentGeneration;
use App\Services\Master\StudentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoPromoteStudents extends Command
{
    protected $signature = 'students:auto-promote
        {--dry-run : Preview changes without saving}
        {--semester= : Target semester e.g. Y1S1, Y2S3}
        {--level-id= : Target generation_id for promoted students}';

    protected $description = 'Promote all active students to the next semester';

    private array $semesterMap = [
        '' => 'Y1S1',
        'Y1S1' => 'Y1S2',
        'Y1S2' => 'Y2S1',
        'Y2S1' => 'Y2S2',
        'Y2S2' => 'Y3S1',
        'Y3S1' => 'Y3S2',
        'Y3S2' => 'Y4S1',
        'Y4S1' => 'Y4S2',
        'Y4S2' => 'graduated',
    ];

    public function handle(): int
    {
        $targetSemester = $this->option('semester');
        $targetLevelId = $this->option('level-id');
        $dryRun = $this->option('dry-run');

        $studentIds = [];
        $previewRows = [];

        Student::with('generation')
            ->where(function ($q) use ($targetSemester) {
                if ($targetSemester) {
                    $q->where('current_semester', $targetSemester);
                } else {
                    $q->whereIn('current_semester', ['Y1S1', 'Y1S2', 'Y2S1', 'Y2S2', 'Y3S1', 'Y3S2', 'Y4S1', 'Y4S2'])
                        ->orWhereNull('current_semester')
                        ->orWhere('current_semester', '');
                }
            })
            ->whereIn('status', ['active', 'leave'])
            ->chunk(100, function ($chunk) use ($targetLevelId, $dryRun, &$studentIds, &$previewRows) {
                foreach ($chunk as $student) {
                    $next = $this->resolveNextSemester($student);
                    $genId = $this->resolveGenerationId($student, $targetLevelId);

                    if ($dryRun) {
                        $previewRows[] = sprintf(
                            '[DRY-RUN] %s (%s): level %s → %s, sem %s → %s, gen %s',
                            $student->nim,
                            $student->name,
                            $student->student_level,
                            $next,
                            $student->current_semester ?? '-',
                            $this->semesterMap[$student->current_semester ?? ''] ?? '?',
                            $genId ?? 'auto',
                        );
                    } else {
                        $studentIds[] = $student->id;
                    }
                }
            });

        if ($dryRun) {
            foreach ($previewRows as $row) {
                $this->line($row);
            }
            $this->info(sprintf("\n%d students would be promoted.", count($previewRows)));
            return self::SUCCESS;
        }

        if (empty($studentIds)) {
            $this->warn('No eligible students found for promotion.');
            return self::SUCCESS;
        }

        DB::transaction(function () use ($studentIds, $targetLevelId) {
            foreach (array_chunk($studentIds, 100) as $chunkIds) {
                $count = app(StudentService::class)->promoteStudents($chunkIds, $targetLevelId);
                $this->line(sprintf('  ✓ %d students promoted.', $count));
            }
        });

        $this->info(sprintf("\n%d students promoted successfully.", count($studentIds)));

        return self::SUCCESS;
    }

    private function resolveNextSemester(Student $student): string
    {
        $sem = $student->current_semester ?? '';
        return $this->semesterMap[$sem] ?? 'Y1S1';
    }

    private function resolveGenerationId(Student $student, ?int $overrideId): ?int
    {
        if ($overrideId) {
            return $overrideId;
        }

        $code = $student->generation?->code;
        if (!$code || strlen($code) < 2) {
            return null;
        }

        $nextYear = ((int) substr($code, 0, 2)) + 1;
        $nextCode = sprintf('%02d%02d', $nextYear, $nextYear + 1);

        return StudentGeneration::where('code', $nextCode)->value('id');
    }
}
