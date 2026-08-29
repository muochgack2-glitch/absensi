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
                            {--date=    : Tanggal (Y-m-d, default: hari ini)}
                            {--dry-run  : Tampilkan pesan tanpa benar-benar mengirim}';

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
        $dayName = Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y');

        $this->info("📊 Rekap Kepsek: {$dayName}");
        if ($dryRun) $this->warn('-- DRY RUN --');

        // Hitung statistik seluruh sekolah
        $totalSiswa = AttendanceStudent::where('is_active', true)->count();

        $stats = AttendanceRecord::withoutGlobalScope('tahun_ajaran')
            ->whereDate('date', $date)
            ->selectRaw('status, COUNT(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $hadir      = ($stats['hadir']    ?? 0) + ($stats['terlambat'] ?? 0);
        $terlambat  = $stats['terlambat'] ?? 0;
        $alpha      = $stats['alpha']     ?? 0;
        $izin       = $stats['izin']      ?? 0;
        $sakit      = $stats['sakit']     ?? 0;

        $persen = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100, 1) : 0;

        $status = match(true) {
            $persen >= 95 => '🟢 BAIK',
            $persen >= 85 => '🟡 PERLU PERHATIAN',
            default       => '🔴 RENDAH',
        };

        $message = $this->buildMessage($dayName, $totalSiswa, $hadir, $terlambat, $alpha, $izin, $sakit, $persen, $status);

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
                $result = $this->waService->send($kepsek->phone, $message, ['type' => 'kepsek-summary', 'sent_by' => null]);
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

    protected function buildMessage(
        string $dayName,
        int $totalSiswa,
        int $hadir,
        int $terlambat,
        int $alpha,
        int $izin,
        int $sakit,
        float $persen,
        string $status
    ): string {
        $schoolName = AttendanceSetting::get('school_name', 'SMK');
        $hadirTepat = $hadir - $terlambat;

        $lines = [
            "📊 *LAPORAN KEHADIRAN HARIAN*",
            "*{$schoolName}*",
            $dayName,
            "",
            "👥 Total Siswa   : {$totalSiswa} orang",
            "✅ Hadir         : {$hadir} ({$persen}%)",
            "   ↳ Tepat waktu : {$hadirTepat} siswa",
            "   ↳ Terlambat   : {$terlambat} siswa",
            "❌ Alpha         : {$alpha} siswa",
            "📋 Izin          : {$izin} siswa",
            "🤒 Sakit         : {$sakit} siswa",
            "",
            "Status: {$status}",
            "",
            "_Sistem Absensi Otomatis_",
        ];

        return implode("\n", $lines);
    }
}
