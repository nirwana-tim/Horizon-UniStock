<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('entitlement_code')->nullable()->after('student_type')->comment('Kode grup entitlement: {LevelCode}{FacultyCode}{ProdiCode}');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('entitlement_code');
        });
    }
};
