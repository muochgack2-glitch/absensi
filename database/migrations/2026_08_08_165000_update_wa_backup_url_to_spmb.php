<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Update wa_server_url_backup default ke SPMB port 3000
     * Ini diperlukan karena seeder tidak mengupdate data existing.
     */
    public function up(): void
    {
        // Update backup URL jika masih kosong
        DB::table('whatsapp_settings')
            ->where('key', 'wa_server_url_backup')
            ->where(function ($q) {
                $q->where('value', '')
                  ->orWhereNull('value');
            })
            ->update([
                'value' => 'http://localhost:3000',
                'label' => 'WhatsApp Server Backup URL (SPMB)',
                'description' => 'URL backup gateway SPMB (port 3000) untuk failover otomatis',
            ]);

        // Jika belum ada sama sekali, insert
        $exists = DB::table('whatsapp_settings')
            ->where('key', 'wa_server_url_backup')
            ->exists();

        if (!$exists) {
            DB::table('whatsapp_settings')->insert([
                'key' => 'wa_server_url_backup',
                'value' => 'http://localhost:3000',
                'type' => 'string',
                'group' => 'general',
                'label' => 'WhatsApp Server Backup URL (SPMB)',
                'description' => 'URL backup gateway SPMB (port 3000) untuk failover otomatis',
                'is_public' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Clear cache agar production ambil value baru
        DB::table('cache')->where('key', 'like', '%wa_setting%')->delete();
    }

    public function down(): void
    {
        DB::table('whatsapp_settings')
            ->where('key', 'wa_server_url_backup')
            ->update([
                'value' => '',
                'label' => 'WhatsApp Server Backup URL',
                'description' => 'URL backup gateway untuk failover',
            ]);
    }
};
