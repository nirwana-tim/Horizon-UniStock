<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE students MODIFY student_type VARCHAR(50) DEFAULT "year_1_sem_1"');
        DB::statement('UPDATE students SET student_type = "year_1_sem_1" WHERE student_type = "freshman"');
        DB::statement('UPDATE students SET student_type = "continuing" WHERE student_type = "continuing"');
    }

    public function down(): void
    {
        DB::statement('UPDATE students SET student_type = "freshman" WHERE student_type = "year_1_sem_1"');
        DB::statement('ALTER TABLE students MODIFY student_type ENUM("freshman", "continuing") DEFAULT "freshman"');
    }
};
