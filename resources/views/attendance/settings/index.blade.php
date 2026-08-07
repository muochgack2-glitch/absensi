@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="title">Settings</x-slot>
    <x-slot name="pageTitle">Pengaturan Sistem</x-slot>

    <div class="max-w-5xl space-y-6">
        {{-- Page Header --}}
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">⚙️ Pengaturan Sistem</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Konfigurasi waktu absensi dan notifikasi</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- Backup DB --}}
                <a href="{{ route('attendance.settings.backup') }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 border-2 border-teal-300 dark:border-teal-700 text-teal-700 dark:text-teal-400 hover:bg-teal-50 dark:hover:bg-teal-900/20"
                   title="Download backup database SQL">
                    <i class="fas fa-database mr-2"></i>
                    Backup DB
                </a>

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
        </div>


        {{-- Settings Form --}}
        <form action="{{ route('attendance.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Schedule Settings --}}
            <x-card>
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Pengaturan Waktu</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Konfigurasi jam masuk, pulang, dan toleransi</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Check In Time --}}
                        <x-input
                            type="time"
                            name="settings[check_in_time]"
                            label="Jam Masuk"
                            :value="old('settings.check_in_time', $settings['schedule']['check_in_time'] ?? '07:00')"
                            helper="Jam mulai absensi masuk"
                            required
                        />

                        {{-- Check Out Time --}}
                        <x-input
                            type="time"
                            name="settings[check_out_time]"
                            label="Jam Pulang"
                            :value="old('settings.check_out_time', $settings['schedule']['check_out_time'] ?? '15:00')"
                            helper="Jam mulai absensi pulang"
                            required
                        />

                        {{-- Tolerance Minutes --}}
                        <x-input
                            type="number"
                            name="settings[tolerance_minutes]"
                            label="Toleransi Keterlambatan (menit)"
                            :value="old('settings.tolerance_minutes', $settings['schedule']['tolerance_minutes'] ?? '15')"
                            min="0"
                            max="60"
                            helper="Siswa dianggap terlambat jika melewati toleransi ini"
                            required
                        />

                        {{-- Cutoff Time --}}
                        <x-input
                            type="time"
                            name="settings[cutoff_time]"
                            label="Batas Waktu Alpha"
                            :value="old('settings.cutoff_time', $settings['schedule']['cutoff_time'] ?? '09:00')"
                            helper="Siswa otomatis alpha jika belum absen sampai jam ini"
                            required
                        />
                    </div>

                    {{-- Example Timeline --}}
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-3">💡 Contoh Timeline:</h4>
                        <div class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                            <p>• <strong>07:00 - 07:15:</strong> Siswa dianggap <span class="text-green-600 dark:text-green-400 font-semibold">✅ Hadir</span></p>
                            <p>• <strong>07:16 - 09:00:</strong> Siswa dianggap <span class="text-yellow-600 dark:text-yellow-400 font-semibold">⏰ Terlambat</span></p>
                            <p>• <strong>Setelah 09:00:</strong> Siswa otomatis <span class="text-red-600 dark:text-red-400 font-semibold">❌ Alpha</span></p>
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Notification Settings --}}
            <x-card>
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Pengaturan Notifikasi WhatsApp</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Konfigurasi notifikasi otomatis ke orang tua</p>
                    </div>
                </div>

                <div class="space-y-6">
                    {{-- Enable Parent Notification --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">
                                Kirim Notifikasi ke Orang Tua
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aktifkan notifikasi WhatsApp otomatis saat siswa absen</p>
                        </div>
                        <div>
                            <input type="hidden" name="settings[enable_parent_notification]" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="settings[enable_parent_notification]" 
                                       value="1"
                                       {{ old('settings.enable_parent_notification', $settings['notification']['enable_parent_notification'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Include Photo --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">
                                Sertakan Foto dalam Notifikasi
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kirim foto absensi bersama dengan pesan WhatsApp</p>
                        </div>
                        <div>
                            <input type="hidden" name="settings[include_photo_in_notification]" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="settings[include_photo_in_notification]" 
                                       value="1"
                                       {{ old('settings.include_photo_in_notification', $settings['notification']['include_photo_in_notification'] ?? '0') == '1' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Test Notification --}}
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded">
                        <h4 class="font-semibold text-yellow-900 dark:text-yellow-300 mb-3">🧪 Test Notifikasi</h4>
                        <div class="flex gap-3">
                            <input type="text" 
                                   id="test_phone"
                                   placeholder="628123456789"
                                   pattern="^628[0-9]{9,12}$"
                                   class="flex-1 px-4 py-2 border border-yellow-300 dark:border-yellow-700 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            <button type="button" 
                                    onclick="testNotification()"
                                    class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-all duration-200 font-medium">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Kirim Test
                            </button>
                        </div>
                        <p class="text-xs text-yellow-800 dark:text-yellow-200 mt-2">
                            Pastikan WhatsApp Gateway sudah berjalan di http://localhost:3002
                        </p>
                    </div>
                </div>
            </x-card>

            {{-- Auto Absent Notification Settings --}}
            <x-card>
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Notifikasi Ketidakhadiran Otomatis</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kirim WA ke orang tua secara otomatis saat siswa alpha</p>
                    </div>
                </div>

                <div class="space-y-5">
                    {{-- Toggle aktif --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">
                                Aktifkan Notifikasi Alpha Otomatis
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sistem otomatis kirim WA ke orang tua saat siswa tidak hadir</p>
                        </div>
                        <div>
                            <input type="hidden" name="settings[auto_absent_notify]" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="settings[auto_absent_notify]"
                                       value="1"
                                       id="autoAbsentNotify"
                                       {{ old('settings.auto_absent_notify', $settings['notification']['auto_absent_notify'] ?? '0') == '1' ? 'checked' : '' }}
                                       onchange="toggleAbsentNotifyFields()"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Toggle: Notifikasi Terlambat --}}
                    <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg border border-yellow-200 dark:border-yellow-800">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">
                                ⚡ Aktifkan Notifikasi Terlambat (Real-time)
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                WA langsung terkirim ke ortu saat siswa scan QR dan statusnya <strong>Terlambat</strong>
                            </p>
                        </div>
                        <div>
                            <input type="hidden" name="settings[late_notify_enabled]" value="false">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="settings[late_notify_enabled]"
                                       value="true"
                                       id="lateNotifyEnabled"
                                       {{ old('settings.late_notify_enabled', $settings['notification']['late_notify_enabled'] ?? 'false') === 'true' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 dark:peer-focus:ring-yellow-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Sub-settings (tampil jika toggle aktif) --}}
                    <div id="absentNotifyFields" class="space-y-4 {{ old('settings.auto_absent_notify', $settings['notification']['auto_absent_notify'] ?? '0') == '1' ? '' : 'opacity-40 pointer-events-none' }}">

                        {{-- Jam pengiriman --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input
                                type="time"
                                name="settings[absent_notify_time]"
                                label="Jam Pengiriman Notifikasi Alpha"
                                :value="old('settings.absent_notify_time', $settings['notification']['absent_notify_time'] ?? '09:00')"
                                helper="WA dikirim ke orang tua pada jam ini setiap hari aktif"
                            />
                        </div>

                        {{-- Hari aktif --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📅 Hari Aktif Pengiriman</label>
                            @php
                                $activeDays = old('settings.absent_notify_days',
                                    $settings['notification']['absent_notify_days'] ?? '1,2,3,4,5'
                                );
                                $activeDaysArr = explode(',', $activeDays);
                                $dayNames = ['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'];
                            @endphp
                            <input type="hidden" name="settings[absent_notify_days]" value="">
                            <div class="flex flex-wrap gap-2">
                                @foreach($dayNames as $num => $name)
                                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition-all
                                        {{ in_array($num, $activeDaysArr)
                                            ? 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300'
                                            : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}"
                                        id="dayLabel{{ $num }}"
                                    >
                                        <input
                                            type="checkbox"
                                            name="absent_days[]"
                                            value="{{ $num }}"
                                            {{ in_array($num, $activeDaysArr) ? 'checked' : '' }}
                                            onchange="updateDayStyle(this, '{{ $num }}')"
                                            class="accent-red-500"
                                        >
                                        {{ $name }}
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Pilih hari di mana notifikasi alpha akan dikirim</p>
                        </div>

                        {{-- Setup Cron Panel --}}
                        <div class="mt-2 rounded-xl border border-orange-200 dark:border-orange-800 overflow-hidden">
                            {{-- Header --}}
                            <div class="flex items-center gap-3 px-4 py-3 bg-orange-50 dark:bg-orange-900/30 border-b border-orange-200 dark:border-orange-800">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm" style="background:#ea580c;">
                                    <i class="fas fa-terminal"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-orange-900 dark:text-orange-200">⚙️ Setup Cron Job di Server (Wajib 1x)</div>
                                    <div class="text-xs text-orange-700 dark:text-orange-400">Tanpa ini, notifikasi otomatis tidak akan berjalan</div>
                                </div>
                            </div>

                            {{-- Steps --}}
                            <div class="p-4 space-y-3 bg-white dark:bg-gray-800">
                                {{-- Step 1 --}}
                                <div class="flex gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center">1</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">SSH ke server lalu buka crontab:</p>
                                        <div class="flex items-center gap-2 mt-1.5 bg-gray-900 rounded-lg px-3 py-2">
                                            <code class="flex-1 text-xs text-green-400 font-mono select-all" id="cronStep1">crontab -e</code>
                                            <button type="button" onclick="copyCmd('cronStep1', this)" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors" title="Copy">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Step 2 --}}
                                <div class="flex gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center">2</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Tambahkan baris ini di bagian paling bawah:</p>
                                        <div class="flex items-center gap-2 mt-1.5 bg-gray-900 rounded-lg px-3 py-2">
                                            <code class="flex-1 text-xs text-green-400 font-mono select-all break-all" id="cronStep2">* * * * * cd /www/wwwroot/absensi &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code>
                                            <button type="button" onclick="copyCmd('cronStep2', this)" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors" title="Copy">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>Sesuaikan <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">/www/wwwroot/absensi</code> dengan path project di server Anda.
                                        </p>
                                    </div>
                                </div>

                                {{-- Step 3 --}}
                                <div class="flex gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center">3</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Simpan &amp; verifikasi cron aktif:</p>
                                        <div class="flex items-center gap-2 mt-1.5 bg-gray-900 rounded-lg px-3 py-2">
                                            <code class="flex-1 text-xs text-green-400 font-mono select-all" id="cronStep3">crontab -l</code>
                                            <button type="button" onclick="copyCmd('cronStep3', this)" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors" title="Copy">
                                                <i class="fas fa-copy text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                {{-- Note --}}
                                <div class="flex items-start gap-2 pt-1 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700">
                                    <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                    <span>Setelah cron aktif, sistem akan otomatis kirim WA pada jam &amp; hari yang dipilih di atas — tanpa perlu tindakan manual lagi.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>

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
    </div>

    {{-- ====================== BACKUP & RESTORE CARD ====================== --}}
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
    </div>

    @push('scripts')
    <script>
        // ===== Test Notification =====
        function testNotification() {
            const phone = document.getElementById('test_phone').value;
            if (!phone || !/^628[0-9]{9,12}$/.test(phone)) {
                alert('Masukkan nomor WhatsApp yang valid (format: 628XXXXXXXXX)');
                return;
            }
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("attendance.settings.test-notification") }}';
            const csrf = document.createElement('input');
            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = '{{ csrf_token() }}';
            const phoneInput = document.createElement('input');
            phoneInput.type = 'hidden'; phoneInput.name = 'phone'; phoneInput.value = phone;
            form.appendChild(csrf); form.appendChild(phoneInput);
            document.body.appendChild(form); form.submit();
        }

        // ===== Toggle auto absent notify fields =====
        function toggleAbsentNotifyFields() {
            const checkbox = document.getElementById('autoAbsentNotify');
            const fields   = document.getElementById('absentNotifyFields');
            if (checkbox.checked) {
                fields.classList.remove('opacity-40', 'pointer-events-none');
            } else {
                fields.classList.add('opacity-40', 'pointer-events-none');
            }
        }

        // ===== Update hari aktif style =====
        function updateDayStyle(checkbox, num) {
            const label = document.getElementById('dayLabel' + num);
            if (checkbox.checked) {
                label.className = label.className
                    .replace('border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400',
                             'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300');
            } else {
                label.className = label.className
                    .replace('border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300',
                             'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400');
            }
        }

        // Sebelum form submit: kumpulkan hari aktif ke settings[absent_notify_days]
        document.querySelector('form[action="{{ route("attendance.settings.update") }}"]')
            ?.addEventListener('submit', function() {
                const checked = [...document.querySelectorAll('input[name="absent_days[]"]:checked')]
                    .map(el => el.value);
                document.querySelector('input[name="settings[absent_notify_days]"]').value = checked.join(',');
            });

        // ===== Copy command ke clipboard =====
        function copyCmd(elementId, btn) {
            const text = document.getElementById(elementId).innerText;
            // Decode HTML entities sebelum copy
            const decoded = text
                .replace(/&amp;/g, '&')
                .replace(/&gt;/g, '>')
                .replace(/&lt;/g, '<');
            navigator.clipboard.writeText(decoded).then(() => {
                const icon = btn.querySelector('i');
                icon.className = 'fas fa-check text-xs text-green-400';
                setTimeout(() => { icon.className = 'fas fa-copy text-xs'; }, 2000);
            });
        }

        // ===== Restore: konfirmasi & preview file =====
        document.getElementById('sqlFileInput')?.addEventListener('change', function () {
            const file = this.files[0];
            const info = document.getElementById('fileInfo');
            if (file) {
                document.getElementById('fileName').textContent = file.name;
                const sizeMb = (file.size / 1024 / 1024).toFixed(2);
                document.getElementById('fileSize').textContent = `(${sizeMb} MB)`;
                info.classList.remove('hidden');
            } else {
                info.classList.add('hidden');
            }
        });

        function confirmRestore() {
            const file = document.getElementById('sqlFileInput').files[0];
            if (!file) {
                alert('Pilih file SQL backup terlebih dahulu.');
                return false;
            }
            return confirm(
                '⚠️ PERINGATAN!\n\n' +
                'Restore akan MENIMPA semua data yang ada saat ini!\n' +
                'File: ' + file.name + '\n\n' +
                'Sudah download backup terbaru?\n\n' +
                'Klik OK untuk melanjutkan restore.'
            );
        }
    </script>
    @endpush
</x-app-layout>
