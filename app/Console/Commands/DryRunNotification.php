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

        $cases = [
            ["CHECK-IN: Hadir", "check_in", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => "07:00", "status" => "Hadir", "tanggal" => $today,
                "hari_tanggal" => $hariTanggal, "terlambat" => 0,
            ]],
            ["CHECK-IN: Terlambat 35 Menit", "check_in", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => "07:35", "status" => "Terlambat", "tanggal" => $today,
                "hari_tanggal" => $hariTanggal, "terlambat" => 35,
            ]],
            ["CHECK-IN: Izin", "check_in", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => "07:00", "status" => "Izin", "tanggal" => $today,
                "hari_tanggal" => $hariTanggal, "terlambat" => 0,
            ]],
            ["CHECK-OUT: Pulang Normal", "check_out", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => $jamResmi, "status" => "Pulang Normal", "tanggal" => $today,
                "hari_tanggal" => $hariTanggal, "jam_resmi" => $jamResmi, "peringatan" => "",
            ]],
            ["CHECK-OUT: Pulang Lebih Awal (13:10)", "check_out", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "waktu" => "13:10", "status" => "Pulang Lebih Awal", "tanggal" => $today,
                "hari_tanggal" => $hariTanggal, "jam_resmi" => $jamResmi,
                "peringatan" => "Siswa meninggalkan sekolah sebelum jam pulang ({$jamResmi})",
            ]],
            ["TIDAK HADIR: Alpha", "absent", [
                "sekolah" => $schoolName, "nama" => $nama, "kelas" => $kelas,
                "tanggal" => $today, "hari_tanggal" => $hariTanggal,
            ]],
        ];

        foreach ($cases as [$judul, $type, $data]) {
            $this->newLine();
            $this->line(str_repeat("-", 60));
            $this->comment("TEST: {$judul}");
            $this->line(str_repeat("-", 60));
            $template = WhatsAppTemplate::where("is_active", true)
                ->where("auto_send", true)->where("type", $type)->first();
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
                $this->warn("[FALLBACK HARDCODE untuk type=" . $type . "]");
            }
        }

        $this->newLine();
        $this->line(str_repeat("=", 60));
        $this->comment("FALLBACK TEST");
        WhatsAppTemplate::where("type", "check_in")->update(["auto_send" => false]);
        $t = WhatsAppTemplate::where("is_active", true)->where("auto_send", true)->where("type", "check_in")->first();
        $t ? $this->error("FALLBACK GAGAL") : $this->info("Fallback berjalan benar");
        WhatsAppTemplate::where("type", "check_in")->update(["auto_send" => true]);
        $this->info("Template dikembalikan");

        $this->newLine();
        $this->line(str_repeat("=", 60));
        $this->comment("TEMPLATE STATUS DI DB");
        $rows = WhatsAppTemplate::select("name", "type", "auto_send", "is_active")->get()
            ->map(fn($t) => [$t->name, $t->type, $t->is_active ? "aktif" : "nonaktif", $t->auto_send ? "auto" : "manual"])
            ->toArray();
        $this->table(["Name", "Type", "Status", "Auto Send"], $rows);
    }
}
