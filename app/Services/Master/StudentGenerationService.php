<?php

namespace App\Services\Master;

use App\Models\StudentGeneration;

class StudentGenerationService
{
    public function store(array $data): StudentGeneration
    {
        $generation = StudentGeneration::create($data);

        return $generation;
    }

    public function update(StudentGeneration $generation, array $data): StudentGeneration
    {
        $generation->update($data);

        return $generation;
    }

    public function destroy(StudentGeneration $generation): void
    {
        $generation->delete();
    }
}
