<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG NOTIFICATION SYSTEM ===\n\n";

// 1. Check Settings
echo "1. SETTINGS CHECK:\n";
echo "   enable_parent_notification: " . App\Models\AttendanceSetting::get('enable_parent_notification') . "\n";
echo "   notify_all_checkin: " . App\Models\AttendanceSetting::get('notify_all_checkin') . "\n";
echo "   notify_checkout: " . App\Models\AttendanceSetting::get('notify_checkout') . "\n";
echo "   late_notify_enabled: " . App\Models\AttendanceSetting::get('late_notify_enabled') . "\n";
echo "\n";

// 2. Check WhatsApp Gateway
echo "2. WHATSAPP GATEWAY STATUS:\n";
try {
    $waService = app(App\Services\AttendanceWhatsAppService::class);
    $status = $waService->getStatus();
    echo "   Success: " . ($status['success'] ? 'YES' : 'NO') . "\n";
    if ($status['success'] && isset($status['data'])) {
        echo "   Status: " . ($status['data']['status'] ?? 'unknown') . "\n";
        echo "   Phone: " . ($status['data']['phone'] ?? 'unknown') . "\n";
    } else {
        echo "   Error: " . ($status['message'] ?? 'Unknown error') . "\n";
    }
} catch (Exception $e) {
    echo "   ERROR: " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Check Recent Attendance Logs
echo "3. RECENT ATTENDANCE LOGS (last 5):\n";
$logs = DB::table('attendance_logs')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($logs->isEmpty()) {
    echo "   No logs found\n";
} else {
    foreach ($logs as $log) {
        echo "   [{$log->id}] Action: {$log->action} | Status: {$log->status} | Message: {$log->message}\n";
        echo "       Time: {$log->created_at}\n";
    }
}
echo "\n";

// 4. Check WhatsApp Logs
echo "4. RECENT WHATSAPP LOGS (last 5):\n";
$waLogs = DB::table('whatsapp_logs')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($waLogs->isEmpty()) {
    echo "   No WhatsApp logs found\n";
} else {
    foreach ($waLogs as $log) {
        echo "   [{$log->id}] Phone: {$log->phone} | Status: {$log->status} | Type: {$log->type}\n";
        echo "       Time: {$log->created_at}\n";
        if ($log->error_message) {
            echo "       Error: {$log->error_message}\n";
        }
    }
}
echo "\n";

// 5. Check Sample Student
echo "5. SAMPLE STUDENT DATA:\n";
$student = App\Models\AttendanceStudent::where('is_active', true)->first();
if ($student) {
    echo "   NIS: {$student->nis}\n";
    echo "   Nama: {$student->nama}\n";
    echo "   No HP Ortu: " . ($student->no_hp_ortu ?: 'EMPTY') . "\n";
    echo "   Kelas: " . ($student->kelas->nama_kelas ?? 'N/A') . "\n";
} else {
    echo "   No active student found\n";
}
echo "\n";

// 6. Check Today's Attendance Records
echo "6. TODAY'S ATTENDANCE RECORDS (last 5):\n";
$records = DB::table('attendance_records')
    ->whereDate('date', today())
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($records->isEmpty()) {
    echo "   No attendance records today\n";
} else {
    foreach ($records as $record) {
        echo "   Student ID: {$record->student_id} | Status: {$record->status}\n";
        echo "   Check-in: " . ($record->check_in_time ?? 'NULL') . "\n";
        echo "   Check-out: " . ($record->check_out_time ?? 'NULL') . "\n";
    }
}
echo "\n";

echo "=== END DEBUG ===\n";
