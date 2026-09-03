<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Models\AttendanceSetting;
use App\Models\Holiday;
use App\Models\User;
use App\Services\AttendanceWhatsAppService;
use App\Services\AttendanceSummaryMessageService;
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

            // Ambil record pulang cepat beserta jam check_out
            $pulangCepatRecords = (clone $records)->where('check_out_status', 'pulang_cepat')
                ->get(['student_id', 'check_out_time'])->keyBy('student_id');
            $pulangCepatIds = $pulangCepatRecords->keys()->toArray();
            $pulangCepatPerKelas = [];
            if (!empty($pulangCepatIds)) {
                $siswaCepat = AttendanceStudent::with(['kelas.waliKelas'])
                    ->whereIn('id', $pulangCepatIds)
                    ->orderBy('kelas_id')->orderBy('nama')->get();
                foreach ($siswaCepat as $s) {
                    $namaKelas = $s->kelas->nama_kelas ?? '-';
                    $wali      = $s->kelas->waliKelas->name ?? '-';
                    if (!isset($pulangCepatPerKelas[$namaKelas])) {
                        $pulangCepatPerKelas[$namaKelas] = ['wali' => $wali, 'siswa' => []];
                    }
                    $rec = $pulangCepatRecords->get($s->id);
                    $jam = $rec && $rec->check_out_time
                        ? Carbon::parse($rec->check_out_time)->format('H:i') : '';
                    $pulangCepatPerKelas[$namaKelas]['siswa'][] = $jam ? "{$s->nama} ({$jam})" : $s->nama;
                }
            }

            $message = AttendanceSummaryMessageService::buildWakaPulang($dayName, $totalSiswa, $hadir, $sudahPulang, $pulangCepat, $belumPulang, $belumPulangPerKelas, $pulangCepatPerKelas);
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

            // Siswa terlambat per kelas
            $terlambatIds = (clone $recordsToday)->where('status', 'terlambat')->pluck('student_id')->toArray();
            $terlambatPerKelas = [];
            if (!empty($terlambatIds)) {
                $siswaTerlambat = AttendanceStudent::with(['kelas.waliKelas'])
                    ->whereIn('id', $terlambatIds)
                    ->orderBy('kelas_id')->orderBy('nama')->get();
                foreach ($siswaTerlambat as $s) {
                    $namaKelas = $s->kelas->nama_kelas ?? '-';
                    $wali      = $s->kelas->waliKelas->name ?? '-';
                    if (!isset($terlambatPerKelas[$namaKelas])) {
                        $terlambatPerKelas[$namaKelas] = ['wali' => $wali, 'siswa' => []];
                    }
                    $terlambatPerKelas[$namaKelas]['siswa'][] = $s->nama;
                }
            }

            $message = AttendanceSummaryMessageService::buildWakaMasuk($dayName, $totalSiswa, $hadir, $terlambat, $alpha, $izin, $sakit, $persen, $status, $alphaPerKelas, $terlambatPerKelas);
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
}

