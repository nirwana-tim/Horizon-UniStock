<?php

namespace App\Services\Master;

use App\Models\StudentGeneration;
use App\Services\AuditService;

class StudentGenerationService
{
    public function store(array $data): StudentGeneration
    {
        $generation = StudentGeneration::create($data);
        AuditService::log('create', 'student_generation', $generation->id, null, $data);
        return $generation;
    }

    public function update(StudentGeneration $generation, array $data): StudentGeneration
    {
        $old = $generation->toArray();
        $generation->update($data);
        AuditService::log('update', 'student_generation', $generation->id, $old, $generation->fresh()->toArray());
        return $generation;
    }

    public function destroy(StudentGeneration $generation): void
    {
        AuditService::log('delete', 'student_generation', $generation->id, $generation->toArray(), null);
        $generation->delete();
    }
}
