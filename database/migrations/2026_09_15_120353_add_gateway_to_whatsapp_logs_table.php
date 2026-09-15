<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            // 'primary' = gateway :3001 (wa-absensi), 'backup' = gateway :3000 (wa-spmb)
            $table->string('gateway', 20)->nullable()->default('primary')->after('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->dropColumn('gateway');
        });
    }
};
