<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('students', 'qr_token') && Schema::hasIndex('students', ['qr_token'], 'unique')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropUnique('students_qr_token_unique');
            });
        }

        if (Schema::hasColumn('students', 'qr_token')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('qr_token');
            });
        }

        if (Schema::hasColumn('students', 'qr_generated_at')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('qr_generated_at');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('students', 'qr_token')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('qr_token')->unique()->nullable();
            });
        }

        if (! Schema::hasColumn('students', 'qr_generated_at')) {
            Schema::table('students', function (Blueprint $table) {
                $table->timestamp('qr_generated_at')->nullable();
            });
        }
    }

};
