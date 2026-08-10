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
        // Add notify_all_checkin setting to attendance_settings table
        DB::table('attendance_settings')->insert([
            'group_name' => 'notification',
            'key' => 'notify_all_checkin',
            'value' => 'true',
            'description' => 'Kirim notifikasi WA untuk semua check-in (hadir dan terlambat). Jika false, hanya kirim untuk terlambat.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('attendance_settings')
            ->where('key', 'notify_all_checkin')
            ->delete();
    }
};
