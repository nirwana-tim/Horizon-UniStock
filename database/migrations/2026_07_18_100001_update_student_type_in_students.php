<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE students MODIFY student_level VARCHAR(50) DEFAULT "Y1S1"');
        DB::statement('UPDATE students SET student_level = "Y1S1" WHERE student_level IS NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE students SET student_level = NULL WHERE student_level = "Y1S1"');
        DB::statement('ALTER TABLE students MODIFY student_level VARCHAR(50) DEFAULT NULL');
    }
};
