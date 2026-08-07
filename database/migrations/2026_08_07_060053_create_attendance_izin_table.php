<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_izin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('attendance_students')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('jenis', ['izin', 'sakit'])->default('izin');
            $table->text('alasan');
            $table->string('nama_pelapor')->nullable();   // nama ortu/wali
            $table->string('no_hp_pelapor')->nullable();  // WA ortu
            $table->string('lampiran')->nullable();       // path file surat/foto
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();    // catatan saat approve/tolak
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'tanggal_mulai']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_izin');
    }
};
