<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('distribution_schedules', 'student_type')) {
            return;
        }

        $afterColumn = Schema::hasColumn('distribution_schedules', 'semester') ? 'semester' : 'period';

        Schema::table('distribution_schedules', function (Blueprint $table) use ($afterColumn) {
            $column = $table->enum('student_type', ['freshman', 'continuing'])
                ->nullable()
                ->index()
                ->comment('Jenis mahasiswa: freshman / continuing, null = semua tipe');

            if (Schema::hasColumn('distribution_schedules', $afterColumn)) {
                $column->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('distribution_schedules', 'student_type')) {
            return;
        }

        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->dropColumn('student_type');
        });
    }
};
