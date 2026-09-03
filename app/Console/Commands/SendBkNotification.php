<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AttendanceRecord;
use App\Models\AttendanceStudent;
use App\Models\AttendanceSetting;
use App\Models\User;
use App\Models\WhatsAppTemplate;
use App\Services\AttendanceWhatsAppService;
use Carbon\Carbon;

class SendBkNotification extends Command
{
    protected $signature = 'attendance:send-bk-notification
                            {--date=      : Tanggal (Y-m-d, default: hari ini)}
                            {--type=alpha : Jenis: alpha atau checkout}
                            {--dry-run    : Tampilkan tanpa benar-benar mengirim}';

    protected $description = 'Kirim notifikasi per siswa ke Guru BK (alpha dengan foto profil, atau belum check-out)';

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

        $type   = $this->option('type') ?? 'alpha';
        $dryRun = $this->option('dry-run');
        $dayName = Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y');

        $this->info(($type === 'checkout' ? '🚪 BK Belum Check-out' : '❌ BK Alpha') . ": {$dayName}");
        if ($dryRun) $this->warn('-- DRY RUN --');

        if (AttendanceSetting::get('bk_notify_enabled', '0') !== '1' && !$dryRun) {
            $this->warn('Notifikasi Guru BK tidak aktif. Gunakan --dry-run untuk test.');
            return Command::SUCCESS;
        }

        $bkUsers = User::where('role', 'guru_bk')
            ->whereNotNull('phone')->where('phone', '!=', '')->get();

        if ($bkUsers->isEmpty()) {
            $this->warn('Tidak ada akun Guru BK dengan nomor HP yang dikonfigurasi.');
            return Command::SUCCESS;
        }

        $this->info("Guru BK: " . $bkUsers->pluck('name')->join(', '));
        $schoolName = AttendanceSetting::get('school_name', 'Sekolah');

        return $type === 'checkout'
            ? $this->handleCheckout($date, $dayName, $bkUsers, $schoolName, $dryRun)
            : $this->handleAlpha($date, $dayName, $bkUsers, $schoolName, $dryRun);
    }

    private function handleAlpha(string $date, string $dayName, $bkUsers, string $schoolName, bool $dryRun): int
    {
        if (AttendanceSetting::get('bk_notify_alpha', '1') !== '1' && !$dryRun) {
            $this->warn('Notifikasi alpha BK dinonaktifkan.');
            return Command::SUCCESS;
        }

        $allIds  = AttendanceStudent::where('is_active', true)->pluck('id')->toArray();
        $hadirIds = AttendanceRecord::withoutGlobalScope('tahun_ajaran')
            ->whereDate('date', $date)->whereNotNull('check_in_time')->pluck('student_id')->toArray();
        $izinIds  = AttendanceRecord::withoutGlobalScope('tahun_ajaran')
            ->whereDate('date', $date)->whereIn('status', ['izin', 'sakit'])->pluck('student_id')->toArray();
        $alphaIds = array_diff($allIds, array_unique(array_merge($hadirIds, $izinIds)));

        if (empty($alphaIds)) { $this->info('Tidak ada siswa alpha hari ini.'); return Command::SUCCESS; }

        $siswaAlpha = AttendanceStudent::with('kelas')
            ->whereIn('id', $alphaIds)->orderBy('kelas_id')->orderBy('nama')->get();

        $this->info("Siswa alpha: {$siswaAlpha->count()}");
        $template = WhatsAppTemplate::where('name', 'bk_alpha')->where('is_active', true)->first();

        $sent = $failed = 0;
        foreach ($siswaAlpha as $siswa) {
            $kelas = $siswa->kelas->nama_kelas ?? '-';
            $message = $template
                ? $template->parse(['nama' => $siswa->nama, 'nis' => $siswa->nis, 'kelas' => $kelas, 'tanggal' => $dayName, 'sekolah' => $schoolName])
                : implode("\n", ["❌ *SISWA ALPHA*", "Nama  : *{$siswa->nama}*", "NIS   : {$siswa->nis}", "Kelas : {$kelas}", "Tgl   : {$dayName}", "", "_{$schoolName}_"]);

            $fotoPath = !empty($siswa->foto_profil) && file_exists(storage_path('app/public/' . $siswa->foto_profil))
                ? storage_path('app/public/' . $siswa->foto_profil) : null;

            if ($dryRun) {
                $this->line("  [{$kelas}] {$siswa->nama} | foto: " . ($fotoPath ? 'ada' : 'tidak ada'));
                $sent++; continue;
            }

            $ok = true;
            foreach ($bkUsers as $bk) {
                $result = $this->waService->sendParentNotification($bk->phone, $message, $fotoPath);
                if (!($result['success'] ?? false)) { $this->error("  Gagal ke {$bk->name}: " . ($result['message'] ?? 'unknown')); $ok = false; }
            }
            $ok ? $sent++ : $failed++;
        }

        $this->newLine();
        $this->info("Selesai. Terkirim:{$sent} Gagal:{$failed}");
        return Command::SUCCESS;
    }

    private function handleCheckout(string $date, string $dayName, $bkUsers, string $schoolName, bool $dryRun): int
    {
        if (AttendanceSetting::get('bk_notify_belum_checkout', '1') !== '1' && !$dryRun) {
            $this->warn('Notifikasi belum check-out BK dinonaktifkan.');
            return Command::SUCCESS;
        }

        $hadirIds      = AttendanceRecord::withoutGlobalScope('tahun_ajaran')
            ->whereDate('date', $date)->whereNotNull('check_in_time')->pluck('student_id')->toArray();
        $sudahPulangIds = AttendanceRecord::withoutGlobalScope('tahun_ajaran')
            ->whereDate('date', $date)->whereNotNull('check_out_time')->pluck('student_id')->toArray();
        $belumIds = array_diff($hadirIds, $sudahPulangIds);

        if (empty($belumIds)) { $this->info('Semua siswa sudah check-out.'); return Command::SUCCESS; }

        $siswaBelum = AttendanceStudent::with('kelas')
            ->whereIn('id', $belumIds)->orderBy('kelas_id')->orderBy('nama')->get();

        $this->info("Belum check-out: {$siswaBelum->count()}");
        $template = WhatsAppTemplate::where('name', 'bk_belum_checkout')->where('is_active', true)->first();

        $sent = $failed = 0;
        foreach ($siswaBelum as $siswa) {
            $kelas = $siswa->kelas->nama_kelas ?? '-';
            $message = $template
                ? $template->parse(['nama' => $siswa->nama, 'nis' => $siswa->nis, 'kelas' => $kelas, 'tanggal' => $dayName, 'sekolah' => $schoolName])
                : implode("\n", ["🚪 *SISWA BELUM CHECK-OUT*", "Nama  : *{$siswa->nama}*", "NIS   : {$siswa->nis}", "Kelas : {$kelas}", "Tgl   : {$dayName}", "", "_{$schoolName}_"]);

            if ($dryRun) { $this->line("  [{$kelas}] {$siswa->nama}"); $sent++; continue; }

            $ok = true;
            foreach ($bkUsers as $bk) {
                $result = $this->waService->sendParentNotification($bk->phone, $message, null);
                if (!($result['success'] ?? false)) { $this->error("  Gagal ke {$bk->name}: " . ($result['message'] ?? 'unknown')); $ok = false; }
            }
            $ok ? $sent++ : $failed++;
        }

        $this->newLine();
        $this->info("Selesai. Terkirim:{$sent} Gagal:{$failed}");
        return Command::SUCCESS;
    }
}
