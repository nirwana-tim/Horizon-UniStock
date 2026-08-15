<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_level', 50)->default('Y1S1')->change();
        });

        DB::statement('UPDATE students SET student_level = "Y1S1" WHERE student_level IS NULL');
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_level', 50)->nullable()->change();
        });
    }
};
