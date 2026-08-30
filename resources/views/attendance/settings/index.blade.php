@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="title">Settings</x-slot>
    <x-slot name="pageTitle">Pengaturan Sistem</x-slot>

    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">⚙️ Pengaturan Sistem</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Informasi sekolah dan konfigurasi sistem</p>
            </div>
            @if(auth()->user()?->isAdmin())
            <div class="flex items-center gap-2">
                {{-- Reset --}}
                <form action="{{ route('attendance.settings.reset') }}" method="POST"
                      onsubmit="return confirm('Reset semua pengaturan ke default? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 border-2 border-red-300 dark:border-red-700 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                        <i class="fas fa-redo mr-2"></i>
                        Reset ke Default
                    </button>
                </form>
            </div>
            @endif
        </div>


        {{-- Settings Form --}}
        <form action="{{ route('attendance.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @if(auth()->user()?->isAdmin())
            {{-- ═══════════════════════════════════════════════════════ --}}
            {{-- SECTION: PENGATURAN GLOBAL --}}
            {{-- ═══════════════════════════════════════════════════════ --}}
            <div class="flex items-center gap-4 mb-2">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-full">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <span class="text-xs font-semibold text-blue-700 dark:text-blue-300 uppercase tracking-wider">🌐 Pengaturan Global</span>
                </div>
                <div class="flex-1 border-t border-blue-200 dark:border-blue-700/50"></div>
                <span class="text-xs text-gray-400 dark:text-gray-500 italic">Berlaku di semua perangkat</span>
            </div>


            {{-- General Settings --}}
            <x-card>
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-school"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Informasi Umum</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Data sekolah, logo, dan identitas sistem</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <x-input
                        type="text"
                        name="settings[school_name]"
                        label="Nama Sekolah"
                        :value="old('settings.school_name', $settings['general']['school_name'] ?? 'SMK Negeri 1')"
                        maxlength="100"
                        helper="Nama ini akan muncul di notifikasi WhatsApp dan Kartu Pelajar"
                        required
                    />

                    {{-- School Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📍 Alamat Sekolah
                        </label>
                        <input
                            type="text"
                            name="school_address"
                            value="{{ old('school_address', $settings['general']['school_address'] ?? '') }}"
                            maxlength="200"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Jl. Raya Blora No. 1, Blora, Jawa Tengah"
                        >
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alamat ini akan muncul di footer Kartu Pelajar</p>
                    </div>

                    {{-- Logo Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🏫 Logo Sekolah
                        </label>
                        <div class="flex items-start gap-6">
                            {{-- Current Logo Preview --}}
                            <div class="flex-shrink-0">
                                @php $currentLogo = $settings['general']['school_logo'] ?? null; @endphp
                                <div class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center overflow-hidden bg-gray-50 dark:bg-gray-800">
                                    @if($currentLogo && Storage::disk('public')->exists($currentLogo))
                                        <img src="{{ Storage::disk('public')->url($currentLogo) }}" 
                                             alt="Logo Sekolah" 
                                             class="w-full h-full object-contain p-1">
                                    @else
                                        <div class="text-center">
                                            <i class="fas fa-image text-2xl text-gray-400"></i>
                                            <p class="text-xs text-gray-400 mt-1">Belum ada</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Upload Input --}}
                            <div class="flex-1">
                                <input
                                    type="file"
                                    name="school_logo"
                                    accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                    class="w-full text-sm text-gray-500 dark:text-gray-400
                                        file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                        file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700
                                        dark:file:bg-primary-900/30 dark:file:text-primary-400
                                        hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50
                                        file:cursor-pointer file:transition-all"
                                >
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                    Format: PNG, JPG, SVG, WebP. Maks: 2MB. Rekomendasi: latar transparan.
                                </p>
                                @if($currentLogo)
                                    <p class="mt-1 text-xs text-green-600 dark:text-green-400">
                                        <i class="fas fa-check-circle mr-1"></i>Logo sudah diupload
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Announcement Field --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📢 Pengumuman
                        </label>
                        <textarea
                            name="settings[announcement]"
                            rows="3"
                            maxlength="255"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="Siswa harap scan QR Code saat masuk gerbang sekolah"
                        >{{ old('settings.announcement', $settings['general']['announcement'] ?? 'Siswa harap scan QR Code saat masuk gerbang sekolah. Jangan lupa bawa kartu siswa!') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pengumuman ini akan ditampilkan di landing page scanner</p>
                    </div>

                    {{-- Kartu Pelajar Quick Link --}}
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500 rounded">
                        <h4 class="font-semibold text-purple-900 dark:text-purple-300 mb-2">🎴 Cetak Kartu Pelajar</h4>
                        <p class="text-sm text-purple-800 dark:text-purple-200 mb-3">
                            Logo dan nama sekolah di atas akan digunakan untuk kartu pelajar siswa.
                        </p>
                        <a href="{{ route('attendance.students.card') }}" 
                           class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-purple-600 hover:bg-purple-700 text-white transition-all">
                            <i class="fas fa-id-card mr-2"></i> Cetak Kartu Pelajar
                        </a>
                    </div>
                </div>
            </x-card>

            {{-- [Kamera dipindah ke /attendance/kamera] --}}




            @endif {{-- end admin --}}

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-3">
                <a
                    href="{{ route('attendance.dashboard') }}"
                    class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700"
                >
                    Batal
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 shadow-md hover:shadow-lg"
                >
                    <i class="fas fa-save mr-2"></i>
                    Simpan Pengaturan
                </button>
            </div>
        </form>

        {{-- Info Box --}}
        <x-card>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-info-circle"></i>
                    </div>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">ℹ️ Informasi Penting</h3>
                    <ul class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span><strong>Pengaturan waktu</strong> akan langsung berlaku untuk absensi hari berikutnya</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span><strong>Notifikasi WhatsApp</strong> memerlukan WhatsApp Gateway yang berjalan di server</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span><strong>Foto dalam notifikasi</strong> akan menambah ukuran pesan dan waktu pengiriman</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                            <span><strong>Reset ke default</strong> akan mengembalikan semua pengaturan seperti instalasi awal</span>
                        </li>
                    </ul>
                </div>
            </div>
        </x-card>

        @if(auth()->user()?->isAdmin())
        {{-- 📸 Manajemen Foto Absensi --}}
        <x-card class="mt-4">
            <div class="flex items-center mb-5">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center text-white text-2xl mr-4">
                    <i class="fas fa-images"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Foto Absensi</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Download arsip dan cleanup foto lama</p>
                </div>
            </div>

            {{-- Stats --}}
            <div id="photoStatsBox" class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 text-center">
                    <div id="photoStatFiles" class="text-2xl font-black text-gray-900 dark:text-white">—</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total Foto</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 text-center">
                    <div id="photoStatMB" class="text-2xl font-black text-orange-500">—</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Ukuran Disk</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 text-center">
                    <div id="photoStatOldest" class="text-sm font-bold text-gray-700 dark:text-gray-300 mt-1">—</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Foto Terlama</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-3 text-center">
                    <div id="photoStatNewest" class="text-sm font-bold text-gray-700 dark:text-gray-300 mt-1">—</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Foto Terbaru</div>
                </div>
            </div>

            {{-- Info auto-cleanup --}}
            <div class="flex items-center gap-2 mb-5 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm text-blue-700 dark:text-blue-300">
                <i class="fas fa-clock-rotate-left"></i>
                <span>Auto cleanup aktif: foto lebih tua dari <strong>30 hari</strong> dihapus otomatis setiap hari Minggu jam 01:00</span>
            </div>

            {{-- Action buttons --}}
            <div class="flex flex-wrap gap-3">
                {{-- Download --}}
                <a href="{{ route('attendance.settings.photos.download') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl font-semibold text-sm transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-download"></i> Download Semua Foto (ZIP)
                </a>

                {{-- Manual Cleanup --}}
                <button onclick="document.getElementById('cleanupModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl font-semibold text-sm transition-all shadow-md hover:shadow-lg">
                    <i class="fas fa-trash-alt"></i> Cleanup Manual
                </button>

                {{-- Refresh stats --}}
                <button onclick="loadPhotoStats()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold text-sm transition-all">
                    <i class="fas fa-refresh" id="photoRefreshIcon"></i> Refresh
                </button>
            </div>
        </x-card>

        {{-- Cleanup Confirmation Modal --}}
        <div id="cleanupModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6">
                <div class="text-center mb-5">
                    <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-trash-alt text-2xl text-red-500"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white">Cleanup Foto Manual</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Foto yang dihapus tidak bisa dikembalikan</p>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Hapus foto lebih tua dari:
                    </label>
                    <div class="flex gap-2">
                        <select id="cleanupDays" class="flex-1 px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-red-500">
                            <option value="7">7 hari (1 minggu)</option>
                            <option value="14">14 hari (2 minggu)</option>
                            <option value="30" selected>30 hari (1 bulan)</option>
                            <option value="60">60 hari (2 bulan)</option>
                            <option value="90">90 hari (3 bulan)</option>
                        </select>
                        <span class="flex items-center text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">yang lalu</span>
                    </div>
                </div>

                <div id="cleanupResult" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>

                <div class="flex gap-3">
                    <button onclick="document.getElementById('cleanupModal').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold text-sm hover:bg-gray-200 transition-all">
                        Batal
                    </button>
                    <button id="cleanupConfirmBtn" onclick="runCleanup()"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-semibold text-sm hover:from-red-600 hover:to-red-700 transition-all">
                        <i class="fas fa-trash-alt mr-1"></i> Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
        // Load photo stats on page load
        document.addEventListener('DOMContentLoaded', loadPhotoStats);

        function loadPhotoStats() {
            const icon = document.getElementById('photoRefreshIcon');
            icon.classList.add('fa-spin');
            fetch('{{ route("attendance.settings.photos.stats") }}')
                .then(r => r.json())
                .then(d => {
                    document.getElementById('photoStatFiles').textContent = d.total_files.toLocaleString('id-ID');
                    document.getElementById('photoStatMB').textContent    = d.total_mb + ' MB';
                    document.getElementById('photoStatOldest').textContent = d.oldest_date || '—';
                    document.getElementById('photoStatNewest').textContent = d.newest_date || '—';
                })
                .catch(() => {})
                .finally(() => icon.classList.remove('fa-spin'));
        }

        function runCleanup() {
            const days = document.getElementById('cleanupDays').value;
            const btn  = document.getElementById('cleanupConfirmBtn');
            const res  = document.getElementById('cleanupResult');

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...';
            res.classList.add('hidden');

            fetch('{{ route("attendance.settings.photos.cleanup") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ days: parseInt(days) }),
            })
            .then(r => r.json())
            .then(d => {
                res.classList.remove('hidden');
                if (d.success) {
                    res.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300';
                    res.innerHTML = '<i class="fas fa-check-circle mr-1"></i>' + d.message;
                    loadPhotoStats(); // refresh stats
                } else {
                    res.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300';
                    res.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i>' + d.message;
                }
            })
            .catch(() => {
                res.classList.remove('hidden');
                res.className = 'mb-4 p-3 rounded-lg text-sm font-medium bg-red-100 text-red-700';
                res.textContent = 'Terjadi kesalahan. Coba lagi.';
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-trash-alt mr-1"></i> Ya, Hapus';
            });
        }
        </script>
        @endpush

    </div>

    <div class="max-w-5xl mt-6">
        <x-card>
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center text-white text-2xl mr-4">
                    <i class="fas fa-database"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Backup & Restore Database</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Download backup atau pulihkan data dari file SQL</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- BACKUP --}}
                <div class="bg-teal-50 dark:bg-teal-900/10 border border-teal-200 dark:border-teal-800 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 bg-teal-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-download text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">Download Backup</h4>
                            <p class="text-xs text-gray-500">Export semua data ke file .sql</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-4">
                        Backup mencakup seluruh tabel: siswa, absensi, settings, log, dan semua data sistem.
                        Simpan file di tempat aman.
                    </p>
                    <a href="{{ route('attendance.settings.backup') }}"
                       class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow hover:shadow-lg">
                        <i class="fas fa-database mr-2"></i>
                        Download Backup (.sql)
                    </a>
                </div>

                {{-- RESTORE --}}
                <div class="bg-orange-50 dark:bg-orange-900/10 border border-orange-200 dark:border-orange-800 rounded-xl p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 bg-orange-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-upload text-sm"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white text-sm">Restore dari Backup</h4>
                            <p class="text-xs text-gray-500">Pulihkan data dari file .sql</p>
                        </div>
                    </div>

                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 mb-4">
                        <p class="text-xs text-red-700 dark:text-red-400 font-medium">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <strong>PERINGATAN:</strong> Restore akan menimpa data yang ada saat ini!
                            Pastikan sudah download backup terbaru sebelum restore.
                        </p>
                    </div>

                    @error('sql_file')
                        <div class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs rounded-lg px-3 py-2 mb-3">
                            <i class="fas fa-times-circle mr-1"></i> {{ $message }}
                        </div>
                    @enderror

                    <form action="{{ route('attendance.settings.restore') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          id="restoreForm"
                          onsubmit="return confirmRestore()">
                        @csrf
                        <div class="flex flex-col gap-3">
                            <label class="block">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Pilih file backup (.sql)</span>
                                <input type="file"
                                       name="sql_file"
                                       id="sqlFileInput"
                                       accept=".sql,.txt"
                                       required
                                       class="mt-1 block w-full text-xs text-gray-600 dark:text-gray-400
                                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                              file:text-xs file:font-medium file:bg-orange-100 file:text-orange-700
                                              hover:file:bg-orange-200 dark:file:bg-orange-900/30 dark:file:text-orange-400
                                              cursor-pointer">
                            </label>
                            <div id="fileInfo" class="text-xs text-gray-500 hidden">
                                <i class="fas fa-file-code mr-1"></i>
                                <span id="fileName"></span>
                                <span id="fileSize" class="ml-2 text-gray-400"></span>
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow hover:shadow-lg">
                                <i class="fas fa-upload mr-2"></i>
                                Restore Database
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info keterangan --}}
            <div class="mt-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg px-4 py-3">
                <p class="text-xs text-gray-600 dark:text-gray-400">
                    <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                    <strong>Tips:</strong> Lakukan backup rutin setiap minggu. Setelah restore berhasil, <strong>refresh halaman</strong> untuk memuat ulang semua data.
                </p>
            </div>
        </x-card>
        @endif {{-- end admin-only foto & backup --}}
    </div>



</x-app-layout>
