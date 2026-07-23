<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration ran on old student_types table.
        // Since we renamed the table to student_levels in the create migration,
        // this migration is now a no-op placeholder.
        // All data is seeded directly via StudentLevelSeeder.
    }

    public function down(): void
    {
    }
};
