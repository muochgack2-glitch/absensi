<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\AttendanceSetting;

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

    // Jalankan command mark absent + kirim WA
    Artisan::call('attendance:mark-absent', ['--notify' => true]);

})->everyMinute()
  ->name('absent-notify')   // diperlukan oleh withoutOverlapping()
  ->timezone('Asia/Jakarta')
  ->withoutOverlapping()
  ->appendOutputTo(storage_path('logs/absent-notification.log'));
