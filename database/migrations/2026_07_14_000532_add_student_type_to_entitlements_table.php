<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('entitlements', function (Blueprint $table) {
            $table->string('student_type', 20)->nullable()->after('code')->comment('Jenis mahasiswa: freshman/continuing');
        });
    }

    public function down(): void
    {
        Schema::table('entitlements', function (Blueprint $table) {
            $table->dropColumn('student_type');
        });
    }
};
