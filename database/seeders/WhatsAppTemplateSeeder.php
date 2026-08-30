<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WhatsAppTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // ── 1. Check-in: Hadir ────────────────────────────────────
            [
                "name"        => "check_in_hadir",
                "label"       => "Check-In: Hadir",
                "message"     => "🏫 *{sekolah}*\n📍 Notifikasi Absensi\n\nSiswa: *{nama}*\nKelas: {kelas}\nHari/Tgl: {hari_tanggal}\nWaktu Masuk: *{waktu}*\nStatus: ✅ Hadir\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template notifikasi saat siswa hadir tepat waktu",
                "type"        => "check_in",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "waktu"]),
                "usage_count" => 0,
            ],
            // ── 2. Check-in: Terlambat ────────────────────────────────
            [
                "name"        => "check_in_terlambat",
                "label"       => "Check-In: Terlambat",
                "message"     => "🏫 *{sekolah}*\n⏰ *Notifikasi Keterlambatan*\n\nSiswa: *{nama}*\nKelas: {kelas}\nHari/Tgl: {hari_tanggal}\nWaktu Masuk: *{waktu}*\nKeterlambatan: *{terlambat} menit*\n\nMohon pastikan siswa hadir tepat waktu.\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template notifikasi saat siswa terlambat masuk. Gunakan {terlambat} untuk menit keterlambatan.",
                "type"        => "check_in",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "waktu", "terlambat"]),
                "usage_count" => 0,
            ],
            // ── 3. Check-in: Izin ─────────────────────────────────────
            [
                "name"        => "check_in_izin",
                "label"       => "Check-In: Izin",
                "message"     => "🏫 *{sekolah}*\n📝 *Notifikasi Izin*\n\nSiswa: *{nama}*\nKelas: {kelas}\nHari/Tgl: {hari_tanggal}\nWaktu: *{waktu}*\nStatus: 📝 Izin\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template notifikasi saat siswa masuk dengan status izin",
                "type"        => "check_in",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "waktu"]),
                "usage_count" => 0,
            ],
            // ── 4. Check-out: Pulang Normal ───────────────────────────
            [
                "name"        => "check_out_normal",
                "label"       => "Check-Out: Pulang Normal",
                "message"     => "🏫 *{sekolah}*\n📍 Notifikasi Pulang\n\nSiswa: *{nama}*\nKelas: {kelas}\nHari/Tgl: {hari_tanggal}\nWaktu Pulang: *{waktu}*\nStatus: ✅ Pulang Normal\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template notifikasi saat siswa pulang tepat/setelah jam sekolah",
                "type"        => "check_out",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "waktu"]),
                "usage_count" => 0,
            ],
            // ── 5. Check-out: Pulang Cepat ────────────────────────────
            [
                "name"        => "check_out_cepat",
                "label"       => "Check-Out: Pulang Lebih Awal",
                "message"     => "🏫 *{sekolah}*\n⚠️ *Notifikasi Pulang Lebih Awal*\n\nSiswa: *{nama}*\nKelas: {kelas}\nHari/Tgl: {hari_tanggal}\nWaktu Pulang: *{waktu}*\nJam Resmi Pulang: {jam_resmi}\n\n⚠️ _Siswa meninggalkan sekolah sebelum jam pulang resmi._\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template notifikasi saat siswa pulang sebelum jam resmi. Gunakan {jam_resmi} untuk jam pulang sekolah.",
                "type"        => "check_out",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "waktu", "jam_resmi"]),
                "usage_count" => 0,
            ],
            // ── 6. Tidak Hadir: Alpha ─────────────────────────────────
            [
                "name"        => "absent_notification",
                "label"       => "Tidak Hadir (Alpha)",
                "message"     => "🏫 *{sekolah}*\n⚠️ *Notifikasi Ketidakhadiran*\n\nSiswa: *{nama}*\nKelas: {kelas}\nHari/Tgl: {hari_tanggal}\nStatus: ❌ *Alpha (Tidak Hadir)*\n\nMohon segera menghubungi pihak sekolah.\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template notifikasi jika siswa tidak hadir (alpha) — dikirim otomatis oleh cron",
                "type"        => "absent",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "tanggal"]),
                "usage_count" => 0,
            ],
            // ── Legacy (non-auto, tetap ada untuk referensi) ──────────
            [
                "name"        => "check_in_notification",
                "label"       => "[Legacy] Notifikasi Check-In",
                "message"     => "🏫 *{sekolah}*\n📍 Notifikasi Absensi\n\nSiswa: *{nama}*\nKelas: {kelas}\nHari/Tgl: {hari_tanggal}\nWaktu Masuk: *{waktu}*\nStatus: {status}\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "[Legacy] Tidak dipakai lagi — digantikan check_in_hadir, check_in_terlambat, check_in_izin",
                "type"        => "check_in",
                "is_active"   => false,
                "auto_send"   => false,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "waktu", "status"]),
                "usage_count" => 0,
            ],
            [
                "name"        => "check_out_notification",
                "label"       => "[Legacy] Notifikasi Check-Out",
                "message"     => "🏫 *{sekolah}*\n📍 Notifikasi Pulang\n\nSiswa: *{nama}*\nKelas: {kelas}\nHari/Tgl: {hari_tanggal}\nWaktu Pulang: *{waktu}*\n{peringatan}\n_Pesan otomatis dari sistem absensi_",
                "description" => "[Legacy] Tidak dipakai lagi — digantikan check_out_normal, check_out_cepat",
                "type"        => "check_out",
                "is_active"   => false,
                "auto_send"   => false,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "waktu", "peringatan", "jam_resmi"]),
                "usage_count" => 0,
            ],
            [
                "name"        => "late_notification",
                "label"       => "[Legacy] Notifikasi Terlambat",
                "message"     => "🏫 *{sekolah}*\n⏰ *Notifikasi Keterlambatan*\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu Masuk: *{waktu}*\nKeterlambatan: {terlambat} menit\n\nMohon untuk memastikan siswa hadir tepat waktu.\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "[Legacy] Digantikan oleh check_in_terlambat",
                "type"        => "check_in",
                "is_active"   => false,
                "auto_send"   => false,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "waktu", "terlambat"]),
                "usage_count" => 0,
            ],
            [
                "name"        => "broadcast_general",
                "label"       => "Broadcast Umum",
                "message"     => "🏫 *{sekolah}*\n📢 *Pengumuman*\n\n{pesan}\n\n_Pesan dari sistem absensi_",
                "description" => "Template untuk pengumuman umum ke orang tua",
                "type"        => "custom",
                "is_active"   => true,
                "auto_send"   => false,
                "variables"   => json_encode(["sekolah", "pesan"]),
                "usage_count" => 0,
            ],
        ];

        foreach ($templates as $template) {
            DB::table("whatsapp_templates")->updateOrInsert(
                ["name" => $template["name"]],
                array_merge($template, [
                    "created_at" => now(),
                    "updated_at" => now(),
                ])
            );
        }
    }
}
