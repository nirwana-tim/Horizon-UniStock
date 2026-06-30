<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eligibility_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete()->comment('Mahasiswa terkait');
            $table->foreignId('period_id')->constrained('distribution_periods')->comment('Periode distribusi');
            $table->boolean('is_eligible')->default(false)->comment('Status kelayakan');
            $table->string('payment_status')->default('belum')->comment('Status pembayaran (lunas/belum/cicilan)');
            $table->timestamps();

            $table->unique(['student_id', 'period_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eligibility_records');
    }
};
