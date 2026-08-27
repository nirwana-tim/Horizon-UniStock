<?php

namespace App\Services\Master;

use App\Models\StudyProgram;

class StudyProgramService
{
    public function store(array $data): StudyProgram
    {
        $program = StudyProgram::create($data);

        return $program;
    }

    public function update(StudyProgram $program, array $data): StudyProgram
    {
        $program->update($data);

        return $program;
    }

    public function destroy(StudyProgram $program): void
    {
        $program->delete();
    }
}
