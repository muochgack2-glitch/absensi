<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceClass;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Services\AttendanceWhatsAppService;
use App\Services\AttendanceSummaryMessageService;
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
            $sakitIds = $records->where('status', 'sakit')->pluck('student_id')->toArray();
            $izin     = count(array_unique($izinIds));
            $sakit    = count(array_unique($sakitIds));

            // Alpha = siswa yang tidak hadir (scan), bukan izin, dan bukan sakit
            $notAlphaIds = array_unique(array_merge($hadirIds, $izinIds, $sakitIds));
            $alfaStudents = AttendanceStudent::where('kelas_id', $kelas->id)
                ->where('is_active', true)
                ->whereNotIn('id', $notAlphaIds)
                ->pluck('nama')->toArray();

            $alfa  = count($alfaStudents);
            $hadir = max(0, $totalSiswa - $alfa - $izin - $sakit);

            // Nama siswa izin dan sakit
            $izinStudents  = AttendanceStudent::where('kelas_id', $kelas->id)->whereIn('id', array_unique($izinIds))->orderBy('nama')->pluck('nama')->toArray();
            $sakitStudents = AttendanceStudent::where('kelas_id', $kelas->id)->whereIn('id', array_unique($sakitIds))->orderBy('nama')->pluck('nama')->toArray();

            if ($type === 'pulang') {
                // Hitung yang sudah check-out
                $pulangIds       = $records->whereNotNull('check_out_time')->pluck('student_id')->toArray();
                $pulangCepatIds  = $records->where('check_out_status', 'pulang_cepat')->pluck('student_id')->toArray();
                $pulangTepatIds  = array_diff($pulangIds, $pulangCepatIds);
                $belumPulangIds  = array_diff($hadirIds, $pulangIds);
                $belumPulang     = max(0, count($belumPulangIds));

                // Nama siswa belum pulang
                $belumPulangStudents = AttendanceStudent::where('kelas_id', $kelas->id)
                    ->where('is_active', true)
                    ->whereIn('id', $belumPulangIds)
                    ->pluck('nama')->toArray();

                // Nama siswa pulang lebih awal + jam pulang
                $pulangCepatRecords = $records->where('check_out_status', 'pulang_cepat')
                    ->keyBy('student_id');
                $pulangCepatStudents = AttendanceStudent::where('kelas_id', $kelas->id)
                    ->where('is_active', true)
                    ->whereIn('id', $pulangCepatIds)
                    ->orderBy('nama')->get()
                    ->map(function ($s) use ($pulangCepatRecords) {
                        $rec = $pulangCepatRecords->get($s->id);
                        $jam = $rec && $rec->check_out_time
                            ? \Carbon\Carbon::parse($rec->check_out_time)->format('H:i') : '';
                        return $jam ? "{$s->nama} ({$jam})" : $s->nama;
                    })->toArray();

                $message = AttendanceSummaryMessageService::buildWaliPulang($kelas->nama_kelas, $dayName, $totalSiswa, $hadir, $izin, $sakit, $alfa, count($pulangTepatIds), count($pulangCepatIds), $belumPulang, $belumPulangStudents, $pulangCepatStudents, $izinStudents, $sakitStudents);
                $this->line("  {$kelas->nama_kelas} | Hadir:{$hadir} Izin:{$izin} Sakit:{$sakit} Alfa:{$alfa} PulangTepat:".count($pulangTepatIds)." PulangCepat:".count($pulangCepatIds)." Belum:{$belumPulang}");
            } else {
                // Hitung terlambat untuk summary masuk
                $terlambat  = $records->where('status', 'terlambat')->count();
                $hadirTepat = max(0, $hadir - $terlambat);
                $message = AttendanceSummaryMessageService::buildWaliMasuk($kelas->nama_kelas, $dayName, $totalSiswa, $hadirTepat, $terlambat, $izin, $sakit, $alfa, $alfaStudents, $izinStudents, $sakitStudents);
                $this->line("  {$kelas->nama_kelas} | HadirTepat:{$hadirTepat} Terlambat:{$terlambat} Izin:{$izin} Sakit:{$sakit} Alfa:{$alfa} Total:{$totalSiswa}");
            }

            if ($dryRun) {
                $this->line("  ┌─ Pesan ke {$wali->name} ({$wali->phone}):");
                foreach (explode("\n", $message) as $l) {
                    $this->line("  │ " . $l);
                }
                $this->line("  └─────────────────────────────");
                $sent++; continue;
            }

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
}
