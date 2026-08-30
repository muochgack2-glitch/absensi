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

class SendWakaSummary extends Command
{
    protected $signature = 'attendance:send-waka-summary
                            {--date=      : Tanggal (Y-m-d, default: hari ini)}
                            {--type=masuk : Jenis: masuk atau pulang}
                            {--dry-run    : Tampilkan pesan tanpa benar-benar mengirim}';

    protected $description = 'Kirim rekap kehadiran seluruh sekolah ke Waka Kesiswaan via WhatsApp (format sama dengan Kepala Sekolah)';

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
        $label   = $type === 'pulang' ? '🌆 Rekap Pulang Waka' : '📊 Rekap Masuk Waka';

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
            $records        = AttendanceRecord::withoutGlobalScope('tahun_ajaran')->whereDate('date', $date);
            $sudahPulang    = (clone $records)->whereNotNull('check_out_time')->count();
            $pulangCepat    = (clone $records)->where('check_out_status', 'pulang_cepat')->count();
            $belumPulang    = max(0, $hadir - $sudahPulang);

            $hadirIds       = (clone $records)->whereNotNull('check_in_time')->pluck('student_id')->toArray();
            $sudahPulangIds = (clone $records)->whereNotNull('check_out_time')->pluck('student_id')->toArray();
            $belumPulangIds = array_diff($hadirIds, $sudahPulangIds);

            $belumPulangPerKelas = [];
            if (!empty($belumPulangIds)) {
                $siswaBelum = AttendanceStudent::with(['kelas.waliKelas'])
                    ->whereIn('id', $belumPulangIds)
                    ->orderBy('kelas_id')->orderBy('nama')->get();

                foreach ($siswaBelum as $s) {
                    $namaKelas = $s->kelas->nama_kelas ?? '-';
                    $wali      = $s->kelas->waliKelas->name ?? '-';
                    if (!isset($belumPulangPerKelas[$namaKelas])) {
                        $belumPulangPerKelas[$namaKelas] = ['wali' => $wali, 'siswa' => []];
                    }
                    $belumPulangPerKelas[$namaKelas]['siswa'][] = $s->nama;
                }
            }

