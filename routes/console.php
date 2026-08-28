<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\AttendanceSetting;
use App\Models\Holiday;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Jadwal Otomatis Absensi
|--------------------------------------------------------------------------
|
| Pastikan cron job sudah dipasang di server:
| * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
|
*/

Schedule::call(function () {
    // Baca setting dari DB setiap kali scheduler jalan
    $autoNotify  = AttendanceSetting::get('auto_absent_notify', '0');
    $notifyTime  = AttendanceSetting::get('absent_notify_time', '09:00');
    $notifyDays  = AttendanceSetting::get('absent_notify_days', '1,2,3,4,5');

    if ($autoNotify !== '1') {
        return; // Fitur dinonaktifkan dari UI
    }

    // Cek apakah hari ini termasuk hari aktif
    $todayDow    = now()->timezone('Asia/Jakarta')->dayOfWeekIso; // 1=Senin..7=Minggu
    $activeDays  = array_filter(explode(',', $notifyDays));

    if (!in_array((string) $todayDow, $activeDays)) {
        return; // Hari ini tidak aktif
    }

    // Cek apakah waktu sekarang sudah lewat jam kirim
    $now         = now()->timezone('Asia/Jakarta')->format('H:i');
    if ($now < $notifyTime) {
        return; // Belum waktunya
    }

    // Cek hari libur — blokir semua notifikasi otomatis
    $todayStr = now()->timezone('Asia/Jakarta')->toDateString();
    if (Holiday::isHoliday($todayStr)) {
        return; // Hari libur, skip notifikasi alfa
    }

    // Jalankan command mark absent + kirim WA
    Artisan::call('attendance:mark-absent', ['--notify' => true]);

})->everyMinute()
  ->name('absent-notify')   // diperlukan oleh withoutOverlapping()
  ->timezone('Asia/Jakarta')
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/absent-notification.log'));

/*
|--------------------------------------------------------------------------
| Auto-Sync Hari Libur dari E-Kaldik
|--------------------------------------------------------------------------
|
| Sync otomatis setiap hari jam 06:00 WIB.
| Bisa juga dijalankan manual: php artisan holidays:sync
|
*/
Schedule::command('holidays:sync')
  ->dailyAt('06:00')
  ->timezone('Asia/Jakarta')
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/holiday-sync.log'));

/*
|--------------------------------------------------------------------------
| Ringkasan Masuk ke Wali Kelas
|--------------------------------------------------------------------------
*/
Schedule::call(function () {
    $enabled   = AttendanceSetting::get('summary_wali_kelas_enabled', '0');
    $sendTime  = AttendanceSetting::get('summary_send_time', '09:00');
    $sendDays  = AttendanceSetting::get('summary_send_days', '1,2,3,4,5');

    if ($enabled !== '1') return;

    $todayDow   = now()->timezone('Asia/Jakarta')->dayOfWeekIso;
    $activeDays = array_filter(explode(',', $sendDays));
    if (!in_array((string) $todayDow, $activeDays)) return;

    $now = now()->timezone('Asia/Jakarta')->format('H:i');
    if ($now < $sendTime) return;

    // Cek sudah dikirim hari ini
    $today = now()->timezone('Asia/Jakarta')->toDateString();
    if (AttendanceSetting::get('summary_last_sent_date', '') === $today) return;

    // Cek hari libur
    if (Holiday::isHoliday($today)) return;

    AttendanceSetting::set('summary_last_sent_date', $today);
    Artisan::call('attendance:send-summary', ['--type' => 'masuk']);
})
  ->everyMinute()
  ->name('summary-masuk-wali-kelas')
  ->timezone('Asia/Jakarta')
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/attendance-summary.log'));

/*
|--------------------------------------------------------------------------
| Ringkasan Pulang ke Wali Kelas
|--------------------------------------------------------------------------
*/
Schedule::call(function () {
    $enabled   = AttendanceSetting::get('summary_wali_kelas_enabled', '0');
    $sendTime  = AttendanceSetting::get('summary_pulang_send_time', '15:00');
    $sendDays  = AttendanceSetting::get('summary_send_days', '1,2,3,4,5');

    if ($enabled !== '1') return;

    $todayDow   = now()->timezone('Asia/Jakarta')->dayOfWeekIso;
    $activeDays = array_filter(explode(',', $sendDays));
    if (!in_array((string) $todayDow, $activeDays)) return;

    $now = now()->timezone('Asia/Jakarta')->format('H:i');
    if ($now < $sendTime) return;

    // Cek sudah dikirim hari ini
    $today = now()->timezone('Asia/Jakarta')->toDateString();
    if (AttendanceSetting::get('summary_pulang_last_sent_date', '') === $today) return;

    // Cek hari libur
    if (Holiday::isHoliday($today)) return;

    AttendanceSetting::set('summary_pulang_last_sent_date', $today);
    Artisan::call('attendance:send-summary', ['--type' => 'pulang']);
})
  ->everyMinute()
  ->name('summary-pulang-wali-kelas')
  ->timezone('Asia/Jakarta')
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/attendance-summary.log'));

// Auto-cleanup foto absensi lebih tua dari 30 hari — setiap Minggu jam 01:00
Schedule::command('attendance:cleanup-photos --days=30')
  ->weekly()
  ->sundays()
  ->at('01:00')
  ->timezone('Asia/Jakarta')
  ->appendOutputTo(storage_path('logs/cleanup-photos.log'));
