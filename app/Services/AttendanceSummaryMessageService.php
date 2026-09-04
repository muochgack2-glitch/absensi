<?php

namespace App\Services;

use App\Models\AttendanceSetting;

/**
 * Centralized WA message builder for all ringkasan roles.
 * Used by Console Commands AND the settings preview page.
 * Ubah format pesan di sini -> otomatis sinkron di preview.
 */
class AttendanceSummaryMessageService
{
    // =========================================================
    // WALI KELAS
    // =========================================================

    public static function buildWaliMasuk(
        string $namaKelas, string $tanggal, int $total,
        int $hadirTepat, int $terlambat, int $izin, int $sakit, int $alfa,
        array $alfaStudents = []
    ): string {
        $hadir  = $hadirTepat + $terlambat;
        $persen = $total > 0 ? round(($hadir / $total) * 100) : 0;

        $lines = [
            "📋 *RINGKASAN KEHADIRAN MASUK*",
            "📚 Kelas  : *{$namaKelas}*",
            "📅 Tanggal: {$tanggal}",
            "",
            "👥 Total             : {$total} siswa",
            "✅ Hadir tepat waktu : {$hadirTepat} siswa",
            "⏰ Terlambat         : {$terlambat} siswa",
            "📝 Izin              : {$izin} siswa",
            "🤒 Sakit             : {$sakit} siswa",
            "❌ Alfa              : {$alfa} siswa",
            "📊 Kehadiran         : {$persen}%",
        ];

        if (!empty($alfaStudents)) {
            $lines[] = "";
            $lines[] = "*Siswa tidak hadir (alfa):*";
            foreach ($alfaStudents as $i => $nama) {
                $lines[] = ($i + 1) . ". {$nama}";
            }
        }

        $lines[] = "";
        $lines[] = "_Sistem Absensi SMK PGRI Blora_";

        return implode("\n", $lines);
    }

    public static function buildWaliPulang(
        string $namaKelas, string $tanggal, int $total,
        int $hadir, int $izin, int $sakit, int $alfa,
        int $pulangTepat, int $pulangCepat, int $belumPulang,
        array $belumPulangStudents = [],
        array $pulangCepatStudents = []
    ): string {
        $lines = [
            "🌆 *RINGKASAN KEPULANGAN*",
            "📚 Kelas  : *{$namaKelas}*",
            "📅 Tanggal: {$tanggal}",
            "",
            "👥 Total              : {$total} siswa",
            "🏫 Hadir hari ini     : {$hadir} siswa",
            "✅ Pulang tepat waktu : {$pulangTepat} siswa",
            "⚡ Pulang lebih awal  : {$pulangCepat} siswa",
            "⏳ Belum pulang       : {$belumPulang} siswa",
            "📝 Izin               : {$izin} siswa",
            "🤒 Sakit              : {$sakit} siswa",
            "❌ Alfa               : {$alfa} siswa",
        ];

        if (!empty($pulangCepatStudents)) {
            $lines[] = "";
            $lines[] = "*Siswa pulang lebih awal:*";
            foreach ($pulangCepatStudents as $i => $nama) {
                $lines[] = ($i + 1) . ". {$nama}";
            }
        }

        if (!empty($belumPulangStudents)) {
            $lines[] = "";
            $lines[] = "*Siswa belum pulang:*";
            foreach ($belumPulangStudents as $i => $nama) {
                $lines[] = ($i + 1) . ". {$nama}";
            }
        }

        $lines[] = "";
        $lines[] = "_Sistem Absensi SMK PGRI Blora_";

        return implode("\n", $lines);
    }

    // =========================================================
    // WAKA & KEPSEK (format identik)
    // =========================================================

    public static function buildWakaMasuk(
        string $dayName, int $totalSiswa, int $hadir, int $terlambat,
        int $alpha, int $izin, int $sakit, float $persen, string $status,
        array $alphaPerKelas = [], array $terlambatPerKelas = []
    ): string {
        $schoolName = AttendanceSetting::get('school_name', 'SMK');
        $hadirTepat = $hadir - $terlambat;

        $lines = [
            "📊 *LAPORAN KEHADIRAN HARIAN*",
            "*{$schoolName}*",
            $dayName, "",
            "👥 Total Siswa   : {$totalSiswa} orang",
            "✅ Hadir         : {$hadir} ({$persen}%)",
            "   ↳ Tepat waktu : {$hadirTepat} siswa",
            "   ↳ Terlambat   : {$terlambat} siswa",
            "❌ Alpha         : {$alpha} siswa",
            "📋 Izin          : {$izin} siswa",
            "🤒 Sakit         : {$sakit} siswa",
            "", "Status: {$status}",
        ];

        if (!empty($terlambatPerKelas)) {
            $lines[] = ""; $lines[] = "*Detail Siswa Terlambat:*";
            foreach ($terlambatPerKelas as $kelas => $data) {
                $lines[] = ""; $lines[] = "📚 *{$kelas}*";
                $lines[] = "   Wali Kelas: {$data['wali']}";
                foreach ($data['siswa'] as $i => $n) { $lines[] = "   ".($i+1).". {$n}"; }
            }
        }

        if (!empty($alphaPerKelas)) {
            $lines[] = ""; $lines[] = "*Detail Siswa Alpha:*";
            foreach ($alphaPerKelas as $kelas => $data) {
                $lines[] = ""; $lines[] = "📚 *{$kelas}*";
                $lines[] = "   Wali Kelas: {$data['wali']}";
                foreach ($data['siswa'] as $i => $n) { $lines[] = "   ".($i+1).". {$n}"; }
            }
        }

        $lines[] = ""; $lines[] = "_Sistem Absensi Otomatis_";
        return implode("\n", $lines);
    }

