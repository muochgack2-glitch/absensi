<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah unique constraint: nis saja → nis + tahun_ajaran
     * Agar siswa bisa ada di beberapa tahun ajaran dengan NIS yang sama.
     */
    public function up(): void
    {
        Schema::table('attendance_students', function (Blueprint $table) {
            // Hapus unique index lama (nis saja)
            $table->dropUnique('attendance_students_nis_unique');

            // Buat unique composite: nis + tahun_ajaran
            $table->unique(['nis', 'tahun_ajaran'], 'attendance_students_nis_tahun_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_students', function (Blueprint $table) {
            $table->dropUnique('attendance_students_nis_tahun_unique');
            $table->unique('nis', 'attendance_students_nis_unique');
        });
    }
};
