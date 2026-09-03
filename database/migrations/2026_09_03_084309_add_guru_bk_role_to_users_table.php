<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'admin','wali_kelas','petugas','kepala_sekolah','waka_kesiswaan','guru_bk'
        ) NOT NULL DEFAULT 'wali_kelas'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'admin','wali_kelas','petugas','kepala_sekolah','waka_kesiswaan'
        ) NOT NULL DEFAULT 'wali_kelas'");
    }
};