    public static function buildWakaPulang(
        string $dayName, int $totalSiswa, int $hadir,
        int $sudahPulang, int $pulangCepat, int $belumPulang,
        array $belumPulangPerKelas = [], array $pulangCepatPerKelas = []
    ): string {
        $schoolName  = AttendanceSetting::get('school_name', 'SMK');
        $pulangTepat = $sudahPulang - $pulangCepat;

        $lines = [
            "🌆 *LAPORAN KEPULANGAN HARIAN*",
            "*{$schoolName}*",
            $dayName, "",
            "👥 Total Siswa     : {$totalSiswa} orang",
            "🏫 Hadir hari ini  : {$hadir} siswa",
            "✅ Sudah pulang    : {$sudahPulang} siswa",
            "   ↳ Tepat waktu  : {$pulangTepat} siswa",
            "   ↳ Pulang cepat : {$pulangCepat} siswa",
            "⏳ Belum pulang   : {$belumPulang} siswa",
        ];

        if (!empty($pulangCepatPerKelas)) {
            $lines[] = ""; $lines[] = "*Detail Pulang Cepat:*";
            foreach ($pulangCepatPerKelas as $kelas => $data) {
                $lines[] = ""; $lines[] = "📚 *{$kelas}*";
                $lines[] = "   Wali Kelas: {$data['wali']}";
                foreach ($data['siswa'] as $i => $n) { $lines[] = "   ".($i+1).". {$n}"; }
            }
        }

        if (!empty($belumPulangPerKelas)) {
            $lines[] = ""; $lines[] = "*Detail Belum Pulang:*";
            foreach ($belumPulangPerKelas as $kelas => $data) {
                $lines[] = ""; $lines[] = "📚 *{$kelas}*";
                $lines[] = "   Wali Kelas: {$data['wali']}";
                foreach ($data['siswa'] as $i => $n) { $lines[] = "   ".($i+1).". {$n}"; }
            }
        }

        $lines[] = ""; $lines[] = "_Sistem Absensi Otomatis_";
        return implode("\n", $lines);
    }

    // =========================================================
    // PREVIEW dengan data dummy (dipanggil controller)
    // =========================================================

    public static function previewWaliMasuk(): string
    {
        return self::buildWaliMasuk('[Nama Kelas]', '[Hari, DD Bulan YYYY]',
            30, 25, 2, 1, 1, 1, ['Budi Santoso', 'Ani Rahayu']);
    }

    public static function previewWaliPulang(): string
    {
        return self::buildWaliPulang('[Nama Kelas]', '[Hari, DD Bulan YYYY]',
            30, 27, 1, 1, 1, 20, 3, 4,
            ['Budi Santoso', 'Ani Rahayu', 'Doni Pratama'],
            ['Siti Nurbaya']
        );
    }

    public static function previewWakaMasuk(): string
    {
        return self::buildWakaMasuk('[Hari, DD Bulan YYYY]',
            400, 380, 10, 8, 7, 5, 95.0, '🟢 Baik',
            ['X AKL' => ['wali' => 'Pak Budi', 'siswa' => ['Budi Santoso', 'Ani Rahayu']]],
            ['X AKL' => ['wali' => 'Pak Budi', 'siswa' => ['Doni Pratama']]]
        );
    }

    public static function previewWakaPulang(): string
    {
        return self::buildWakaPulang('[Hari, DD Bulan YYYY]',
            400, 380, 360, 15, 20,
            ['X AKL'  => ['wali' => 'Pak Budi', 'siswa' => ['Budi Santoso']]],
            ['XI TKJ' => ['wali' => 'Bu Sari',  'siswa' => ['Doni Pratama']]]
        );
    }
}
