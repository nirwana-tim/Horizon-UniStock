<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('program_levels')->whereIn('code', ['SEM-1', 'SEM-2', 'SEM-3', 'SEM-4', 'SEM-5', 'SEM-6'])->delete();
    }

    public function down(): void
    {
        $semesters = [
            ['name' => 'Semester 1', 'code' => 'SEM-1'],
            ['name' => 'Semester 2', 'code' => 'SEM-2'],
            ['name' => 'Semester 3', 'code' => 'SEM-3'],
            ['name' => 'Semester 4', 'code' => 'SEM-4'],
            ['name' => 'Semester 5', 'code' => 'SEM-5'],
            ['name' => 'Semester 6', 'code' => 'SEM-6'],
        ];

        foreach ($semesters as $semester) {
            DB::table('program_levels')->insert($semester);
        }
    }
};
