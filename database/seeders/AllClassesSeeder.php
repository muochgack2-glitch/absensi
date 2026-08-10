<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AllClassesSeeder extends Seeder
{
    /**
     * Run all class seeders.
     * Jalankan semua seeder kelas yang tersedia.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting All Classes Seeder...');
        $this->command->info('');

        // 1. Seed X Busana
        $this->command->info('📚 Seeding X Busana...');
        $this->call(XBusanaSeeder::class);
        $this->command->info('');

        // 2. Seed X MPLB
        $this->command->info('📚 Seeding X MPLB...');
        $this->call(XMPLBSeeder::class);
        $this->command->info('');

        // 3. Seed X AKL
        $this->command->info('📚 Seeding X AKL...');
        $this->call(XAKLSeeder::class);
        $this->command->info('');

        // 4. Seed XI Busana
        $this->command->info('📚 Seeding XI Busana...');
        $this->call(XIBusanaSeeder::class);
        $this->command->info('');

        // 5. Seed XI MPLB
        $this->command->info('📚 Seeding XI MPLB...');
        $this->call(XIMPLBSeeder::class);
        $this->command->info('');

        // 6. Seed XI AKL
        $this->command->info('📚 Seeding XI AKL...');
        $this->call(XIAKLSeeder::class);
        $this->command->info('');

        $this->command->info('✅ All classes seeded successfully!');
        $this->command->info('');
        $this->command->info('📊 Total Summary:');
        $this->command->info('   - X Busana: 13 siswa');
        $this->command->info('   - X MPLB: 29 siswa');
        $this->command->info('   - X AKL: 12 siswa');
        $this->command->info('   - XI Busana: 15 siswa');
        $this->command->info('   - XI MPLB: 29 siswa');
        $this->command->info('   - XI AKL: 5 siswa');
        $this->command->info('   - Total: 103 siswa, 6 kelas, 6 wali kelas');
    }
}
