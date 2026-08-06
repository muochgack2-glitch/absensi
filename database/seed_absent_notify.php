<?php
// Seed absent notification settings ke DB

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AttendanceSetting;

$settings = [
    [
        'key'   => 'auto_absent_notify',
        'value' => '0',
        'label' => 'Aktif Notifikasi Alpha Otomatis',
        'group' => 'notification',
    ],
    [
        'key'   => 'absent_notify_time',
        'value' => '09:00',
        'label' => 'Jam Kirim Notifikasi Alpha',
        'group' => 'notification',
    ],
    [
        'key'   => 'absent_notify_days',
        'value' => '1,2,3,4,5',
        'label' => 'Hari Aktif Notifikasi Alpha',
        'group' => 'notification',
    ],
];

foreach ($settings as $s) {
    $existing = AttendanceSetting::firstOrCreate(
        ['key' => $s['key']],
        $s
    );
    echo ($existing->wasRecentlyCreated ? 'Created' : 'Exists') . ': ' . $s['key'] . PHP_EOL;
}

echo 'Done!' . PHP_EOL;
