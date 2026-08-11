<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DEBUG FAILOVER ===\n\n";

// 1. Check Settings
echo "1. FAILOVER SETTINGS:\n";
echo "   Primary URL: " . App\Models\WhatsAppSetting::get('wa_server_url') . "\n";
echo "   Backup URL: " . App\Models\WhatsAppSetting::get('wa_server_url_backup') . "\n";
echo "   Failover Enabled: " . var_export(App\Models\WhatsAppSetting::get('wa_failover_enabled'), true) . "\n";
echo "   Failover Timeout: " . App\Models\WhatsAppSetting::get('wa_failover_timeout', 5) . " seconds\n";
echo "\n";

// 2. Test Gateway Health
echo "2. GATEWAY HEALTH CHECK:\n";

$service = app(App\Services\AttendanceWhatsAppService::class);

// Test primary
$primary = App\Models\WhatsAppSetting::get('wa_server_url');
echo "   Testing PRIMARY ({$primary}):\n";
try {
    $response = \Illuminate\Support\Facades\Http::timeout(5)->get("{$primary}/status");
    if ($response->successful()) {
        $data = $response->json();
        echo "     Status: " . ($data['status'] ?? 'unknown') . "\n";
        echo "     Connected: " . ($data['status'] === 'connected' ? 'YES' : 'NO') . "\n";
    } else {
        echo "     Status: FAILED (HTTP " . $response->status() . ")\n";
    }
} catch (Exception $e) {
    echo "     Status: ERROR - " . $e->getMessage() . "\n";
}

// Test backup
$backup = App\Models\WhatsAppSetting::get('wa_server_url_backup');
echo "\n   Testing BACKUP ({$backup}):\n";
try {
    $response = \Illuminate\Support\Facades\Http::timeout(5)->get("{$backup}/status");
    if ($response->successful()) {
        $data = $response->json();
        echo "     Status: " . ($data['status'] ?? 'unknown') . "\n";
        echo "     Connected: " . ($data['status'] === 'connected' ? 'YES' : 'NO') . "\n";
    } else {
        echo "     Status: FAILED (HTTP " . $response->status() . ")\n";
    }
} catch (Exception $e) {
    echo "     Status: ERROR - " . $e->getMessage() . "\n";
}
echo "\n";

// 3. Test getActiveServerUrl
echo "3. ACTIVE SERVER URL:\n";
$activeUrl = $service->getCurrentServerUrl();
echo "   System will use: {$activeUrl}\n";
echo "\n";

// 4. Check Recent Logs
echo "4. RECENT WHATSAPP LOGS (last 5):\n";
$logs = DB::table('whatsapp_logs')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

if ($logs->isEmpty()) {
    echo "   No logs found\n";
} else {
    foreach ($logs as $log) {
        echo "   [{$log->id}] {$log->type} | {$log->status} | {$log->phone}\n";
        echo "       Time: {$log->created_at}\n";
        if ($log->error_message) {
            echo "       Error: " . substr($log->error_message, 0, 150) . "...\n";
        }
    }
}
echo "\n";

// 5. Check Attendance Logs
echo "5. RECENT ATTENDANCE LOGS (notification related):\n";
$attendanceLogs = DB::table('attendance_logs')
    ->where('action', 'notification')
    ->orderBy('created_at', 'desc')
    ->limit(3)
    ->get();

if ($attendanceLogs->isEmpty()) {
    echo "   No notification logs found\n";
} else {
    foreach ($attendanceLogs as $log) {
        echo "   [{$log->id}] {$log->action} | {$log->status}\n";
        echo "       Message: {$log->message}\n";
        echo "       Time: {$log->created_at}\n";
    }
}
echo "\n";

echo "=== END DEBUG ===\n";
