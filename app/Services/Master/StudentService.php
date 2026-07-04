<?php

namespace App\Services\Master;

use App\Models\Student;
use App\Services\AuditService;
use Illuminate\Support\Str;

class StudentService
{
    public function store(array $data): Student
    {
        $data['qr_token'] = Str::uuid();

        $student = Student::create($data);

        AuditService::log('create', 'student', $student->id, null, $student->toArray());

        return $student;
    }

    public function update(Student $student, array $data): Student
    {
        $old = $student->toArray();
        $student->update($data);

        AuditService::log('update', 'student', $student->id, $old, $student->fresh()->toArray());

        return $student;
    }

    public function destroy(Student $student): void
    {
        AuditService::log('delete', 'student', $student->id, $student->toArray(), null);
        $student->delete();
    }
}
