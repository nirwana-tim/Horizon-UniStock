<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('size_change_events', function (Blueprint $table) {
            if (!Schema::hasIndex('size_change_events', 'size_change_events_is_active_index')) {
                $table->index('is_active');
            }
            if (!Schema::hasIndex('size_change_events', 'size_change_events_is_active_start_date_end_date_index')) {
                $table->index(['is_active', 'start_date', 'end_date']);
            }
        });

        Schema::table('otp_codes', function (Blueprint $table) {
            if (!Schema::hasIndex('otp_codes', 'otp_codes_email_code_type_expires_at_index')) {
                $table->index(['email', 'code', 'type', 'expires_at']);
            }
            if (!Schema::hasIndex('otp_codes', 'otp_codes_user_id_used_at_expires_at_index')) {
                $table->index(['user_id', 'used_at', 'expires_at']);
            }
        });

        Schema::table('email_notifications', function (Blueprint $table) {
            if (!Schema::hasIndex('email_notifications', 'email_notifications_type_status_index')) {
                $table->index(['type', 'status']);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasIndex('users', 'users_must_change_password_index')) {
                $table->index('must_change_password');
            }
        });

        Schema::table('distribution_transactions', function (Blueprint $table) {
            if (!Schema::hasIndex('distribution_transactions', 'distribution_transactions_student_id_schedule_id_status_index')) {
                $table->index(['student_id', 'schedule_id', 'status']);
            }
        });

        Schema::table('distribution_schedules', function (Blueprint $table) {
            if (!Schema::hasIndex('distribution_schedules', 'distribution_schedules_is_active_date_index')) {
                $table->index(['is_active', 'date']);
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasIndex('stock_movements', 'stock_movements_item_id_variant_id_type_index')) {
                $table->index(['item_id', 'variant_id', 'type']);
            }
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            if (!Schema::hasIndex('stock_batches', 'stock_batches_item_id_variant_id_received_date_index')) {
                $table->index(['item_id', 'variant_id', 'received_date']);
            }
        });

        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasIndex('items', 'items_gender_index')) {
                $table->index('gender');
            }
        });

        Schema::table('entitlements', function (Blueprint $table) {
            if (!Schema::hasIndex('entitlements', 'entitlements_is_active_index')) {
                $table->index('is_active');
            }
        });

        Schema::table('stock_balances', function (Blueprint $table) {
            if (Schema::hasIndex('stock_balances', 'stock_balances_item_id_index')) {
                $table->dropIndex('stock_balances_item_id_index');
            }
            if (!Schema::hasIndex('stock_balances', 'stock_balances_quantity_index')) {
                $table->index('quantity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('size_change_events', function (Blueprint $table) {
            if (Schema::hasIndex('size_change_events', 'size_change_events_is_active_index')) {
                $table->dropIndex('size_change_events_is_active_index');
            }
            if (Schema::hasIndex('size_change_events', 'size_change_events_is_active_start_date_end_date_index')) {
                $table->dropIndex('size_change_events_is_active_start_date_end_date_index');
            }
        });

        Schema::table('otp_codes', function (Blueprint $table) {
            if (Schema::hasIndex('otp_codes', 'otp_codes_email_code_type_expires_at_index')) {
                $table->dropIndex('otp_codes_email_code_type_expires_at_index');
            }
            if (Schema::hasIndex('otp_codes', 'otp_codes_user_id_used_at_expires_at_index')) {
                $table->dropIndex('otp_codes_user_id_used_at_expires_at_index');
            }
        });

        Schema::table('email_notifications', function (Blueprint $table) {
            if (Schema::hasIndex('email_notifications', 'email_notifications_type_status_index')) {
                $table->dropIndex('email_notifications_type_status_index');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'users_must_change_password_index')) {
                $table->dropIndex('users_must_change_password_index');
            }
        });

        Schema::table('distribution_transactions', function (Blueprint $table) {
            if (Schema::hasIndex('distribution_transactions', 'distribution_transactions_student_id_schedule_id_status_index')) {
                $table->dropIndex('distribution_transactions_student_id_schedule_id_status_index');
            }
        });

        Schema::table('distribution_schedules', function (Blueprint $table) {
            if (Schema::hasIndex('distribution_schedules', 'distribution_schedules_is_active_date_index')) {
                $table->dropIndex('distribution_schedules_is_active_date_index');
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasIndex('stock_movements', 'stock_movements_item_id_variant_id_type_index')) {
                $table->dropIndex('stock_movements_item_id_variant_id_type_index');
            }
        });

        Schema::table('stock_batches', function (Blueprint $table) {
            if (Schema::hasIndex('stock_batches', 'stock_batches_item_id_variant_id_received_date_index')) {
                $table->dropIndex('stock_batches_item_id_variant_id_received_date_index');
            }
        });

        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasIndex('items', 'items_gender_index')) {
                $table->dropIndex('items_gender_index');
            }
        });

        Schema::table('entitlements', function (Blueprint $table) {
            if (Schema::hasIndex('entitlements', 'entitlements_is_active_index')) {
                $table->dropIndex('entitlements_is_active_index');
            }
        });

        Schema::table('stock_balances', function (Blueprint $table) {
            if (!Schema::hasIndex('stock_balances', 'stock_balances_item_id_index')) {
                $table->index('item_id');
            }
            if (Schema::hasIndex('stock_balances', 'stock_balances_quantity_index')) {
                $table->dropIndex('stock_balances_quantity_index');
            }
        });
    }
};
