<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            // Status checkout: null = belum checkout, 'pulang' = tepat waktu, 'pulang_cepat' = sebelum jadwal
            $table->string('check_out_status', 20)->nullable()->after('check_out_photo');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropColumn('check_out_status');
        });
    }
};