            $message = $this->buildPulangMessage($dayName, $totalSiswa, $hadir, $sudahPulang, $pulangCepat, $belumPulang, $belumPulangPerKelas);
        } else {
            $persen = $totalSiswa > 0 ? round(($hadir / $totalSiswa) * 100, 1) : 0;
            $status = match(true) {
                $persen >= 95 => '🟢 BAIK',
                $persen >= 85 => '🟡 PERLU PERHATIAN',
                default       => '🔴 RENDAH',
            };

            $recordsToday  = AttendanceRecord::withoutGlobalScope('tahun_ajaran')->whereDate('date', $date);
            $hadirIds2     = (clone $recordsToday)->whereNotNull('check_in_time')->pluck('student_id')->toArray();
            $izinSakitIds  = (clone $recordsToday)->whereIn('status', ['izin', 'sakit'])->pluck('student_id')->toArray();
            $tidakHadirIds = array_diff(
                AttendanceStudent::where('is_active', true)->pluck('id')->toArray(),
                array_unique(array_merge($hadirIds2, $izinSakitIds))
            );

            $alphaPerKelas = [];
            if (!empty($tidakHadirIds)) {
                $siswaAlpha = AttendanceStudent::with(['kelas.waliKelas'])
                    ->whereIn('id', $tidakHadirIds)
                    ->orderBy('kelas_id')->orderBy('nama')->get();

                foreach ($siswaAlpha as $s) {
                    $namaKelas = $s->kelas->nama_kelas ?? '-';
                    $wali      = $s->kelas->waliKelas->name ?? '-';
                    if (!isset($alphaPerKelas[$namaKelas])) {
                        $alphaPerKelas[$namaKelas] = ['wali' => $wali, 'siswa' => []];
                    }
                    $alphaPerKelas[$namaKelas]['siswa'][] = $s->nama;
                }
            }

            $message = $this->buildMasukMessage($dayName, $totalSiswa, $hadir, $terlambat, $alpha, $izin, $sakit, $persen, $status, $alphaPerKelas);
        }

        $this->line('');
        $this->line($message);
        $this->line('');

        if ($dryRun) {
            $this->info('Pesan TIDAK dikirim (dry-run).');
            return Command::SUCCESS;
        }

        $wakaUsers = User::where('role', 'waka_kesiswaan')
            ->whereNotNull('phone')->where('phone', '!=', '')->get();

        if ($wakaUsers->isEmpty()) {
            $this->warn('Tidak ada user waka_kesiswaan dengan nomor HP yang dikonfigurasi.');
            return Command::SUCCESS;
        }

        $sent = $failed = 0;
        foreach ($wakaUsers as $waka) {
            try {
                $result = $this->waService->send($waka->phone, $message, ['type' => "waka-{$type}", 'sent_by' => null]);
                if ($result['success'] ?? false) {
                    $this->info("Terkirim ke {$waka->name} ({$waka->phone})");
                    $sent++;
                } else {
                    $this->error("Gagal ke {$waka->name}: " . ($result['message'] ?? 'unknown'));
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("Error [{$waka->name}]: " . $e->getMessage());
                $failed++;
            }
        }

        $this->info("Selesai. Terkirim:{$sent} Gagal:{$failed}");
        return Command::SUCCESS;
    }

    protected function buildMasukMessage(
        string $dayName, int $totalSiswa, int $hadir, int $terlambat,
        int $alpha, int $izin, int $sakit, float $persen, string $status,
        array $alphaPerKelas = []
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
        ];

        if (!empty($alphaPerKelas)) {
            $lines[] = "";
            $lines[] = "*Detail Siswa Alpha:*";
            foreach ($alphaPerKelas as $namaKelas => $data) {
                $lines[] = "";
                $lines[] = "📚 *{$namaKelas}*";
                $lines[] = "   Wali Kelas: {$data['wali']}";
                foreach ($data['siswa'] as $i => $nama) {
                    $lines[] = "   " . ($i + 1) . ". {$nama}";
                }
            }
        }

        $lines[] = "";
        $lines[] = "_Sistem Absensi Otomatis_";

        return implode("\n", $lines);
    }

    protected function buildPulangMessage(
        string $dayName, int $totalSiswa, int $hadir,
        int $sudahPulang, int $pulangCepat, int $belumPulang,
        array $belumPulangPerKelas = []
    ): string {
        $schoolName  = AttendanceSetting::get('school_name', 'SMK');
        $pulangTepat = $sudahPulang - $pulangCepat;

        $lines = [
            "🌆 *LAPORAN KEPULANGAN HARIAN*",
            "*{$schoolName}*",
            $dayName,
            "",
            "👥 Total Siswa     : {$totalSiswa} orang",
            "🏫 Hadir hari ini  : {$hadir} siswa",
            "✅ Sudah pulang    : {$sudahPulang} siswa",
            "   ↳ Tepat waktu  : {$pulangTepat} siswa",
            "   ↳ Pulang cepat : {$pulangCepat} siswa",
            "⏳ Belum pulang   : {$belumPulang} siswa",
        ];

        if (!empty($belumPulangPerKelas)) {
            $lines[] = "";
            $lines[] = "*Detail Belum Pulang:*";
            foreach ($belumPulangPerKelas as $namaKelas => $data) {
                $lines[] = "";
                $lines[] = "📚 *{$namaKelas}*";
                $lines[] = "   Wali Kelas: {$data['wali']}";
                foreach ($data['siswa'] as $i => $nama) {
                    $lines[] = "   " . ($i + 1) . ". {$nama}";
                }
            }
        }

        $lines[] = "";
        $lines[] = "_Sistem Absensi Otomatis_";

        return implode("\n", $lines);
    }
}
