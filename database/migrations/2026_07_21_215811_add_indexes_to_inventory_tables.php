<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_balances', function (Blueprint $table) {
            if (!Schema::hasIndex('stock_balances', 'stock_balances_item_id_index')) {
                $table->index('item_id');
            }
            if (!Schema::hasIndex('stock_balances', 'stock_balances_variant_id_index')) {
                $table->index('variant_id');
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasIndex('stock_movements', 'stock_movements_reference_type_reference_id_index')) {
                $table->index(['reference_type', 'reference_id']);
            }
            if (!Schema::hasIndex('stock_movements', 'stock_movements_item_id_variant_id_index')) {
                $table->index(['item_id', 'variant_id']);
            }
        });

        Schema::table('distribution_transactions', function (Blueprint $table) {
            if (!Schema::hasIndex('distribution_transactions', 'distribution_transactions_student_id_schedule_id_index')) {
                $table->index(['student_id', 'schedule_id']);
            }
            if (!Schema::hasIndex('distribution_transactions', 'distribution_transactions_staff_id_index')) {
                $table->index('staff_id');
            }
        });

        Schema::table('distribution_items', function (Blueprint $table) {
            if (!Schema::hasIndex('distribution_items', 'distribution_items_transaction_id_item_id_index')) {
                $table->index(['transaction_id', 'item_id']);
            }
        });

        Schema::table('distribution_schedules', function (Blueprint $table) {
            if (!Schema::hasIndex('distribution_schedules', 'distribution_schedules_period_is_active_index')) {
                $table->index(['period', 'is_active']);
            }
        });

        Schema::table('eligibility_records', function (Blueprint $table) {
            if (!Schema::hasIndex('eligibility_records', 'eligibility_records_is_eligible_index')) {
                $table->index('is_eligible');
            }
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            if (!Schema::hasIndex('audit_logs', 'audit_logs_model_type_model_id_index')) {
                $table->index(['model_type', 'model_id']);
            }
            if (Schema::hasColumn('audit_logs', 'user_id')) {
                try {
                    $table->unsignedBigInteger('user_id')->nullable()->change();
                } catch (\Exception $e) {
                    // Column may already be nullable
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_balances', function (Blueprint $table) {
            $table->dropIndex(['item_id']);
            $table->dropIndex(['variant_id']);
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['reference_type', 'reference_id']);
            $table->dropIndex(['item_id', 'variant_id']);
        });

        Schema::table('distribution_transactions', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'schedule_id']);
            $table->dropIndex(['staff_id']);
        });

        Schema::table('distribution_items', function (Blueprint $table) {
            $table->dropIndex(['transaction_id', 'item_id']);
        });

        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->dropIndex(['period', 'is_active']);
        });

        Schema::table('eligibility_records', function (Blueprint $table) {
            $table->dropIndex('is_eligible');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['model_type', 'model_id']);
        });
    }

    private function isColumnNullable(string $table, string $column): bool
    {
        return Schema::hasColumn($table, $column)
            && app('db')->connection()->getDoctrineColumn($table, $column)->getNotnull() === false;
    }
};
