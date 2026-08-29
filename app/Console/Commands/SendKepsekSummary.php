<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Models\AttendanceSetting;
use App\Models\Holiday;
use App\Models\User;
use App\Services\AttendanceWhatsAppService;
use Carbon\Carbon;

class SendKepsekSummary extends Command
{
    protected $signature = 'attendance:send-kepsek-summary
                            {--date=      : Tanggal (Y-m-d, default: hari ini)}
                            {--type=masuk : Jenis: masuk atau pulang}
                            {--dry-run    : Tampilkan pesan tanpa benar-benar mengirim}';

    protected $description = 'Kirim laporan kehadiran ringkas (executive summary) ke Kepala Sekolah via WhatsApp';

    public function __construct(
        protected AttendanceWhatsAppService $waService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $date   = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : Carbon::today()->timezone('Asia/Jakarta')->toDateString();

        $dryRun  = $this->option('dry-run');
        $type    = $this->option('type') ?? 'masuk';
        $dayName = Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y');
        $label   = $type === 'pulang' ? '🌆 Rekap Pulang Kepsek' : '📊 Rekap Masuk Kepsek';

        $this->info("{$label}: {$dayName}");
        if ($dryRun) $this->warn('-- DRY RUN --');

        // Hitung statistik seluruh sekolah
        $totalSiswa = AttendanceStudent::where('is_active', true)->count();

        $stats = AttendanceRecord::withoutGlobalScope('tahun_ajaran')
            ->whereDate('date', $date)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $hadir     = ($stats['hadir'] ?? 0) + ($stats['terlambat'] ?? 0);
        $terlambat = $stats['terlambat'] ?? 0;
        $alpha     = $stats['alpha']     ?? 0;
        $izin      = $stats['izin']      ?? 0;
        $sakit     = $stats['sakit']     ?? 0;

        if ($type === 'pulang') {
            $records     = AttendanceRecord::withoutGlobalScope('tahun_ajaran')->whereDate('date', $date);
            $sudahPulang = (clone $records)->whereNotNull('check_out_time')->count();
            $pulangCepat = (clone $records)->where('check_out_status', 'pulang_cepat')->count();
            $belumPulang = max(0, $hadir - $sudahPulang);
            $message     = $this->buildPulangMessage($dayName, $totalSiswa, $hadir, $sudahPulang, $pulangCepat, $belumPulang);
        } else {
            $persen  = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100, 1) : 0;
            $status  = match(true) {
                $persen >= 95 => '🟢 BAIK',
                $persen >= 85 => '🟡 PERLU PERHATIAN',
                default       => '🔴 RENDAH',
            };
            $message = $this->buildMasukMessage($dayName, $totalSiswa, $hadir, $terlambat, $alpha, $izin, $sakit, $persen, $status);
        }

        $this->line('');
        $this->line($message);
        $this->line('');

        if ($dryRun) {
            $this->info('Pesan TIDAK dikirim (dry-run).');
            return Command::SUCCESS;
        }

        // Ambil semua user kepala_sekolah yang punya nomor HP
        $kepsekUsers = User::where('role', 'kepala_sekolah')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($kepsekUsers->isEmpty()) {
            $this->warn('Tidak ada user kepala_sekolah dengan nomor HP yang dikonfigurasi.');
            return Command::SUCCESS;
        }

        $sent = $failed = 0;
        foreach ($kepsekUsers as $kepsek) {
            try {
                $result = $this->waService->send($kepsek->phone, $message, ['type' => "kepsek-{$type}", 'sent_by' => null]);
                if ($result['success'] ?? false) {
                    $this->info("Terkirim ke {$kepsek->name} ({$kepsek->phone})");
                    $sent++;
                } else {
                    $this->error("Gagal ke {$kepsek->name}: " . ($result['message'] ?? 'unknown'));
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("Error [{$kepsek->name}]: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("Selesai. Terkirim:{$sent} Gagal:{$failed}");
        return Command::SUCCESS;
    }

    protected function buildMasukMessage(
        string $dayName, int $totalSiswa, int $hadir, int $terlambat,
        int $alpha, int $izin, int $sakit, float $persen, string $status
    ): string {
        $schoolName = AttendanceSetting::get('school_name', 'SMK');
        return implode("\n", [
            "📊 *LAPORAN KEHADIRAN HARIAN*",
            "*{$schoolName}*", $dayName, "",
            "👥 Total Siswa   : {$totalSiswa} orang",
            "✅ Hadir         : {$hadir} ({$persen}%)",
            "   ↳ Tepat waktu : " . ($hadir - $terlambat) . " siswa",
            "   ↳ Terlambat   : {$terlambat} siswa",
            "❌ Alpha         : {$alpha} siswa",
            "📋 Izin          : {$izin} siswa",
            "🤒 Sakit         : {$sakit} siswa",
            "", "Status: {$status}", "",
            "_Sistem Absensi Otomatis_",
        ]);
    }

    protected function buildPulangMessage(
        string $dayName, int $totalSiswa, int $hadir,
        int $sudahPulang, int $pulangCepat, int $belumPulang
    ): string {
        $schoolName  = AttendanceSetting::get('school_name', 'SMK');
        return implode("\n", [
            "🌆 *LAPORAN KEPULANGAN HARIAN*",
            "*{$schoolName}*", $dayName, "",
            "👥 Total Siswa     : {$totalSiswa} orang",
            "🏫 Hadir hari ini  : {$hadir} siswa",
            "✅ Sudah pulang    : {$sudahPulang} siswa",
            "   ↳ Tepat waktu  : " . ($sudahPulang - $pulangCepat) . " siswa",
            "   ↳ Pulang cepat : {$pulangCepat} siswa",
            "⏳ Belum pulang   : {$belumPulang} siswa",
            "", "_Sistem Absensi Otomatis_",
        ]);
    }
}
