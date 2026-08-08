<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhatsAppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // === General ===
            [
                'key' => 'wa_server_url',
                'value' => 'http://localhost:3001',
                'type' => 'string',
                'group' => 'general',
                'label' => 'WhatsApp Server URL',
                'description' => 'URL utama WhatsApp Gateway Server',
                'is_public' => false,
            ],
            [
                'key' => 'wa_server_url_backup',
                'value' => 'http://localhost:3000',
                'type' => 'string',
                'group' => 'general',
                'label' => 'WhatsApp Server Backup URL (SPMB)',
                'description' => 'URL backup gateway SPMB (port 3000) untuk failover otomatis',
                'is_public' => false,
            ],
            [
                'key' => 'wa_auto_send_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'general',
                'label' => 'Auto Send Enabled',
                'description' => 'Otomatis kirim notifikasi saat check-in/check-out/alpha',
                'is_public' => false,
            ],

            // === Connection ===
            [
                'key' => 'wa_timeout',
                'value' => '10',
                'type' => 'integer',
                'group' => 'connection',
                'label' => 'Connection Timeout',
                'description' => 'Timeout koneksi ke gateway (detik)',
                'is_public' => false,
            ],
            [
                'key' => 'wa_retry_attempts',
                'value' => '3',
                'type' => 'integer',
                'group' => 'connection',
                'label' => 'Retry Attempts',
                'description' => 'Jumlah percobaan kirim ulang jika gagal',
                'is_public' => false,
            ],
            [
                'key' => 'wa_failover_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'group' => 'connection',
                'label' => 'Auto Failover',
                'description' => 'Otomatis pindah ke backup gateway jika primary down',
                'is_public' => false,
            ],
            [
                'key' => 'wa_failover_timeout',
                'value' => '5',
                'type' => 'integer',
                'group' => 'connection',
                'label' => 'Failover Timeout',
                'description' => 'Timeout health check sebelum failover (detik)',
                'is_public' => false,
            ],

            // === Notification ===
            [
                'key' => 'wa_rate_limit',
                'value' => '20',
                'type' => 'integer',
                'group' => 'notification',
                'label' => 'Rate Limit',
                'description' => 'Maksimal pesan per menit',
                'is_public' => false,
            ],
            [
                'key' => 'wa_broadcast_delay',
                'value' => '2',
                'type' => 'integer',
                'group' => 'notification',
                'label' => 'Broadcast Delay',
                'description' => 'Delay antar pesan broadcast (detik)',
                'is_public' => false,
            ],

            // === Advanced ===
            [
                'key' => 'wa_log_retention_days',
                'value' => '90',
                'type' => 'integer',
                'group' => 'advanced',
                'label' => 'Log Retention',
                'description' => 'Berapa hari log pesan disimpan',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('whatsapp_settings')->updateOrInsert(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
