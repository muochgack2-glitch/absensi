<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECK SETTINGS GROUP ===\n\n";

$timeSettings = DB::table('attendance_settings')
    ->whereIn('key', ['check_in_time', 'check_out_time', 'tolerance_minutes', 'cutoff_time'])
    ->select('key', 'value', 'group_name')
    ->get();

foreach ($timeSettings as $setting) {
    echo "{$setting->key}:\n";
    echo "  Value: {$setting->value}\n";
    echo "  Group: {$setting->group_name}\n\n";
}

echo "=== GROUPED SETTINGS ===\n\n";
$grouped = App\Models\AttendanceSetting::getGrouped();
print_r($grouped);
