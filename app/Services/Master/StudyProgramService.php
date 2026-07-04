<?php

namespace App\Services\Master;

use App\Models\StudyProgram;
use App\Services\AuditService;

class StudyProgramService
{
    public function store(array $data): StudyProgram
    {
        $program = StudyProgram::create($data);
        AuditService::log('create', 'study_program', $program->id, null, $data);
        return $program;
    }

    public function update(StudyProgram $program, array $data): StudyProgram
    {
        $old = $program->toArray();
        $program->update($data);
        AuditService::log('update', 'study_program', $program->id, $old, $program->fresh()->toArray());
        return $program;
    }

    public function destroy(StudyProgram $program): void
    {
        AuditService::log('delete', 'study_program', $program->id, $program->toArray(), null);
        $program->delete();
    }
}
