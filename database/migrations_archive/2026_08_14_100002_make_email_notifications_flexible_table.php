<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE email_notifications MODIFY COLUMN status VARCHAR(20) DEFAULT \'pending\'');

        $fkName = $this->getForeignKeyName('email_notifications', 'schedule_id');
        if ($fkName) {
            DB::statement("ALTER TABLE email_notifications DROP FOREIGN KEY `{$fkName}`");
        }
        DB::statement('ALTER TABLE email_notifications MODIFY COLUMN schedule_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE email_notifications MODIFY COLUMN status ENUM('pending','sent','failed','skipped') DEFAULT 'pending'");

        $fkName = $this->getForeignKeyName('email_notifications', 'schedule_id');
        if (! $fkName) {
            DB::statement('ALTER TABLE email_notifications MODIFY COLUMN schedule_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE email_notifications ADD CONSTRAINT fk_email_notifications_schedule_id FOREIGN KEY (schedule_id) REFERENCES distribution_schedules(id)');
        } else {
            DB::statement('ALTER TABLE email_notifications MODIFY COLUMN schedule_id BIGINT UNSIGNED NOT NULL');
            DB::statement("ALTER TABLE email_notifications ADD CONSTRAINT `{$fkName}` FOREIGN KEY (schedule_id) REFERENCES distribution_schedules(id)");
        }
    }

    private function getForeignKeyName(string $table, string $column): ?string
    {
        $row = DB::selectOne(
            "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$table, $column]
        );

        return $row?->CONSTRAINT_NAME;
    }
};
