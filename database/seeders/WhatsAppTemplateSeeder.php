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
                "message"     => "🏫 *{sekolah}*\n📍 Notifikasi Absensi\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu Masuk: *{waktu}*\nStatus: ✅ Hadir\n\n_Pesan otomatis dari sistem absensi_",
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
                "message"     => "🏫 *{sekolah}*\n⏰ *Notifikasi Keterlambatan*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu Masuk: *{waktu}*\nKeterlambatan: *{terlambat} menit*\n\nMohon pastikan siswa hadir tepat waktu.\n\n_Pesan otomatis dari sistem absensi_",
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
                "message"     => "🏫 *{sekolah}*\n📝 *Notifikasi Izin*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu: *{waktu}*\nStatus: 📝 Izin\n\n_Pesan otomatis dari sistem absensi_",
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
                "message"     => "🏫 *{sekolah}*\n📍 Notifikasi Pulang\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu Pulang: *{waktu}*\nStatus: ✅ Pulang Normal\n\n_Pesan otomatis dari sistem absensi_",
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
                "message"     => "🏫 *{sekolah}*\n⚠️ *Notifikasi Pulang Lebih Awal*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu Pulang: *{waktu}*\nJam Resmi Pulang: {jam_resmi}\n\n⚠️ _Siswa meninggalkan sekolah sebelum jam pulang resmi._\n\n_Pesan otomatis dari sistem absensi_",
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
                "message"     => "🏫 *{sekolah}*\n⚠️ *Notifikasi Ketidakhadiran*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\nStatus: ❌ *Alpha (Tidak Hadir)*\n\nMohon segera menghubungi pihak sekolah.\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template notifikasi jika siswa tidak hadir (alpha) — dikirim otomatis oleh cron",
                "type"        => "absent",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "tanggal"]),
                "usage_count" => 0,
            ],
            // ── 7. Check-in: Hadir dalam Toleransi ───────────────────
            [
                "name"        => "check_in_toleransi",
                "label"       => "Check-In: Hadir (Toleransi)",
                "message"     => "🏫 *{sekolah}*\n✅ *Notifikasi Hadir (Toleransi)*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\nWaktu Masuk: *{waktu}*\nStatus: ✅ Hadir (dalam toleransi)\n\nℹ️ _Siswa masuk {terlambat} menit setelah jam resmi ({jam_resmi_masuk}). Batas toleransi sekolah: {toleransi} menit. Siswa tetap tercatat hadir._\n\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template soft-warning untuk siswa hadir setelah jam resmi tapi masih dalam batas toleransi. Gunakan {toleransi} untuk menit toleransi dari setting, {jam_resmi_masuk} untuk jam resmi masuk.",
                "type"        => "check_in",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah", "nama", "kelas", "hari_tanggal", "waktu", "terlambat", "toleransi", "jam_resmi_masuk"]),
                "usage_count" => 0,
            ],
            // ── 8. Koreksi Absensi Manual ─────────────────────────────
            [
                "name"        => "manual_correction",
                "label"       => "Koreksi Absensi Manual",
                "message"     => "🏫 *{sekolah}*\n🔄 *Koreksi Data Absensi*\n📅 {tanggal_absensi}\n\nSiswa: *{nama}*\nKelas: {kelas}\n\nStatus diperbarui:\n{status_lama}  →  {status_baru}\n⏰ Waktu Masuk: {waktu_masuk}\n📝 Keterangan: {keterangan}\n\n_Dikoreksi pada {tanggal_koreksi}. Mohon abaikan notifikasi sebelumnya._\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template WA koreksi saat admin mengubah status absensi alpha ke status lain melalui Input Manual. Variabel {keterangan} diisi admin, jika kosong default 'Koreksi oleh admin'.",
                "type"        => "manual",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah","nama","kelas","tanggal_absensi","tanggal_koreksi","status_lama","status_baru","waktu_masuk","keterangan"]),
                "usage_count" => 0,
            ],
            // ── 9. Manual Input: Hadir Tepat Waktu ───────────────────
            [
                "name"        => "manual_hadir",
                "label"       => "Manual Input: Hadir",
                "message"     => "🏫 *{sekolah}*\n✅ *Notifikasi Kehadiran*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\n⏰ Waktu Masuk: {waktu}\n📝 Keterangan: {keterangan}\n\n_Dicatat oleh admin sekolah — bukan QR scan_\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template WA saat admin menginput kehadiran tepat waktu via Input Manual (bukan QR scan)",
                "type"        => "manual",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah","nama","kelas","hari_tanggal","waktu","keterangan"]),
                "usage_count" => 0,
            ],
            // ── 10. Manual Input: Hadir dalam Toleransi ──────────────
            [
                "name"        => "manual_toleransi",
                "label"       => "Manual Input: Hadir (Toleransi)",
                "message"     => "🏫 *{sekolah}*\n✅ *Notifikasi Hadir (Toleransi)*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\n⏰ Waktu Masuk: {waktu}\nℹ️ Masuk {terlambat} menit setelah jam resmi ({jam_resmi_masuk}), masih dalam toleransi {toleransi} menit.\n📝 Keterangan: {keterangan}\n\n_Dicatat oleh admin sekolah — bukan QR scan_\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template WA saat admin menginput kehadiran dalam toleransi via Input Manual",
                "type"        => "manual",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah","nama","kelas","hari_tanggal","waktu","terlambat","toleransi","jam_resmi_masuk","keterangan"]),
                "usage_count" => 0,
            ],
            // ── 11. Manual Input: Terlambat ───────────────────────────
            [
                "name"        => "manual_terlambat",
                "label"       => "Manual Input: Terlambat",
                "message"     => "🏫 *{sekolah}*\n⚠️ *Notifikasi Terlambat*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\n⏰ Waktu Masuk: {waktu}\n📝 Keterangan: {keterangan}\n\n_Dicatat oleh admin sekolah — bukan QR scan_\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template WA saat admin menginput keterlambatan siswa via Input Manual",
                "type"        => "manual",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah","nama","kelas","hari_tanggal","waktu","keterangan"]),
                "usage_count" => 0,
            ],
            // ── 12. Manual Input: Izin ────────────────────────────────
            [
                "name"        => "manual_izin",
                "label"       => "Manual Input: Izin",
                "message"     => "🏫 *{sekolah}*\n📝 *Notifikasi Izin*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\n\n📝 {nama} hari ini *izin* tidak hadir.\nKeterangan: {keterangan}\n\n_Mohon hubungi sekolah jika ada informasi lebih lanjut._\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template WA saat admin menginput izin siswa via Input Manual",
                "type"        => "manual",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah","nama","kelas","hari_tanggal","keterangan"]),
                "usage_count" => 0,
            ],
            // ── 13. Manual Input: Sakit ───────────────────────────────
            [
                "name"        => "manual_sakit",
                "label"       => "Manual Input: Sakit",
                "message"     => "🏫 *{sekolah}*\n🤒 *Notifikasi Sakit*\n📅 {hari_tanggal}\n\nSiswa: *{nama}*\nKelas: {kelas}\n\n🤒 {nama} hari ini tidak hadir karena *sakit*.\nKeterangan: {keterangan}\n\n_Semoga lekas sembuh. Hubungi sekolah jika ada informasi lebih lanjut._\n_Pesan otomatis dari sistem absensi_",
                "description" => "Template WA saat admin menginput sakit siswa via Input Manual",
                "type"        => "manual",
                "is_active"   => true,
                "auto_send"   => true,
                "variables"   => json_encode(["sekolah","nama","kelas","hari_tanggal","keterangan"]),
                "usage_count" => 0,
            ],
            // ── Broadcast Umum (manual, untuk admin) ──────────────────
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

        // Hapus legacy templates jika masih ada di DB
        DB::table("whatsapp_templates")
            ->whereIn("name", ["check_in_notification", "check_out_notification", "late_notification"])
            ->delete();



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
