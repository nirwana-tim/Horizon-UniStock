<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->comment('Mahasiswa penerima');
            $table->foreignId('schedule_id')->constrained('distribution_schedules')->comment('Jadwal terkait');
            $table->string('type')->comment('Tipe email (event_invite/credentials/password_reset)');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending')->comment('Status');
            $table->timestamp('sent_at')->nullable()->comment('Waktu email terkirim');
            $table->text('error_message')->nullable()->comment('Pesan error jika gagal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_notifications');
    }
};
