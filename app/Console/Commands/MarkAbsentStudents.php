<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AttendanceService;
use App\Services\AttendanceNotificationService;
use App\Models\AttendanceStudent;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class MarkAbsentStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:mark-absent
                            {--notify : Kirim notifikasi WA ke orang tua siswa yang alpha}
                            {--dry-run : Tampilkan siapa saja yang akan di-mark tanpa benar-benar menyimpan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark siswa yang tidak hadir sebagai alpha, opsional kirim notifikasi WA ke orang tua';

    public function __construct(
        protected AttendanceService $attendanceService,
        protected AttendanceNotificationService $notificationService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today    = Carbon::today()->format('d/m/Y');
        $isDryRun = $this->option('dry-run');
        $doNotify = $this->option('notify');

        $this->info("====================================");
        $this->info("  Absensi Auto-Alpha: {$today}");
        $this->info("====================================");

        if ($isDryRun) {
            $this->warn('[DRY RUN] Tidak ada data yang akan disimpan.');
            $this->newLine();
        }

        try {
            if ($isDryRun) {
                return $this->runDryRun();
            }

            // 1. Mark semua yang belum hadir sebagai alpha
            $result = $this->attendanceService->markAbsentStudents();

            if (!$result['success']) {
                $this->error('Gagal menandai siswa alpha: ' . ($result['message'] ?? 'Unknown error'));
                return self::FAILURE;
            }

            // 2. Tampilkan ringkasan
            $this->line("✓ Total siswa aktif    : " . $result['total_students']);
            $this->line("✓ Di-mark alpha        : " . $result['marked_absent']);
            $this->line("✓ Sudah ada record     : " . $result['already_recorded']);
            $this->line("✓ Siswa non-aktif skip : " . $result['inactive_skipped']);

            if ($result['marked_absent'] === 0) {
                $this->newLine();
                $this->comment('Tidak ada siswa yang perlu di-mark alpha hari ini.');
                return self::SUCCESS;
            }

            // 3. Tampilkan daftar siswa yang di-mark
            $this->newLine();
            $this->line('Siswa yang di-mark alpha:');
            foreach ($result['marked_students'] as $s) {
                $this->line("  • {$s['nis']} - {$s['nama']} ({$s['kelas']})");
            }

            // 4. Kirim notifikasi WA jika flag --notify aktif
            if ($doNotify) {
                $this->newLine();
                $this->info('Mengirim notifikasi WhatsApp ke orang tua...');
                $this->sendNotifications($result['marked_students']);
            } else {
                $this->newLine();
                $this->comment('Tip: jalankan dengan --notify untuk kirim WA ke orang tua.');
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Kirim WA notifikasi ke orang tua untuk setiap siswa yang di-mark alpha.
     */
    protected function sendNotifications(array $markedStudents): void
    {
        $today   = Carbon::today();
        $success = 0;
        $failed  = 0;
        $noPhone = 0;

        foreach ($markedStudents as $data) {
            $student = AttendanceStudent::with('kelas')
                ->where('nis', $data['nis'])
                ->first();

            if (!$student) continue;

            // Skip jika tidak ada nomor HP ortu
            if (empty($student->no_hp_ortu)) {
                $this->warn("  ⚠ {$student->nama} — tidak ada nomor HP orang tua");
                $noPhone++;
                continue;
            }

            // Ambil record alpha yang baru dibuat
            $record = AttendanceRecord::where('student_id', $student->id)
                ->whereDate('date', $today)
                ->first();

            if (!$record) continue;

            try {
                $this->notificationService->notifyAbsent($student, $record);
                $this->line("  ✓ WA terkirim ke orang tua {$student->nama} ({$student->no_hp_ortu})");
                $success++;
            } catch (\Exception $e) {
                $this->error("  ✗ Gagal kirim WA ke {$student->nama}: " . $e->getMessage());
                $failed++;
            }

            // Delay 2 detik antar pesan agar tidak rate-limited oleh WA
            sleep(2);
        }

        $this->newLine();
        $this->info("Notifikasi WA: ✓ {$success} berhasil | ✗ {$failed} gagal | ⚠ {$noPhone} tanpa no HP");
    }

    /**
     * Mode dry-run: tampilkan siapa yang akan di-mark tanpa simpan.
     */
    protected function runDryRun(): int
    {
        $today    = Carbon::today();
        $students = AttendanceStudent::with('kelas')->where('is_active', true)->get();
        $wouldMark = [];

        foreach ($students as $student) {
            $record = AttendanceRecord::where('student_id', $student->id)
                ->whereDate('date', $today)
                ->first();

            if (!$record || ($record->check_in_time === null && $record->status !== 'alpha')) {
                $wouldMark[] = $student;
            }
        }

        if (empty($wouldMark)) {
            $this->comment('Tidak ada siswa yang akan di-mark alpha.');
            return self::SUCCESS;
        }

        $this->line("Siswa yang AKAN di-mark alpha (" . count($wouldMark) . " orang):");
        $this->newLine();

        $rows = collect($wouldMark)->map(fn($s) => [
            $s->nis,
            $s->nama,
            $s->kelas->nama_kelas ?? '-',
            empty($s->no_hp_ortu) ? '❌ Tidak ada' : $s->no_hp_ortu,
        ])->toArray();

        $this->table(['NIS', 'Nama', 'Kelas', 'No HP Ortu'], $rows);

        return self::SUCCESS;
    }
}
