<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distribution_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('distribution_schedules', 'stage_id')) {
                $table->dropForeign(['stage_id']);
                $table->dropColumn('stage_id');
            }
        });

        Schema::dropIfExists('distribution_stages');
    }

    public function down(): void
    {
        Schema::create('distribution_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('stage_order')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('distribution_schedules', function (Blueprint $table) {
            $table->foreignId('stage_id')->nullable()->after('semester')
                ->constrained('distribution_stages')->nullOnDelete();
        });
    }
};
