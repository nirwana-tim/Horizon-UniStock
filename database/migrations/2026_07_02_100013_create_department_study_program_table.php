<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table dropped by drop_department_study_program_table migration
    }

    public function down(): void
    {
        Schema::dropIfExists('department_study_program');
    }
};
