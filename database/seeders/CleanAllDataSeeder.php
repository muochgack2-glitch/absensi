<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceClass;
use App\Models\AttendanceStudent;
use App\Models\AttendanceRecord;
use App\Models\AttendanceLog;
use App\Models\AttendanceIzin;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CleanAllDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * HATI-HATI! Seeder ini akan menghapus SEMUA DATA:
     * - Siswa
     * - Kelas
     * - Users (kecuali admin)
     * - Records absensi
     * - Logs
     * - Izin
     * - QR Codes
     */
    public function run(): void
    {
        $this->command->warn('⚠️  WARNING: This will DELETE ALL DATA!');
        $this->command->warn('   - All Students');
        $this->command->warn('   - All Classes');
        $this->command->warn('   - All Users (except admin)');
        $this->command->warn('   - All Attendance Records');
        $this->command->warn('   - All Logs');
        $this->command->warn('   - All QR Codes');
        $this->command->info('');

        if (!$this->command->confirm('Are you sure you want to continue?', false)) {
            $this->command->info('❌ Operation cancelled.');
            return;
        }

        $this->command->info('');
        $this->command->info('🗑️  Starting cleanup...');
        $this->command->info('');

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Delete Attendance Records
        $recordsCount = AttendanceRecord::count();
        if ($recordsCount > 0) {
            AttendanceRecord::truncate();
            $this->command->info('✓ Deleted ' . $recordsCount . ' attendance records');
        }

        // 2. Delete Attendance Logs
        $logsCount = AttendanceLog::count();
        if ($logsCount > 0) {
            AttendanceLog::truncate();
            $this->command->info('✓ Deleted ' . $logsCount . ' attendance logs');
        }

        // 3. Delete Attendance Izin
        $izinCount = AttendanceIzin::count();
        if ($izinCount > 0) {
            AttendanceIzin::truncate();
            $this->command->info('✓ Deleted ' . $izinCount . ' izin records');
        }

        // 4. Delete QR Codes from storage
        if (Storage::disk('public')->exists('qr_codes')) {
            $qrFiles = Storage::disk('public')->files('qr_codes');
            $qrCount = count($qrFiles);
            if ($qrCount > 0) {
                Storage::disk('public')->deleteDirectory('qr_codes');
                Storage::disk('public')->makeDirectory('qr_codes');
                $this->command->info('✓ Deleted ' . $qrCount . ' QR code files');
            }
        }

        // 5. Delete Students
        $studentsCount = AttendanceStudent::withoutGlobalScope('tahun_ajaran')->count();
        if ($studentsCount > 0) {
            AttendanceStudent::withoutGlobalScope('tahun_ajaran')->forceDelete();
            $this->command->info('✓ Deleted ' . $studentsCount . ' students');
        }

        // 6. Delete Classes
        $classesCount = AttendanceClass::count();
        if ($classesCount > 0) {
            AttendanceClass::truncate();
            $this->command->info('✓ Deleted ' . $classesCount . ' classes');
        }

        // 7. Delete Users (except admin)
        $usersCount = User::where('role', '!=', 'admin')->count();
        if ($usersCount > 0) {
            User::where('role', '!=', 'admin')->delete();
            $this->command->info('✓ Deleted ' . $usersCount . ' users (kept admin accounts)');
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('');
        $this->command->info('✅ Cleanup completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Attendance Records: ' . $recordsCount . ' deleted');
        $this->command->info('   - Attendance Logs: ' . $logsCount . ' deleted');
        $this->command->info('   - Izin Records: ' . $izinCount . ' deleted');
        $this->command->info('   - Students: ' . $studentsCount . ' deleted');
        $this->command->info('   - Classes: ' . $classesCount . ' deleted');
        $this->command->info('   - Users: ' . $usersCount . ' deleted (admin kept)');
        $this->command->info('');
        $this->command->info('💡 You can now run: php artisan db:seed --class=XBusanaSeeder');
    }
}
