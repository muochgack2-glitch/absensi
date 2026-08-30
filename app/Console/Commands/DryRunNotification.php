<?php

namespace App\Console\Commands;

use App\Models\AttendanceSetting;
use App\Models\AttendanceStudent;
use App\Models\WhatsAppTemplate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DryRunNotification extends Command
{
    protected $signature   = "notif:dry-run {--nis= : NIS siswa untuk dry run dengan data asli}";
    protected $description = "Dry run semua kondisi notifikasi WA tanpa kirim WA. Gunakan --nis=XXXX untuk data siswa asli.";

    public function handle(): void
    {
        $schoolName  = AttendanceSetting::get("school_name", "SMK PGRI Blora");
        $jamResmi    = AttendanceSetting::get("check_out_time", "15:00");
        $today       = Carbon::today()->format("d/m/Y");
        $hariTanggal = Carbon::today()->locale("id")->translatedFormat("l, d/m/Y");

        $nis    = $this->option("nis");
        $nama   = "Ahmad Rizki";
        $kelas  = "X Busana 1";
        $hpOrtu = "-";

        if ($nis) {
            $student = AttendanceStudent::with("kelas")->where("nis", $nis)->first();
            if (!$student) {
                $this->error("Siswa dengan NIS {$nis} tidak ditemukan.");
                return;
            }
            $nama   = $student->nama;
            $kelas  = $student->kelas->nama_kelas;
            $hpOrtu = $student->no_hp_ortu ?: "(tidak ada HP ortu)";
            $this->info("Menggunakan data asli: {$nama} -- {$kelas} | HP: {$hpOrtu}");
        } else {
            $this->comment("Menggunakan data dummy. Gunakan --nis=XXXX untuk data asli.");
        }

        $this->info("");
        $this->info("Sekolah : {$schoolName}");
        $this->info("Hari/Tgl: {$hariTanggal}");

        // 1 kondisi = 1 template name
        $peringatanCepat = "Siswa meninggalkan sekolah sebelum jam pulang ({$jamResmi})";
        $cases = [
            ["CHECK-IN: Hadir", "check_in_hadir", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => "07:00", "tanggal" => $today, "hari_tanggal" => $hariTanggal,
                "terlambat" => 0, "toleransi" => 15, "jam_resmi_masuk" => "07:00",
            ]],
            ["CHECK-IN: Hadir dalam Toleransi (07:10)", "check_in_toleransi", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => "07:10", "tanggal" => $today, "hari_tanggal" => $hariTanggal,
                "terlambat" => 10, "toleransi" => 15, "jam_resmi_masuk" => "07:00",
            ]],
            ["CHECK-IN: Terlambat 35 Menit", "check_in_terlambat", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => "07:35", "tanggal" => $today, "hari_tanggal" => $hariTanggal, "terlambat" => 35,
            ]],
            ["CHECK-IN: Izin", "check_in_izin", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => "07:00", "tanggal" => $today, "hari_tanggal" => $hariTanggal, "terlambat" => 0,
            ]],
            ["CHECK-OUT: Pulang Normal", "check_out_normal", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => $jamResmi, "tanggal" => $today, "hari_tanggal" => $hariTanggal,
                "jam_resmi" => $jamResmi, "peringatan" => "",
            ]],
            ["CHECK-OUT: Pulang Lebih Awal (13:10)", "check_out_cepat", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => "13:10", "tanggal" => $today, "hari_tanggal" => $hariTanggal,
                "jam_resmi" => $jamResmi, "peringatan" => $peringatanCepat,
            ]],
            ["TIDAK HADIR: Alpha", "absent_notification", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "tanggal" => $today, "hari_tanggal" => $hariTanggal,
            ]],
            ["KOREKSI: Alpha → Hadir (tidak bawa kartu)", "manual_correction", [
                "sekolah"         => $schoolName,
                "nama"            => $nama,
                "kelas"           => $kelas,
                "tanggal_absensi" => $hariTanggal,
                "tanggal_koreksi" => now()->format('d/m/Y'),
                "status_lama"     => "❌ Alpha",
                "status_baru"     => "✅ Hadir",
                "waktu_masuk"     => "06:58",
                "keterangan"      => "Tidak bawa kartu, sudah divalidasi guru piket",
            ]],
            ["KOREKSI: Alpha → Izin (surat terlambat)", "manual_correction", [
                "sekolah"         => $schoolName,
                "nama"            => $nama,
                "kelas"           => $kelas,
                "tanggal_absensi" => $hariTanggal,
                "tanggal_koreksi" => now()->format('d/m/Y'),
                "status_lama"     => "❌ Alpha",
                "status_baru"     => "📝 Izin",
                "waktu_masuk"     => "-",
                "keterangan"      => "Surat izin terlambat disampaikan wali kelas",
            ]],
            ["MANUAL FIRST: Hadir tepat waktu (lupa kartu)", "manual_hadir", [
                "sekolah"     => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "hari_tanggal"=> $hariTanggal, "waktu" => "06:58",
                "keterangan"  => "Tidak bawa kartu QR",
            ]],
            ["MANUAL FIRST: Hadir dalam toleransi (07:10)", "manual_toleransi", [
                "sekolah"         => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "hari_tanggal"    => $hariTanggal, "waktu" => "07:10",
                "terlambat"       => 10, "toleransi" => 15, "jam_resmi_masuk" => "07:00",
                "keterangan"      => "Tidak bawa kartu, masuk toleransi",
            ]],
            ["MANUAL FIRST: Terlambat", "manual_terlambat", [
                "sekolah"     => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "hari_tanggal"=> $hariTanggal, "waktu" => "09:30",
                "keterangan"  => "Ban sepeda bocor",
            ]],
            ["MANUAL FIRST: Izin (SOP tepat waktu)", "manual_izin", [
                "sekolah"     => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "hari_tanggal"=> $hariTanggal,
                "keterangan"  => "Izin keperluan keluarga",
            ]],
            ["MANUAL FIRST: Sakit", "manual_sakit", [
                "sekolah"     => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "hari_tanggal"=> $hariTanggal,
                "keterangan"  => "Demam tinggi sejak kemarin",
            ]],
            ["STATUS CHANGE: Terlambat → Sakit", "manual_status_change", [
                "sekolah"         => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "hari_tanggal"    => $hariTanggal,
                "tanggal_koreksi" => now()->format('d/m/Y'),
                "status_lama"     => "⚠️ Terlambat",
                "status_baru"     => "🤒 Sakit",
                "keterangan"      => "Pulang ke rumah karena pusing",
            ]],
            ["STATUS CHANGE: Sakit → Terlambat", "manual_status_change", [
                "sekolah"         => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "hari_tanggal"    => $hariTanggal,
                "tanggal_koreksi" => now()->format('d/m/Y'),
                "status_lama"     => "🤒 Sakit",
                "status_baru"     => "⚠️ Terlambat",
                "keterangan"      => "Ternyata masuk setelah berobat",
            ]],
        ];

        foreach ($cases as [$judul, $templateName, $data]) {
            $this->newLine();
            $this->line(str_repeat("-", 60));
            $this->comment("TEST: {$judul}");
            $this->line(str_repeat("-", 60));
            $template = WhatsAppTemplate::where("name", $templateName)
                ->where("is_active", true)->where("auto_send", true)->first();
            if ($template) {
                $this->line("[TEMPLATE: {$template->name}]");
                $msg = $template->message;
                foreach ($data as $key => $val) {
                    $msg = str_replace("{" . $key . "}", $val, $msg);
                }
                preg_match_all("/\{([a-z_]+)\}/", $msg, $unreplaced);
                $this->line($msg);
                if (!empty($unreplaced[1])) {
                    $this->warn("Var belum terganti: {" . implode("}, {", $unreplaced[1]) . "}");
                }
            } else {
                $this->warn("[FALLBACK HARDCODE -- template '{$templateName}' tidak aktif atau tidak ada]");
            }
        }

        $this->newLine();
        $this->line(str_repeat("=", 60));
        $this->comment("TEMPLATE STATUS DI DB");
        $rows = WhatsAppTemplate::select("name", "type", "is_active", "auto_send")->get()
            ->map(fn($t) => [
                $t->name,
                $t->type,
                $t->is_active ? "aktif" : "nonaktif",
                $t->auto_send ? "AUTO" : "manual",
            ])->toArray();
        $this->table(["Name", "Type", "Status", "Auto Send"], $rows);
    }
}
