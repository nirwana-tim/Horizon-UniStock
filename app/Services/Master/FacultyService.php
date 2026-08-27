<?php

namespace App\Services\Master;

use App\Models\Faculty;

class FacultyService
{
    public function store(array $data): Faculty
    {
        $faculty = Faculty::create($data);

        return $faculty;
    }

    public function update(Faculty $faculty, array $data): Faculty
    {
        $faculty->update($data);

        return $faculty;
    }

    public function destroy(Faculty $faculty): void
    {
        $faculty->delete();
    }
}
