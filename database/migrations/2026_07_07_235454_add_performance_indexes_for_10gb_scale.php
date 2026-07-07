<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    private function addIndexSafely(string $table, string $indexName, array|string $columns): void
    {
        if (!Schema::hasTable($table)) return;
        try {
            Schema::table($table, fn(Blueprint $t) => $t->index((array) $columns, $indexName));
        } catch (\Illuminate\Database\QueryException $e) {
            // Ignore duplicate key errors — index already exists
            if (str_contains($e->getMessage(), '1061 Duplicate key name')) return;
            throw $e;
        }
    }

    public function up(): void
    {
        $this->addIndexSafely('items', 'idx_items_name', 'name');
        $this->addIndexSafely('students', 'idx_students_name', 'name');
        $this->addIndexSafely('students', 'idx_students_entitlement_code', 'entitlement_code');
        $this->addIndexSafely('distribution_schedules', 'idx_schedules_is_active', 'is_active');
        $this->addIndexSafely('distribution_schedules', 'idx_schedules_date', 'date');
        $this->addIndexSafely('distribution_schedules', 'idx_schedules_period', 'period');
        $this->addIndexSafely('entitlements', 'idx_entitlements_is_active', 'is_active');
        $this->addIndexSafely('stock_movements', 'idx_movements_type', 'type');
        $this->addIndexSafely('stock_movements', 'idx_movements_created_at', 'created_at');
        $this->addIndexSafely('distribution_items', 'idx_dist_items_transaction_item', ['transaction_id', 'item_id']);
        $this->addIndexSafely('stock_receive_items', 'idx_receive_items_receive_item', ['stock_receive_id', 'item_id']);
        $this->addIndexSafely('audit_logs', 'idx_audit_logs_created_at', 'created_at');
    }

    public function down(): void
    {
        $tables = ['items', 'students', 'distribution_schedules', 'entitlements', 'stock_movements', 'distribution_items', 'stock_receive_items', 'audit_logs'];
        $indexes = [
            'items' => ['idx_items_name'],
            'students' => ['idx_students_name', 'idx_students_entitlement_code'],
            'distribution_schedules' => ['idx_schedules_is_active', 'idx_schedules_date', 'idx_schedules_period'],
            'entitlements' => ['idx_entitlements_is_active'],
            'stock_movements' => ['idx_movements_type', 'idx_movements_created_at'],
            'distribution_items' => ['idx_dist_items_transaction_item'],
            'stock_receive_items' => ['idx_receive_items_receive_item'],
            'audit_logs' => ['idx_audit_logs_created_at'],
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
