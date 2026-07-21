<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_types', function (Blueprint $table) {
            $table->renameColumn('value', 'kode');
            $table->renameColumn('label', 'deskripsi');
            $table->string('status')->nullable()->after('deskripsi');
            $table->dropColumn(['is_active', 'sort_order']);
        });

        $map = [
            'year_1_sem_1' => ['kode' => 'Y1S1', 'deskripsi' => 'Year 1 Sem 1', 'status' => 'Freshman'],
            'year_1_sem_2' => ['kode' => 'Y1S2', 'deskripsi' => 'Year 1 Sem 2', 'status' => 'Freshman'],
            'year_2_sem_3' => ['kode' => 'Y2S1', 'deskripsi' => 'Year 2 Sem 1', 'status' => 'Continuing'],
            'year_2_sem_4' => ['kode' => 'Y2S2', 'deskripsi' => 'Year 2 Sem 2', 'status' => 'Continuing'],
            'continuing'    => ['kode' => 'Y2S1', 'deskripsi' => 'Year 2 Sem 1', 'status' => 'Continuing'],
            'year_3_sem_1' => ['kode' => 'Y3S1', 'deskripsi' => 'Year 3 Sem 1', 'status' => 'Continuing'],
            'year_3_sem_2' => ['kode' => 'Y3S2', 'deskripsi' => 'Year 3 Sem 2', 'status' => 'Continuing'],
            'year_4_sem_1' => ['kode' => 'Y4S1', 'deskripsi' => 'Year 4 Sem 1', 'status' => 'Continuing'],
            'year_4_sem_2' => ['kode' => 'Y4S2', 'deskripsi' => 'Year 4 Sem 2', 'status' => 'Continuing'],
            'graduated'     => ['kode' => 'graduated', 'deskripsi' => 'Graduated', 'status' => 'Graduated'],
        ];

        foreach ($map as $oldValue => $new) {
            if ($oldValue === 'continuing') {
                DB::table('student_types')->where('kode', 'Y2S1')->update(['status' => 'Continuing']);
                DB::table('student_types')->where('kode', $oldValue)->delete();
                continue;
            }
            DB::table('student_types')
                ->where('kode', $oldValue)
                ->update($new);
        }

        $fkTables = ['students', 'entitlements', 'distribution_schedules', 'size_change_events'];
        $fkMap = [
            'year_1_sem_1' => 'Y1S1',
            'year_1_sem_2' => 'Y1S2',
            'year_2_sem_3' => 'Y2S1',
            'year_2_sem_4' => 'Y2S2',
            'year_3_sem_1' => 'Y3S1',
            'year_3_sem_2' => 'Y3S2',
            'year_4_sem_1' => 'Y4S1',
            'year_4_sem_2' => 'Y4S2',
            'graduated'     => 'graduated',
            'continuing'    => 'Y2S1',
            'freshman'      => 'Y1S1',
        ];

        foreach ($fkTables as $table) {
            foreach ($fkMap as $old => $new) {
                DB::table($table)
                    ->where('student_type', $old)
                    ->update(['student_type' => $new]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('student_types', function (Blueprint $table) {
            $table->renameColumn('kode', 'value');
            $table->renameColumn('deskripsi', 'label');
            $table->dropColumn('status');
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
        });
    }
};
