<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function addIndexSafely(string $table, string $indexName, array|string $columns): void
    {
        if (!Schema::hasTable($table)) return;
        try {
            Schema::table($table, fn(Blueprint $t) => $t->index((array) $columns, $indexName));
        } catch (\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), '1061 Duplicate key name')) return;
            throw $e;
        }
    }

    public function up(): void
    {
        $this->addIndexSafely('items', 'idx_items_base_code', 'base_code');
        $this->addIndexSafely('students', 'idx_students_user_id', 'user_id');
        $this->addIndexSafely('audit_logs', 'idx_audit_logs_user_id', 'user_id');
        $this->addIndexSafely('item_prices', 'idx_item_prices_effective_date', 'effective_date');
    }

    public function down(): void
    {
        $tables = ['items', 'students', 'audit_logs', 'item_prices'];
        $indexes = [
            'items' => ['idx_items_base_code'],
            'students' => ['idx_students_user_id'],
            'audit_logs' => ['idx_audit_logs_user_id'],
            'item_prices' => ['idx_item_prices_effective_date'],
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table, $indexes) {
                    foreach ($indexes[$table] as $index) {
                        $t->dropIndex($index);
                    }
                });
            }
        }
    }
};
