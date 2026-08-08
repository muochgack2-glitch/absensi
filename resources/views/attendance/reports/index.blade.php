<x-app-layout>
    <x-slot name="title">Laporan</x-slot>
    <x-slot name="pageTitle">Laporan Absensi</x-slot>

    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan Absensi</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Generate dan export laporan absensi siswa</p>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300">
                <i class="fas fa-times-circle text-red-500 text-lg"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif
        @if(session('info'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300">
                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
        @endif

        {{-- Quick Stats Hari Ini --}}
        @php
            $todayRecords = \App\Models\AttendanceRecord::today();
            $todayHadir = (clone $todayRecords)->where('status', 'hadir')->count();
            $todayTerlambat = (clone $todayRecords)->where('status', 'terlambat')->count();
            $todayIzinSakit = (clone $todayRecords)->whereIn('status', ['izin', 'sakit'])->count();
            $todayAlpha = (clone $todayRecords)->where('status', 'alpha')->count();
        @endphp
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-stat-card title="Hadir Hari Ini" :value="$todayHadir" icon="fas fa-check-circle" color="success" />
            <x-stat-card title="Terlambat" :value="$todayTerlambat" icon="fas fa-clock" color="warning" />
            <x-stat-card title="Izin / Sakit" :value="$todayIzinSakit" icon="fas fa-file-medical" color="info" />
            <x-stat-card title="Alpha" :value="$todayAlpha" icon="fas fa-times-circle" color="danger" />
        </div>

        {{-- Quick Links Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Daily Report --}}
            <a href="{{ route('attendance.reports.daily') }}" 
               class="group relative bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">📅</div>
                    <h3 class="text-lg font-bold mb-2">Laporan Harian</h3>
                    <p class="text-sm text-blue-100 flex-grow">Absensi hari ini real-time</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Lihat Detail</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            {{-- Monthly Report --}}
            <a href="{{ route('attendance.reports.monthly') }}" 
               class="group relative bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">📆</div>
                    <h3 class="text-lg font-bold mb-2">Laporan Bulanan</h3>
                    <p class="text-sm text-purple-100 flex-grow">Rekapitulasi per bulan</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Lihat Detail</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            {{-- Custom Report --}}
            <a href="#custom" 
               class="group relative bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">🔍</div>
                    <h3 class="text-lg font-bold mb-2">Laporan Custom</h3>
                    <p class="text-sm text-orange-100 flex-grow">Filter kustom sesuai kebutuhan</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Generate</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            {{-- Export Excel --}}
            <a href="{{ route('attendance.reports.export-summary') }}" 
               onclick="return confirm('Export ringkasan absensi bulan ini ke Excel?')"
               class="group relative bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">📥</div>
                    <h3 class="text-lg font-bold mb-2">Export Excel</h3>
                    <p class="text-sm text-green-100 flex-grow">Download ringkasan bulan ini</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Download</span>
                        <i class="fas fa-download ml-2 transform group-hover:translate-y-1 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Baris kedua: Laporan Alpha --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
            {{-- Laporan Alpha --}}
            <a href="{{ route('attendance.reports.alpha') }}"
               class="group relative bg-gradient-to-br from-red-500 to-orange-500 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">⚠️</div>
                    <h3 class="text-lg font-bold mb-2">Laporan Alpha</h3>
                    <p class="text-sm text-red-100 flex-grow">Siswa sering tidak hadir + kirim WA ke ortu</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Lihat & Kirim WA</span>
                        <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-2 transition-transform"></i>
                    </div>
                </div>
            </a>

            {{-- Export Siswa Excel --}}
            <a href="{{ route('attendance.students.export.excel') }}"
               class="group relative bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl p-6 text-white hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                <div class="flex flex-col h-full">
                    <div class="text-4xl mb-4 transform group-hover:scale-110 transition-transform">👥</div>
                    <h3 class="text-lg font-bold mb-2">Export Data Siswa</h3>
                    <p class="text-sm text-teal-100 flex-grow">Download daftar semua siswa ke Excel</p>
                    <div class="mt-4 flex items-center text-sm font-medium">
                        <span>Download</span>
                        <i class="fas fa-download ml-2 transform group-hover:translate-y-1 transition-transform"></i>
                    </div>
                </div>
            </a>
        </div>

        {{-- Generate Report Form --}}
        <x-card id="custom">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white text-2xl mr-4">
                    <i class="fas fa-file-chart"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Generate Laporan Custom</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Filter data sesuai kebutuhan Anda</p>
                </div>
            </div>

            <form action="{{ route('attendance.reports.generate') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    {{-- Date Range --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-input
                            type="date"
                            name="start_date"
                            label="Tanggal Mulai"
                            :value="old('start_date', date('Y-m-01'))"
                            required
                            :error="$errors->first('start_date')"
                        />

                        <x-input
                            type="date"
                            name="end_date"
                            label="Tanggal Akhir"
                            :value="old('end_date', date('Y-m-d'))"
                            required
                            :error="$errors->first('end_date')"
                        />
                    </div>

                    {{-- Filters --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-select
                            name="class_id"
                            label="Filter Kelas"
                            :error="$errors->first('class_id')"
                        >
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->nama_kelas }}
                                </option>
                            @endforeach
                        </x-select>

                        <x-select
                            name="status"
                            label="Filter Status"
                            :error="$errors->first('status')"
                        >
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ old('status') == 'hadir' ? 'selected' : '' }}>✅ Hadir</option>
                            <option value="terlambat" {{ old('status') == 'terlambat' ? 'selected' : '' }}>⏰ Terlambat</option>
                            <option value="sakit" {{ old('status') == 'sakit' ? 'selected' : '' }}>🤒 Sakit</option>
                            <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>📝 Izin</option>
                            <option value="alpha" {{ old('status') == 'alpha' ? 'selected' : '' }}>❌ Alpha</option>
                        </x-select>
                    </div>

                    {{-- Format --}}
                    <x-select
                        name="format"
                        label="Format Output"
                        required
                        :error="$errors->first('format')"
                    >
                        <option value="preview" {{ old('format') == 'preview' ? 'selected' : '' }}>👁️ Preview (di layar)</option>
                        <option value="excel" {{ old('format') == 'excel' ? 'selected' : '' }}>📥 Excel (.xlsx)</option>
                    </x-select>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end gap-3 pt-4">
                        <a
                            href="{{ route('attendance.dashboard') }}"
                            class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                        >
                            Batal
                        </a>
                        <button
                            type="submit"
                            id="btnGenerate"
                            class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 shadow-md hover:shadow-lg"
                        >
                            <i class="fas fa-search mr-2" id="btnIcon"></i>
                            <span id="btnText">Generate Laporan</span>
                        </button>
                    </div>
                </div>
            </form>
        </x-card>

        @push('scripts')
        <script>
            document.querySelector('form[action*="generate"]').addEventListener('submit', function() {
                const btn = document.getElementById('btnGenerate');
                const icon = document.getElementById('btnIcon');
                const text = document.getElementById('btnText');
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                icon.className = 'fas fa-spinner fa-spin mr-2';
                text.textContent = 'Memproses...';
            });
        </script>
        @endpush

        {{-- Instructions --}}
        <x-card>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">💡 Panduan Penggunaan</h3>
                    <ul class="space-y-2 text-gray-700 dark:text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Laporan Harian:</strong> Lihat absensi hari ini secara real-time dengan status lengkap</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Laporan Bulanan:</strong> Rekapitulasi absensi per siswa dalam 1 bulan untuk evaluasi</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Laporan Custom:</strong> Filter berdasarkan tanggal, kelas, dan status tertentu sesuai kebutuhan</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Export Excel:</strong> Download data dalam format .xlsx untuk analisis lebih lanjut</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span><strong>Preview:</strong> Tampilkan data di layar terlebih dahulu sebelum melakukan export</span>
                        </li>
                    </ul>
                </div>
            </div>
        </x-card>
    </div>
</x-app-layout>
