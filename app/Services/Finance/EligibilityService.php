<?php

namespace App\Services\Finance;

use App\Models\EligibilityRecord;
use App\Models\Student;
use App\Services\AuditService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EligibilityService
{
    public function search(?string $search): LengthAwarePaginator
    {
        $search = str_replace(['%', '_'], ['\%', '\_'], $search ?? '');

        return Student::with(['studyProgram.faculty', 'eligibilityRecords'])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('nim', 'like', "%{$search}%")
                    ->orWhere('email_kampus', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(20);
    }

    public function toggle(Student $student): string
    {
        return DB::transaction(function () use ($student) {
            $record = EligibilityRecord::where('student_id', $student->id)
                ->lockForUpdate()
                ->first();

            if ($record) {
                AuditService::log('delete', 'eligibility_record', $record->id, $record->toArray(), null);
                $record->delete();
                return "Status kelayakan untuk mahasiswa {$student->name} berhasil dihapus (Set Belum Lunas).";
            }

            $newRecord = EligibilityRecord::create([
                'student_id' => $student->id,
                'is_eligible' => true,
                'payment_status' => 'Paid',
            ]);
            AuditService::log('create', 'eligibility_record', $newRecord->id, null, $newRecord->toArray());
            return "Mahasiswa {$student->name} berhasil di-set Layak (Lunas).";
        });
    }
}
