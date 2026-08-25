<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom no_hp_ortu2 (nomor HP wali/alternatif) ke tabel attendance_students.
     * Notifikasi WA akan dikirim ke semua nomor yang terisi.
     */
    public function up(): void
    {
        Schema::table('attendance_students', function (Blueprint $table) {
            $table->string('no_hp_ortu2')->nullable()->after('no_hp_ortu')
                  ->comment('Nomor HP wali/alternatif — jika diisi, notifikasi dikirim ke 2 nomor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_students', function (Blueprint $table) {
            $table->dropColumn('no_hp_ortu2');
        });
    }
};
