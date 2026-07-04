<?php

namespace App\Services\Master;

use App\Models\Faculty;
use App\Services\AuditService;

class FacultyService
{
    public function store(array $data): Faculty
    {
        $faculty = Faculty::create($data);
        AuditService::log('create', 'faculty', $faculty->id, null, $data);
        return $faculty;
    }

    public function update(Faculty $faculty, array $data): Faculty
    {
        $old = $faculty->toArray();
        $faculty->update($data);
        AuditService::log('update', 'faculty', $faculty->id, $old, $faculty->fresh()->toArray());
        return $faculty;
    }

    public function destroy(Faculty $faculty): void
    {
        AuditService::log('delete', 'faculty', $faculty->id, $faculty->toArray(), null);
        $faculty->delete();
    }
}
