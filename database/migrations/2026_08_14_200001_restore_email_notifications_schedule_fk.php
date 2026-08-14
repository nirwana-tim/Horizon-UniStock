<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('email_notifications') && Schema::hasTable('distribution_schedules')) {
            $fkName = DB::selectOne(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_notifications' AND COLUMN_NAME = 'schedule_id' AND REFERENCED_TABLE_NAME = 'distribution_schedules'"
            );

            if (! $fkName) {
                DB::statement('UPDATE email_notifications SET schedule_id = NULL WHERE schedule_id IS NOT NULL AND schedule_id NOT IN (SELECT id FROM distribution_schedules)');
                DB::statement('ALTER TABLE email_notifications ADD CONSTRAINT fk_email_notifications_schedule_id FOREIGN KEY (schedule_id) REFERENCES distribution_schedules(id) ON DELETE SET NULL');
            }
        }
    }

    public function down(): void
    {
        $fkName = DB::selectOne(
            "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'email_notifications' AND COLUMN_NAME = 'schedule_id' AND REFERENCED_TABLE_NAME = 'distribution_schedules'"
        );

        if ($fkName) {
            DB::statement("ALTER TABLE email_notifications DROP FOREIGN KEY `{$fkName->CONSTRAINT_NAME}`");
        }
    }
};
