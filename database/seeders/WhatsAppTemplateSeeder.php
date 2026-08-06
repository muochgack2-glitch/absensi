<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhatsAppTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'check_in_notification',
                'label' => 'Notifikasi Check-In',
                'message' => "🏫 *{sekolah}*\n📍 Notifikasi Absensi\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu Masuk: *{waktu}*\nStatus: {status}\n\n_Pesan otomatis dari sistem absensi_",
                'description' => 'Template notifikasi saat siswa check-in / masuk sekolah',
                'type' => 'check_in',
                'is_active' => true,
                'auto_send' => true,
                'variables' => json_encode(['sekolah', 'nama', 'kelas', 'waktu', 'status']),
                'usage_count' => 0,
            ],
            [
                'name' => 'check_out_notification',
                'label' => 'Notifikasi Check-Out',
                'message' => "🏫 *{sekolah}*\n📍 Notifikasi Pulang\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu Pulang: *{waktu}*\n\n_Pesan otomatis dari sistem absensi_",
                'description' => 'Template notifikasi saat siswa check-out / pulang sekolah',
                'type' => 'check_out',
                'is_active' => true,
                'auto_send' => true,
                'variables' => json_encode(['sekolah', 'nama', 'kelas', 'waktu']),
                'usage_count' => 0,
            ],
            [
                'name' => 'absent_notification',
                'label' => 'Notifikasi Tidak Hadir',
                'message' => "🏫 *{sekolah}*\n⚠️ *Notifikasi Ketidakhadiran*\n\nSiswa: *{nama}*\nKelas: {kelas}\nTanggal: {tanggal}\nStatus: ❌ *Alpha (Tidak Hadir)*\n\nMohon segera menghubungi pihak sekolah.\n\n_Pesan otomatis dari sistem absensi_",
                'description' => 'Template notifikasi jika siswa tidak hadir (alpha)',
                'type' => 'absent',
                'is_active' => true,
                'auto_send' => true,
                'variables' => json_encode(['sekolah', 'nama', 'kelas', 'tanggal']),
                'usage_count' => 0,
            ],
            [
                'name' => 'late_notification',
                'label' => 'Notifikasi Terlambat',
                'message' => "🏫 *{sekolah}*\n⏰ *Notifikasi Keterlambatan*\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu Masuk: *{waktu}*\nKeterlambatan: {terlambat} menit\n\nMohon untuk memastikan siswa hadir tepat waktu.\n\n_Pesan otomatis dari sistem absensi_",
                'description' => 'Template notifikasi jika siswa terlambat masuk',
                'type' => 'check_in',
                'is_active' => true,
                'auto_send' => false,
                'variables' => json_encode(['sekolah', 'nama', 'kelas', 'waktu', 'terlambat']),
                'usage_count' => 0,
            ],
            [
                'name' => 'broadcast_general',
                'label' => 'Broadcast Umum',
                'message' => "🏫 *{sekolah}*\n📢 *Pengumuman*\n\n{pesan}\n\n_Pesan dari sistem absensi_",
                'description' => 'Template untuk pengumuman umum ke orang tua',
                'type' => 'custom',
                'is_active' => true,
                'auto_send' => false,
                'variables' => json_encode(['sekolah', 'pesan']),
                'usage_count' => 0,
            ],
        ];

        foreach ($templates as $template) {
            DB::table('whatsapp_templates')->updateOrInsert(
                ['name' => $template['name']],
                array_merge($template, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
