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
        // Add notify_checkout setting to attendance_settings table
        DB::table('attendance_settings')->insert([
            'group_name' => 'notification',
            'key' => 'notify_checkout',
            'value' => 'true',
            'description' => 'Kirim notifikasi WA saat siswa check-out (pulang)',
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
            ->where('key', 'notify_checkout')
            ->delete();
    }
};
