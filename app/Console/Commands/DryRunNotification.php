<?php

namespace App\Console\Commands;

use App\Models\AttendanceSetting;
use App\Models\WhatsAppTemplate;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DryRunNotification extends Command
{
    protected $signature   = 'notif:dry-run';
    protected $description = 'Dry run semua kondisi notifikasi WA tanpa kirim WA';

    public function handle(): void
    {
        $schoolName  = AttendanceSetting::get('school_name', 'SMK PGRI Blora');
        $today       = Carbon::today()->format('d/m/Y');
        $hariTanggal = Carbon::today()->locale('id')->translatedFormat('l, d/m/Y');

        $this->info('');
        $this->info('╔══════════════════════════════════════════════════════════╗');
        $this->info('║         DRY RUN — WA Notification Templates             ║');
        $this->info('╚══════════════════════════════════════════════════════════╝');
        $this->info("Sekolah : {$schoolName}");
        $this->info("Hari/Tgl: {$hariTanggal}");

        $cases = [
            ['CHECK-IN: Hadir Tepat Waktu', 'check_in', [
                'sekolah' => $schoolName, 'nama' => 'Ahmad Rizki', 'kelas' => 'X Busana 1',
                'waktu' => '07:00', 'status' => '✅ Hadir', 'tanggal' => $today,
                'hari_tanggal' => $hariTanggal, 'terlambat' => 0,
            ]],
            ['CHECK-IN: Terlambat 35 Menit', 'check_in', [
                'sekolah' => $schoolName, 'nama' => 'Ahmad Rizki', 'kelas' => 'X Busana 1',
                'waktu' => '07:35', 'status' => '⚠️ Terlambat', 'tanggal' => $today,
                'hari_tanggal' => $hariTanggal, 'terlambat' => 35,
            ]],
            ['CHECK-IN: Izin', 'check_in', [
                'sekolah' => $schoolName, 'nama' => 'Budi Santoso', 'kelas' => 'XI TKJ 1',
                'waktu' => '07:00', 'status' => '📝 Izin', 'tanggal' => $today,
                'hari_tanggal' => $hariTanggal, 'terlambat' => 0,
            ]],
            ['CHECK-OUT: Pulang Normal', 'check_out', [
                'sekolah'      => $schoolName, 'nama' => 'Ahmad Rizki', 'kelas' => 'X Busana 1',
                'waktu'        => '15:30', 'status' => '✅ Pulang Normal', 'tanggal' => $today,
                'hari_tanggal' => $hariTanggal,
                'jam_resmi'    => '15:00',
                'peringatan'   => '', // kosong = pulang normal
            ]],
            ['CHECK-OUT: Pulang Lebih Awal', 'check_out', [
                'sekolah'      => $schoolName, 'nama' => 'Siti Rahayu', 'kelas' => 'XII AKL 2',
                'waktu'        => '13:10', 'status' => '⚠️ Pulang Lebih Awal', 'tanggal' => $today,
                'hari_tanggal' => $hariTanggal,
                'jam_resmi'    => '15:00',
                'peringatan'   => '⚠️ _Siswa meninggalkan sekolah sebelum jam pulang (15:00)_',
            ]],
            ['TIDAK HADIR: Alpha', 'absent', [
                'sekolah' => $schoolName, 'nama' => 'Dewi Anggraini', 'kelas' => 'XI RPL 1',
                'tanggal' => $today, 'hari_tanggal' => $hariTanggal,
            ]],
        ];

        foreach ($cases as [$judul, $type, $data]) {
            $this->newLine();
            $this->line(str_repeat('─', 60));
            $this->comment("🧪 {$judul}");
            $this->line(str_repeat('─', 60));
            $template = WhatsAppTemplate::where('is_active', true)
                ->where('auto_send', true)->where('type', $type)->first();
            if ($template) {
                $this->line("<fg=green>[TEMPLATE: {$template->name}}</>");
                $msg = $template->message;
                foreach ($data as $key => $val) {
                    $msg = str_replace('{' . $key . '}', $val, $msg);
                }
                preg_match_all('/\{([a-z_]+)\}/', $msg, $unreplaced);
                $this->line($msg);
                if (!empty($unreplaced[1])) {
                    $this->warn('⚠️  Var belum terganti: {' . implode('}, {', $unreplaced[1]) . '}');
                }
            } else {
                $this->warn('[FALLBACK HARDCODE — tidak ada template aktif untuk type=' . $type . ']');
            }
        }

        // Fallback test
        $this->newLine();
        $this->line(str_repeat('═', 60));
        $this->comment('🔄 FALLBACK TEST');
        WhatsAppTemplate::where('type', 'check_in')->update(['auto_send' => false]);
        $t = WhatsAppTemplate::where('is_active', true)->where('auto_send', true)->where('type', 'check_in')->first();
        $t ? $this->error('❌ Fallback GAGAL') : $this->info('✅ Fallback berjalan benar');
        WhatsAppTemplate::where('type', 'check_in')->update(['auto_send' => true]);
        $this->info('✅ Template dikembalikan');

        // Summary table
        $this->newLine();
        $this->line(str_repeat('═', 60));
        $this->comment('📊 TEMPLATE STATUS');
        $rows = WhatsAppTemplate::select('name','type','auto_send','is_active')->get()
            ->map(fn($t) => [$t->name, $t->type, $t->is_active ? '🟢 aktif' : '🔴 nonaktif', $t->auto_send ? '✅ auto' : '⬜ manual'])
            ->toArray();
        $this->table(['Name','Type','Status','Auto Send'], $rows);
    }
}
