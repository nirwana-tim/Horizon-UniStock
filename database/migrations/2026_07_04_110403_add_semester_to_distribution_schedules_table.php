<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->string('semester', 10)->nullable()->after('period')->comment('Ganjil / Genap');
        });
    }

    public function down(): void
    {
        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
