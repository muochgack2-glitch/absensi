<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

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

// Mark siswa tidak hadir sebagai alpha + kirim notifikasi WA
// Setiap hari kerja (Senin–Jumat) jam 09:00
Schedule::command('attendance:mark-absent --notify')
    ->weekdays()
    ->at('09:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/absent-notification.log'));
