<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambah kolom tahun_ajaran ke tabel-tabel yang perlu dipisahkan per tahun.
     */
    public function up(): void
    {
        $activeTahun = \DB::table('attendance_settings')
            ->where('key', 'active_tahun_ajaran')
            ->value('value') ?? '2026/2027';

        // attendance_students — siswa bisa punya record di banyak tahun ajaran
        Schema::table('attendance_students', function (Blueprint $table) use ($activeTahun) {
            $table->string('tahun_ajaran', 9)->default($activeTahun)->after('is_active');
            $table->index('tahun_ajaran', 'idx_students_tahun_ajaran');
        });

        // attendance_records — data absensi terpisah per tahun
        Schema::table('attendance_records', function (Blueprint $table) use ($activeTahun) {
            $table->string('tahun_ajaran', 9)->default($activeTahun)->after('notes');
            $table->index('tahun_ajaran', 'idx_records_tahun_ajaran');
        });

        // attendance_izin — izin terpisah per tahun
        Schema::table('attendance_izin', function (Blueprint $table) use ($activeTahun) {
            $table->string('tahun_ajaran', 9)->default($activeTahun)->after('status');
            $table->index('tahun_ajaran', 'idx_izin_tahun_ajaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendance_students', function (Blueprint $table) {
            $table->dropIndex('idx_students_tahun_ajaran');
            $table->dropColumn('tahun_ajaran');
        });

        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex('idx_records_tahun_ajaran');
            $table->dropColumn('tahun_ajaran');
        });

        Schema::table('attendance_izin', function (Blueprint $table) {
            $table->dropIndex('idx_izin_tahun_ajaran');
            $table->dropColumn('tahun_ajaran');
        });
    }
};
