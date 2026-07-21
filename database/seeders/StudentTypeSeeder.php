<?php

namespace Database\Seeders;

use App\Models\StudentType;
use Illuminate\Database\Seeder;

class StudentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['kode' => 'Y1S1', 'deskripsi' => 'Year 1 Sem 1', 'status' => 'Freshman'],
            ['kode' => 'Y1S2', 'deskripsi' => 'Year 1 Sem 2', 'status' => 'Freshman'],
            ['kode' => 'Y2S1', 'deskripsi' => 'Year 2 Sem 1', 'status' => 'Continuing'],
            ['kode' => 'Y2S2', 'deskripsi' => 'Year 2 Sem 2', 'status' => 'Continuing'],
            ['kode' => 'Y3S1', 'deskripsi' => 'Year 3 Sem 1', 'status' => 'Continuing'],
            ['kode' => 'Y3S2', 'deskripsi' => 'Year 3 Sem 2', 'status' => 'Continuing'],
            ['kode' => 'Y4S1', 'deskripsi' => 'Year 4 Sem 1', 'status' => 'Continuing'],
            ['kode' => 'Y4S2', 'deskripsi' => 'Year 4 Sem 2', 'status' => 'Continuing'],
            ['kode' => 'graduated', 'deskripsi' => 'Graduated', 'status' => 'Graduated'],
        ];

        foreach ($types as $type) {
            StudentType::firstOrCreate(
                ['kode' => $type['kode']],
                $type
            );
        }
    }
}
