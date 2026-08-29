<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceClass;
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

    protected $description = 'Kirim rekap kehadiran seluruh sekolah (per kelas + nama siswa) ke Waka Kesiswaan via WhatsApp';

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

        $type   = $this->option('type') ?? 'masuk';
        $dryRun = $this->option('dry-run');
        $dayName = Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y');

        $label = $type === 'pulang' ? '🌆 Rekap Pulang Waka' : '🌅 Rekap Masuk Waka';
        $this->info("{$label}: {$dayName}");
        if ($dryRun) $this->warn('-- DRY RUN --');

        // Ambil semua user dengan role waka_kesiswaan yang punya nomor HP
        $wakaUsers = User::where('role', 'waka_kesiswaan')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        if ($wakaUsers->isEmpty()) {
            $this->warn('Tidak ada user waka_kesiswaan dengan nomor HP yang dikonfigurasi.');
            return Command::SUCCESS;
        }

        // Ambil semua kelas aktif
        $classes = AttendanceClass::active()
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        if ($classes->isEmpty()) {
            $this->warn('Tidak ada kelas aktif.');
            return Command::SUCCESS;
        }

        // Bangun data per kelas
        $classData = [];
        $totalHadir = $totalTerlambat = $totalAlpha = $totalIzinSakit = 0;
        $totalSudahPulang = $totalBelumPulang = $totalPulangCepat = 0;

        foreach ($classes as $kelas) {
            $studentIds = AttendanceStudent::where('kelas_id', $kelas->id)
                ->where('is_active', true)
                ->pluck('id');

            $totalSiswa = $studentIds->count();
            if ($totalSiswa === 0) continue;

            $records = AttendanceRecord::withoutGlobalScope('tahun_ajaran')
                ->whereDate('date', $date)
                ->whereIn('student_id', $studentIds)
                ->with('student')
                ->get();

            if ($type === 'masuk') {
                $hadirIds    = $records->where('status', 'hadir')->pluck('student_id')->toArray();
                $terlambatIds = $records->where('status', 'terlambat')->pluck('student_id')->toArray();
                $izinIds     = $records->whereIn('status', ['izin', 'sakit'])->pluck('student_id')->toArray();
                $recorded    = array_unique(array_merge($hadirIds, $terlambatIds, $izinIds));

                // Alpha = siswa aktif yang tidak ada record atau belum check-in
                $alphaStudents = AttendanceStudent::where('kelas_id', $kelas->id)
                    ->where('is_active', true)
                    ->whereNotIn('id', $recorded)
                    ->pluck('nama')
                    ->toArray();

                $hadir      = count($hadirIds);
                $terlambat  = count($terlambatIds);
                $izinSakit  = count($izinIds);
                $alpha      = count($alphaStudents);

                $totalHadir      += $hadir + $terlambat;
                $totalTerlambat  += $terlambat;
                $totalAlpha      += $alpha;
                $totalIzinSakit  += $izinSakit;

                $classData[] = [
                    'nama'        => $kelas->nama_kelas,
                    'total'       => $totalSiswa,
                    'hadir'       => $hadir,
                    'terlambat'   => $terlambat,
                    'alpha'       => $alpha,
                    'izin_sakit'  => $izinSakit,
                    'alpha_names' => $alphaStudents,
                ];

            } else {
                // Pulang
                $hadirIds       = $records->whereNotNull('check_in_time')->pluck('student_id')->toArray();
                $pulangCepatIds = $records->where('check_out_status', 'pulang_cepat')->pluck('student_id')->toArray();
                $pulangTepatIds = $records->whereNotNull('check_out_time')
                    ->where('check_out_status', '!=', 'pulang_cepat')
                    ->pluck('student_id')->toArray();
                $sudahPulangIds = $records->whereNotNull('check_out_time')->pluck('student_id')->toArray();

                $belumPulangStudents = AttendanceStudent::where('kelas_id', $kelas->id)
                    ->where('is_active', true)
                    ->whereIn('id', $hadirIds)
                    ->whereNotIn('id', $sudahPulangIds)
                    ->pluck('nama')
                    ->toArray();

                $totalSudahPulang += count($sudahPulangIds);
                $totalBelumPulang += count($belumPulangStudents);
                $totalPulangCepat += count($pulangCepatIds);

                if (empty($hadirIds)) continue; // skip kelas yang tidak ada yang hadir

                $classData[] = [
                    'nama'          => $kelas->nama_kelas,
                    'hadir'         => count($hadirIds),
                    'pulang_tepat'  => count($pulangTepatIds),
                    'pulang_cepat'  => count($pulangCepatIds),
                    'belum_pulang'  => count($belumPulangStudents),
                    'belum_names'   => $belumPulangStudents,
                ];
            }
        }

        // Bangun pesan
        $message = $type === 'pulang'
            ? $this->buildPulangMessage($dayName, $classData, $totalSudahPulang, $totalBelumPulang, $totalPulangCepat)
            : $this->buildMasukMessage($dayName, $classData, $totalHadir, $totalTerlambat, $totalAlpha, $totalIzinSakit);

        $this->line($message);

        if ($dryRun) {
            $this->info('Pesan TIDAK dikirim (dry-run).');
            return Command::SUCCESS;
        }

        $sent = $failed = 0;
        foreach ($wakaUsers as $waka) {
            try {
                $result = $this->waService->send($waka->phone, $message, ['type' => 'waka-summary', 'sent_by' => null]);
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
        string $dayName,
        array $classData,
        int $totalHadir,
        int $totalTerlambat,
        int $totalAlpha,
        int $totalIzinSakit
    ): string {
        $schoolName = AttendanceSetting::get('school_name', 'SMK');
        $totalSemua = $totalHadir + $totalAlpha + $totalIzinSakit;
        $persen     = $totalSemua > 0 ? round(($totalHadir / $totalSemua) * 100, 1) : 0;

        $lines = [
            "🌅 *REKAP MASUK HARIAN*",
            "*{$schoolName}*",
            $dayName,
            "",
            "📊 *Total Seluruh Sekolah*",
            "✅ Hadir tepat  : " . ($totalHadir - $totalTerlambat) . " siswa",
            "⏰ Terlambat    : {$totalTerlambat} siswa",
            "❌ Alpha        : {$totalAlpha} siswa",
            "📋 Izin/Sakit   : {$totalIzinSakit} siswa",
            "📈 Kehadiran    : {$persen}%",
            "",
            "━━━━━━━━━━━━━━━━━━",
        ];

        foreach ($classData as $k) {
            $lines[] = "";
            $lines[] = "📚 *{$k['nama']}* ({$k['total']} siswa)";
            $parts = [];
            if ($k['hadir'] > 0)      $parts[] = "✅ {$k['hadir']} hadir";
            if ($k['terlambat'] > 0)  $parts[] = "⏰ {$k['terlambat']} terlambat";
            if ($k['alpha'] > 0)      $parts[] = "❌ {$k['alpha']} alpha";
            if ($k['izin_sakit'] > 0) $parts[] = "📋 {$k['izin_sakit']} izin/sakit";
            $lines[] = implode(' | ', $parts);

            if (!empty($k['alpha_names'])) {
                $lines[] = "   Alpha: " . implode(', ', $k['alpha_names']);
            }
        }

        $lines[] = "";
        $lines[] = "_Sistem Absensi Otomatis_";

        return implode("\n", $lines);
    }

    protected function buildPulangMessage(
        string $dayName,
        array $classData,
        int $totalSudahPulang,
        int $totalBelumPulang,
        int $totalPulangCepat
    ): string {
        $schoolName = AttendanceSetting::get('school_name', 'SMK');

        $lines = [
            "🌆 *REKAP PULANG HARIAN*",
            "*{$schoolName}*",
            $dayName,
            "",
            "📊 *Total Seluruh Sekolah*",
            "✅ Sudah pulang    : {$totalSudahPulang} siswa",
            "⚡ Pulang cepat   : {$totalPulangCepat} siswa",
            "⏳ Belum pulang   : {$totalBelumPulang} siswa",
            "",
            "━━━━━━━━━━━━━━━━━━",
        ];

        foreach ($classData as $k) {
            if ($k['hadir'] === 0) continue;
            $lines[] = "";
            $lines[] = "📚 *{$k['nama']}* (hadir {$k['hadir']})";
            $parts = [];
            if ($k['pulang_tepat'] > 0) $parts[] = "✅ {$k['pulang_tepat']} tepat";
            if ($k['pulang_cepat'] > 0) $parts[] = "⚡ {$k['pulang_cepat']} cepat";
            if ($k['belum_pulang'] > 0) $parts[] = "⏳ {$k['belum_pulang']} belum";
            $lines[] = implode(' | ', $parts);

            if (!empty($k['belum_names'])) {
                $lines[] = "   Belum: " . implode(', ', $k['belum_names']);
            }
        }

        $lines[] = "";
        $lines[] = "_Sistem Absensi Otomatis_";

        return implode("\n", $lines);
    }
}
