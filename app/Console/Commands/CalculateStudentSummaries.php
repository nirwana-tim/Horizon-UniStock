<?php

namespace App\Console\Commands;

use App\Models\DistributionTransaction;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalculateStudentSummaries extends Command
{
    protected $signature = 'summaries:calculate {--student-id= : Hitung ulang untuk satu student}';

    protected $description = 'Pre-calculate summary distribusi per mahasiswa (materialized view)';

    public function handle(): int
    {
        $query = Student::query()->with('studyProgram');

        if ($studentId = $this->option('student-id')) {
            $query->where('id', (int) $studentId);
        }

        $query->select(['id'])->orderBy('id')->chunk(500, function ($students) {
            foreach ($students as $student) {
                $this->calculateForStudent($student->id);
            }
        });

        $this->info('Student summaries updated.');

        return self::SUCCESS;
    }

    private function calculateForStudent(int $studentId): void
    {
        $aggregate = DistributionTransaction::select(
            DB::raw('COUNT(*) as total_transactions'),
            DB::raw('COALESCE(SUM(di.quantity), 0) as total_items_received'),
            DB::raw('COALESCE(SUM(di.quantity * di.selling_price_at_distribution), 0) as total_spend'),
            DB::raw('MAX(pickup_time) as last_distribution_at')
        )
            ->leftJoin('distribution_items as di', 'distribution_transactions.id', '=', 'di.transaction_id')
            ->where('distribution_transactions.student_id', $studentId)
            ->whereNotIn('distribution_transactions.status', ['cancelled'])
            ->first();

        DB::table('student_summaries')->updateOrInsert(
            ['student_id' => $studentId],
            [
                'total_transactions' => $aggregate->total_transactions ?? 0,
                'total_items_received' => $aggregate->total_items_received ?? 0,
                'total_spend' => $aggregate->total_spend ?? 0,
                'last_distribution_at' => $aggregate->last_distribution_at,
                'last_calculated_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
