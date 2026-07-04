<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Drop ALL FKs that might back the unique indexes ──
        // entitlements: FKs on study_program_id, program_level_id, period_id
        DB::statement('ALTER TABLE entitlements DROP FOREIGN KEY entitlements_study_program_id_foreign');
        DB::statement('ALTER TABLE entitlements DROP FOREIGN KEY entitlements_program_level_id_foreign');
        DB::statement('ALTER TABLE entitlements DROP FOREIGN KEY entitlements_period_id_foreign');

        // student_size_profiles: FKs on student_id, period_id
        DB::statement('ALTER TABLE student_size_profiles DROP FOREIGN KEY student_size_profiles_student_id_foreign');
        DB::statement('ALTER TABLE student_size_profiles DROP FOREIGN KEY student_size_profiles_period_id_foreign');

        // eligibility_records: FKs on student_id, period_id
        DB::statement('ALTER TABLE eligibility_records DROP FOREIGN KEY eligibility_records_student_id_foreign');
        DB::statement('ALTER TABLE eligibility_records DROP FOREIGN KEY eligibility_records_period_id_foreign');

        // item_prices: FKs on item_id, period_id
        DB::statement('ALTER TABLE item_prices DROP FOREIGN KEY item_prices_item_id_foreign');
        DB::statement('ALTER TABLE item_prices DROP FOREIGN KEY item_prices_period_id_foreign');

        // distribution_stages: FK on period_id
        DB::statement('ALTER TABLE distribution_stages DROP FOREIGN KEY distribution_stages_period_id_foreign');

        // distribution_schedules, distribution_transactions: FKs on stage_id
        DB::statement('ALTER TABLE distribution_schedules DROP FOREIGN KEY distribution_schedules_stage_id_foreign');
        DB::statement('ALTER TABLE distribution_transactions DROP FOREIGN KEY distribution_transactions_stage_id_foreign');

        // ── 2. Drop ALL unique indexes (now safe — all FKs released) ──
        DB::statement('ALTER TABLE entitlements DROP INDEX entitlement_unique');
        DB::statement('ALTER TABLE student_size_profiles DROP INDEX student_size_profiles_student_id_period_id_unique');
        DB::statement('ALTER TABLE eligibility_records DROP INDEX eligibility_records_student_id_period_id_unique');
        DB::statement('ALTER TABLE item_prices DROP INDEX item_prices_item_id_period_id_unique');

        // ── 3. Drop columns ──
        Schema::table('entitlements', fn (Blueprint $t) => $t->dropColumn('period_id'));
        Schema::table('eligibility_records', fn (Blueprint $t) => $t->dropColumn('period_id'));
        Schema::table('student_size_profiles', fn (Blueprint $t) => $t->dropColumn('period_id'));
        Schema::table('item_prices', fn (Blueprint $t) => $t->dropColumn('period_id'));
        Schema::table('distribution_schedules', fn (Blueprint $t) => $t->dropColumn('stage_id'));
        Schema::table('distribution_transactions', fn (Blueprint $t) => $t->dropColumn('stage_id'));

        // ── 4. Drop tables ──
        Schema::dropIfExists('distribution_stages');
        Schema::dropIfExists('distribution_periods');

        // ── 5. Add new columns to distribution_schedules ──
        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->foreignId('program_level_id')->nullable()->constrained('program_levels')->nullOnDelete()->after('session');
            $table->foreignId('faculty_id')->nullable()->constrained('faculties')->nullOnDelete()->after('program_level_id');
            $table->foreignId('study_program_id')->nullable()->constrained('study_programs')->nullOnDelete()->after('faculty_id');
        });

        // ── 6. Recreate FKs (without period_id) — match original FK behaviour ──
        Schema::table('entitlements', function (Blueprint $table) {
            $table->foreign('study_program_id')->references('id')->on('study_programs');
            $table->foreign('program_level_id')->references('id')->on('program_levels');
        });
        Schema::table('student_size_profiles', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });
        Schema::table('eligibility_records', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });
        Schema::table('item_prices', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });

        // ── 7. Add new unique constraints (without period_id) ──
        Schema::table('entitlements', fn (Blueprint $t) => $t->unique(['study_program_id', 'program_level_id', 'student_type'], 'entitlement_unique'));
        Schema::table('student_size_profiles', fn (Blueprint $t) => $t->unique('student_id'));
        Schema::table('eligibility_records', fn (Blueprint $t) => $t->unique('student_id'));
        Schema::table('item_prices', fn (Blueprint $t) => $t->unique('item_id'));
    }

    public function down(): void
    {
        // ── Reverse: drop new columns and constraints ──
        Schema::table('distribution_schedules', fn (Blueprint $t) => $t->dropForeignId('program_level_id'));
        Schema::table('distribution_schedules', fn (Blueprint $t) => $t->dropForeignId('faculty_id'));
        Schema::table('distribution_schedules', fn (Blueprint $t) => $t->dropForeignId('study_program_id'));

        Schema::table('entitlements', fn (Blueprint $t) => $t->dropUnique('entitlement_unique'));
        Schema::table('student_size_profiles', fn (Blueprint $t) => $t->dropUnique('student_id'));
        Schema::table('eligibility_records', fn (Blueprint $t) => $t->dropUnique('student_id'));
        Schema::table('item_prices', fn (Blueprint $t) => $t->dropUnique('item_id'));

        // Drop recreated FKs
        DB::statement('ALTER TABLE entitlements DROP FOREIGN KEY entitlements_study_program_id_foreign');
        DB::statement('ALTER TABLE entitlements DROP FOREIGN KEY entitlements_program_level_id_foreign');
        DB::statement('ALTER TABLE student_size_profiles DROP FOREIGN KEY student_size_profiles_student_id_foreign');
        DB::statement('ALTER TABLE eligibility_records DROP FOREIGN KEY eligibility_records_student_id_foreign');
        DB::statement('ALTER TABLE item_prices DROP FOREIGN KEY item_prices_item_id_foreign');

        // ── Re-create tables ──
        Schema::create('distribution_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamp('size_change_deadline')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('distribution_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('distribution_periods');
            $table->string('name');
            $table->integer('stage_order');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Add columns back with FKs ──
        Schema::table('entitlements', function (Blueprint $table) {
            $table->foreignId('period_id')->constrained('distribution_periods')->after('program_level_id');
        });
        Schema::table('eligibility_records', fn (Blueprint $t) => $t->foreignId('period_id')->constrained('distribution_periods')->after('student_id'));
        Schema::table('student_size_profiles', fn (Blueprint $t) => $t->foreignId('period_id')->constrained('distribution_periods')->after('student_id'));
        Schema::table('item_prices', fn (Blueprint $t) => $t->foreignId('period_id')->nullable()->constrained('distribution_periods')->nullOnDelete()->after('item_id'));
        Schema::table('distribution_schedules', fn (Blueprint $t) => $t->foreignId('stage_id')->constrained('distribution_stages')->after('name'));
        Schema::table('distribution_transactions', fn (Blueprint $t) => $t->foreignId('stage_id')->constrained('distribution_stages')->after('schedule_id'));

        // ── Restore original FKs ──
        Schema::table('entitlements', function (Blueprint $table) {
            $table->foreign('study_program_id')->references('id')->on('study_programs');
            $table->foreign('program_level_id')->references('id')->on('program_levels');
        });
        Schema::table('student_size_profiles', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });
        Schema::table('eligibility_records', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });
        Schema::table('item_prices', function (Blueprint $table) {
            $table->foreign('item_id')->references('id')->on('items')->cascadeOnDelete();
        });

        // ── Restore unique constraints ──
        Schema::table('item_prices', fn (Blueprint $t) => $t->unique(['item_id', 'period_id']));
        Schema::table('eligibility_records', fn (Blueprint $t) => $t->unique(['student_id', 'period_id']));
        Schema::table('student_size_profiles', fn (Blueprint $t) => $t->unique(['student_id', 'period_id']));
        Schema::table('entitlements', fn (Blueprint $t) => $t->unique(['study_program_id', 'program_level_id', 'period_id', 'student_type'], 'entitlement_unique'));
    }
};
