<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert late warning settings
        DB::table('attendance_settings')->insert([
            [
                'key' => 'late_warning_enabled',
                'value' => '0',
                'group_name' => 'notification',
                'description' => 'Enable/disable late warning notifications (1=enabled, 0=disabled)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_warning_threshold_minutes',
                'value' => '30',
                'group_name' => 'notification',
                'description' => 'Minimum minutes late to trigger warning notification',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'late_warning_min_count',
                'value' => '3',
                'group_name' => 'notification',
                'description' => 'Minimum number of late occurrences in a month before sending warning',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove late warning settings
        DB::table('attendance_settings')->whereIn('key', [
            'late_warning_enabled',
            'late_warning_threshold_minutes',
            'late_warning_min_count',
        ])->delete();
    }
};
