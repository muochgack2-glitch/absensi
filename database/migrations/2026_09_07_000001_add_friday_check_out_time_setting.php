<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('attendance_settings')->upsert([
            [
                'key'         => 'friday_check_out_time',
                'value'       => '11:30',
                'group_name'  => 'time',
                'description' => 'Jam pulang resmi di hari Jumat (biasanya lebih awal dari hari lain)',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ], ['key'], ['value', 'group_name', 'description', 'updated_at']);
    }

    public function down(): void
    {
        DB::table('attendance_settings')->where('key', 'friday_check_out_time')->delete();
    }
};
