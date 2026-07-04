<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop all FKs on entitlements (unique index backs them)
        DB::statement('ALTER TABLE entitlements DROP FOREIGN KEY entitlements_study_program_id_foreign');
        DB::statement('ALTER TABLE entitlements DROP FOREIGN KEY entitlements_program_level_id_foreign');

        // Drop old unique constraint, add semester, add new unique constraint with semester
        DB::statement('ALTER TABLE entitlements DROP INDEX entitlement_unique');

        Schema::table('entitlements', function (Blueprint $table) {
            $table->string('semester', 10)->nullable()->after('student_type')->comment('Ganjil / Genap');
        });

        // Update existing data to 'ganjil' as default
        DB::table('entitlements')->update(['semester' => 'ganjil']);

        Schema::table('entitlements', function (Blueprint $table) {
            $table->string('semester', 10)->nullable(false)->change();
        });

        // Add new unique constraint with semester
        DB::statement('ALTER TABLE entitlements ADD UNIQUE INDEX entitlement_unique (study_program_id, program_level_id, student_type, semester)');

        // Recreate FKs
        Schema::table('entitlements', function (Blueprint $table) {
            $table->foreign('study_program_id')->references('id')->on('study_programs');
            $table->foreign('program_level_id')->references('id')->on('program_levels');
        });
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE entitlements DROP FOREIGN KEY entitlements_study_program_id_foreign');
        DB::statement('ALTER TABLE entitlements DROP FOREIGN KEY entitlements_program_level_id_foreign');

        DB::statement('ALTER TABLE entitlements DROP INDEX entitlement_unique');
        DB::statement('ALTER TABLE entitlements ADD UNIQUE INDEX entitlement_unique (study_program_id, program_level_id, student_type)');

        Schema::table('entitlements', function (Blueprint $table) {
            $table->dropColumn('semester');
        });

        Schema::table('entitlements', function (Blueprint $table) {
            $table->foreign('study_program_id')->references('id')->on('study_programs');
            $table->foreign('program_level_id')->references('id')->on('program_levels');
        });
    }
};
