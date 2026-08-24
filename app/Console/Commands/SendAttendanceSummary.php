<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceClass;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Services\AttendanceWhatsAppService;
use Carbon\Carbon;

class SendAttendanceSummary extends Command
{
    protected $signature = 'attendance:send-summary
                            {--date=    : Tanggal ringkasan (Y-m-d, default: hari ini)}
                            {--class=   : ID kelas tertentu (default: semua kelas aktif)}
                            {--type=masuk : Jenis ringkasan: masuk atau pulang}
                            {--dry-run  : Tampilkan ringkasan tanpa benar-benar mengirim WA}';

    protected $description = 'Kirim ringkasan kehadiran (masuk/pulang) ke wali kelas via WhatsApp';

    public function __construct(
        protected AttendanceWhatsAppService $waService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $date    = $this->option('date')
            ? Carbon::parse($this->option('date'))->toDateString()
            : Carbon::today()->timezone('Asia/Jakarta')->toDateString();

        $classId = $this->option('class');
        $dryRun  = $this->option('dry-run');
        $type    = $this->option('type') ?? 'masuk'; // masuk | pulang
        $dayName = Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y');

        $label = $type === 'pulang' ? '🌆 Ringkasan Pulang' : '🌅 Ringkasan Masuk';
        $this->info("{$label}: {$dayName}");
        if ($dryRun) $this->warn('-- DRY RUN: pesan tidak akan dikirim --');

        $query = AttendanceClass::with('waliKelas')
            ->active()
            ->whereNotNull('wali_kelas_id');

        if ($classId) $query->where('id', $classId);

        $classes = $query->get();

        if ($classes->isEmpty()) {
            $this->warn('Tidak ada kelas aktif dengan wali kelas yang dikonfigurasi.');
            return Command::SUCCESS;
        }

        $sent = $skipped = $failed = 0;

        foreach ($classes as $kelas) {
            $wali = $kelas->waliKelas;

            if (empty($wali->phone)) {
                $this->warn("  [{$kelas->nama_kelas}] Wali kelas tidak punya nomor HP — dilewati");
                $skipped++;
                continue;
            }

            $studentIds = AttendanceStudent::where('kelas_id', $kelas->id)
                ->where('is_active', true)->pluck('id');

            $totalSiswa = $studentIds->count();

            $records = AttendanceRecord::withoutGlobalScope('tahun_ajaran')
                ->whereDate('date', $date)
                ->whereIn('student_id', $studentIds)
                ->with('student')
                ->get();

            $hadirIds = $records->whereNotNull('check_in_time')->pluck('student_id')->toArray();
            $izinIds  = $records->where('status', 'izin')->pluck('student_id')->toArray();
            $izin     = count(array_unique($izinIds));

            $alfaStudents = AttendanceStudent::where('kelas_id', $kelas->id)
                ->where('is_active', true)
                ->whereNotIn('id', array_unique(array_merge($hadirIds, $izinIds)))
                ->pluck('nama')->toArray();

            $alfa  = count($alfaStudents);
            $hadir = max(0, $totalSiswa - $alfa - $izin);

            if ($type === 'pulang') {
                // Hitung yang sudah check-out
                $pulangIds    = $records->whereNotNull('check_out_time')->pluck('student_id')->toArray();
                $belumPulang  = count($hadirIds) - count(array_intersect($hadirIds, $pulangIds));
                $belumPulang  = max(0, $belumPulang);
                $message = $this->buildPulangMessage($kelas->nama_kelas, $dayName, $totalSiswa, $hadir, $izin, $alfa, count($pulangIds), $belumPulang);
                $this->line("  {$kelas->nama_kelas} | Hadir:{$hadir} Sudah pulang:".count($pulangIds)." Belum:{$belumPulang}");
            } else {
                $message = $this->buildMasukMessage($kelas->nama_kelas, $dayName, $totalSiswa, $hadir, $izin, $alfa, $alfaStudents);
                $this->line("  {$kelas->nama_kelas} | Hadir:{$hadir} Izin:{$izin} Alfa:{$alfa} Total:{$totalSiswa}");
            }

            if ($dryRun) { $sent++; continue; }

            try {
                $result = $this->waService->send($wali->phone, $message, ['type' => 'summary', 'sent_by' => null]);
                if ($result['success'] ?? false) {
                    $this->info("  Terkirim ke {$wali->name} ({$wali->phone})");
                    $sent++;
                } else {
                    $this->error("  Gagal: " . ($result['message'] ?? 'unknown'));
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("  Error [{$kelas->nama_kelas}]: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Selesai. Terkirim:{$sent} Dilewati:{$skipped} Gagal:{$failed}");
        return Command::SUCCESS;
    }

    protected function buildMasukMessage(string $namaKelas, string $tanggal, int $total, int $hadir, int $izin, int $alfa, array $alfaStudents): string
    {
        $persen = $total > 0 ? round(($hadir / $total) * 100) : 0;

        $lines = [
            "*RINGKASAN KEHADIRAN MASUK*",
            "Kelas  : *{$namaKelas}*",
            "Tanggal: {$tanggal}",
            "",
            "Hadir  : {$hadir} siswa",
            "Izin   : {$izin} siswa",
            "Alfa   : {$alfa} siswa",
            "Total  : {$total} siswa",
            "Kehadiran: {$persen}%",
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

    protected function buildPulangMessage(string $namaKelas, string $tanggal, int $total, int $hadir, int $izin, int $alfa, int $sudahPulang, int $belumPulang): string
    {
        $lines = [
            "*RINGKASAN KEPULANGAN*",
            "Kelas  : *{$namaKelas}*",
            "Tanggal: {$tanggal}",
            "",
            "Hadir hari ini : {$hadir} siswa",
            "Sudah pulang   : {$sudahPulang} siswa",
            "Belum pulang   : {$belumPulang} siswa",
            "Izin           : {$izin} siswa",
            "Alfa           : {$alfa} siswa",
            "Total          : {$total} siswa",
        ];

        $lines[] = "";
        $lines[] = "_Sistem Absensi SMK PGRI Blora_";

        return implode("\n", $lines);
    }
}
