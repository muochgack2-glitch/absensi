<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // MySQL: ubah enum untuk tambah 'petugas'
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','wali_kelas','petugas') NOT NULL DEFAULT 'admin'");
        }
        // SQLite tidak butuh perubahan — kolom sudah string, validasi di app layer
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','wali_kelas') NOT NULL DEFAULT 'admin'");
        }
    }
};
