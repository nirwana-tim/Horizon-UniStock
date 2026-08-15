<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perbaiki drift index unik pada student_size_profiles & eligibility_records.
     * Migration asli mendeklarasikan ->unique() pada kolom student_id, tetapi
     * DB aktual hanya memiliki FK index (non-unique). Tambahkan unique index
     * yang hilang secara idempotent.
     */
    public function up(): void
    {
        foreach (['student_size_profiles', 'eligibility_records'] as $table) {
            if ($this->hasUniqueIndex($table, 'student_id')) {
                continue;
            }

            Schema::table($table, function ($blueprint) {
                $blueprint->unique('student_id');
            });
        }
    }

    public function down(): void
    {
        foreach (['student_size_profiles', 'eligibility_records'] as $table) {
            if (! $this->hasUniqueIndex($table, 'student_id')) {
                continue;
            }

            Schema::table($table, function ($blueprint) use ($table) {
                $blueprint->dropForeign(['student_id']);
                $blueprint->dropUnique($table.'_student_id_unique');
                $blueprint->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            });
        }
    }

    private function hasUniqueIndex(string $table, string $column): bool
    {
        $result = DB::selectOne(
            'SELECT COUNT(*) AS c FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND NON_UNIQUE = 0',
            [$table, $column]
        );

        return $result !== null && (int) $result->c > 0;
    }
};