@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="title">Settings</x-slot>
    <x-slot name="pageTitle">Pengaturan Sistem</x-slot>

    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">⚙️ Pengaturan Sistem</h1>
                @if(auth()->user()?->isAdmin())
                <p class="text-gray-600 dark:text-gray-400 mt-1">Konfigurasi waktu absensi dan notifikasi</p>
                @else
                <p class="text-gray-600 dark:text-gray-400 mt-1">Pengaturan kamera scanner</p>
                @endif
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

            {{-- [Setting Waktu dipindah ke /attendance/setting-waktu] --}}


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
                                       {{ old('settings.include_photo_in_notification', $settings['notification']['include_photo_in_notification'] ?? '1') == '1' ? 'checked' : '' }}
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
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="settings[auto_absent_notify]"
                                       value="1"
                                       id="autoAbsentNotify"
                                       @if(old('settings.auto_absent_notify', $settings['notification']['auto_absent_notify'] ?? $settings['general']['auto_absent_notify'] ?? '0') == '1') checked @endif
                                       onchange="toggleAbsentNotifyFields()"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Toggle: Notifikasi Terlambat --}}
                    <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-700">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">
                                ⚡ Aktifkan Notifikasi Terlambat (Real-time)
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                WA langsung terkirim ke ortu saat siswa scan QR dan statusnya <strong>Terlambat</strong>
                            </p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="settings[late_notify_enabled]"
                                       value="true"
                                       id="lateNotifyEnabled"
                                       @if(old('settings.late_notify_enabled', $settings['notification']['late_notify_enabled'] ?? $settings['general']['late_notify_enabled'] ?? 'false') === 'true') checked @endif
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

            {{-- Ringkasan Kehadiran ke Wali Kelas --}}
            <x-card>
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">📊 Ringkasan Kehadiran ke Wali Kelas</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kirim ringkasan harian (hadir/izin/alfa) ke wali kelas masing-masing kelas via WhatsApp</p>
                    </div>
                </div>

                <div class="space-y-5">
                    {{-- Toggle aktif --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">
                                Aktifkan Ringkasan Otomatis ke Wali Kelas
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sistem akan kirim WA ringkasan kehadiran ke setiap wali kelas secara otomatis</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="settings[summary_wali_kelas_enabled]"
                                       value="1"
                                       id="summaryWaliKelasEnabled"
                                       @if(old('settings.summary_wali_kelas_enabled', $settings['notification']['summary_wali_kelas_enabled'] ?? '0') == '1') checked @endif
                                       onchange="toggleSummaryFields()"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Sub-settings --}}
                    <div id="summaryWaliKelasFields" class="space-y-4 {{ old('settings.summary_wali_kelas_enabled', $settings['notification']['summary_wali_kelas_enabled'] ?? '0') == '1' ? '' : 'opacity-40 pointer-events-none' }}">

                        {{-- Jam pengiriman --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input
                                type="time"
                                name="settings[summary_send_time]"
                                label="🌅 Jam Ringkasan Masuk"
                                :value="old('settings.summary_send_time', $settings['notification']['summary_send_time'] ?? '09:00')"
                                helper="Ringkasan kehadiran masuk dikirim pada jam ini"
                            />
                            <x-input
                                type="time"
                                name="settings[summary_pulang_send_time]"
                                label="🌆 Jam Ringkasan Pulang"
                                :value="old('settings.summary_pulang_send_time', $settings['notification']['summary_pulang_send_time'] ?? '15:00')"
                                helper="Ringkasan kehadiran pulang dikirim pada jam ini"
                            />
                        </div>

                        {{-- Hari aktif --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📅 Hari Pengiriman Ringkasan</label>
                            @php
                                $summaryDays = old('settings.summary_send_days',
                                    $settings['notification']['summary_send_days'] ?? '1,2,3,4,5'
                                );
                                $summaryDaysArr = explode(',', $summaryDays);
                                $dayNames = ['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'];
                            @endphp
                            <input type="hidden" name="settings[summary_send_days]" value="" id="summaryDaysHidden">
                            <div class="flex flex-wrap gap-2">
                                @foreach($dayNames as $num => $name)
                                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition-all
                                        {{ in_array($num, $summaryDaysArr)
                                            ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300'
                                            : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}"
                                        id="summaryDayLabel{{ $num }}"
                                    >
                                        <input
                                            type="checkbox"
                                            name="summary_days[]"
                                            value="{{ $num }}"
                                            {{ in_array($num, $summaryDaysArr) ? 'checked' : '' }}
                                            onchange="updateSummaryDayStyle(this, '{{ $num }}')"
                                            class="accent-green-500"
                                        >
                                        {{ $name }}
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Pilih hari pengiriman ringkasan ke wali kelas</p>
                        </div>

                        {{-- Info persyaratan --}}
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded">
                            <h4 class="font-semibold text-green-900 dark:text-green-300 mb-2">📋 Persyaratan:</h4>
                            <ul class="text-xs text-green-800 dark:text-green-400 space-y-1 list-disc list-inside">
                                <li>Setiap kelas harus memiliki wali kelas yang terdaftar di sistem</li>
                                <li>Nomor HP wali kelas harus diisi di profil pengguna</li>
                                <li>WhatsApp Gateway harus aktif saat jam pengiriman</li>
                                <li>Cron job Laravel harus terpasang di server</li>
                            </ul>
                        </div>

                        {{-- Tombol kirim manual --}}
                        <div class="flex items-center gap-3">
                            <button type="button"
                                    onclick="sendSummaryNow()"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Kirim Ringkasan Sekarang
                            </button>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kirim manual ke semua wali kelas hari ini</p>
                        </div>
                        <div id="summaryResult" class="hidden"></div>
                    </div>
                </div>
            </x-card>

            {{-- Ringkasan Waka Kesiswaan --}}
            <x-card>
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-user-tie text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan ke Waka Kesiswaan</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Laporan kehadiran seluruh sekolah + detail alpha & belum pulang</p>
                    </div>
                </div>
                <div class="space-y-6">
                    {{-- Toggle aktif --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Aktifkan Ringkasan Otomatis ke Waka Kesiswaan</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Waka menerima laporan harian (masuk & pulang) secara otomatis</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="settings[waka_summary_enabled]"
                                       value="1"
                                       id="wakaSummaryEnabled"
                                       @if(old('settings.waka_summary_enabled', $settings['notification']['waka_summary_enabled'] ?? '0') == '1') checked @endif
                                       onchange="toggleWakaFields()"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Sub-settings --}}
                    <div id="wakaFields" class="space-y-4 {{ old('settings.waka_summary_enabled', $settings['notification']['waka_summary_enabled'] ?? '0') == '1' ? '' : 'opacity-40 pointer-events-none' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input
                                type="time"
                                name="settings[waka_summary_masuk_time]"
                                label="🌅 Jam Kirim Masuk"
                                :value="old('settings.waka_summary_masuk_time', $settings['notification']['waka_summary_masuk_time'] ?? '08:00')"
                                helper="Laporan kehadiran masuk dikirim pada jam ini"
                            />
                            <x-input
                                type="time"
                                name="settings[waka_summary_pulang_time]"
                                label="🌆 Jam Kirim Pulang"
                                :value="old('settings.waka_summary_pulang_time', $settings['notification']['waka_summary_pulang_time'] ?? '15:00')"
                                helper="Laporan kepulangan dikirim pada jam ini"
                            />
                        </div>

                        {{-- Hari aktif --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📅 Hari Pengiriman</label>
                            @php
                                $wakaDays    = old('settings.waka_summary_send_days', $settings['notification']['waka_summary_send_days'] ?? '1,2,3,4,5');
                                $wakaDaysArr = explode(',', $wakaDays);
                            @endphp
                            <input type="hidden" name="settings[waka_summary_send_days]" value="" id="wakaDaysHidden">
                            <div class="flex flex-wrap gap-2">
                                @foreach(['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'] as $num => $name)
                                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition-all
                                        {{ in_array($num, $wakaDaysArr) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}"
                                        id="wakaDayLabel{{ $num }}">
                                        <input type="checkbox" name="waka_days[]" value="{{ $num }}"
                                               {{ in_array($num, $wakaDaysArr) ? 'checked' : '' }}
                                               onchange="updateWakaDayStyle(this, '{{ $num }}')"
                                               class="accent-blue-500">
                                        {{ $name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded">
                            <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">📋 Persyaratan:</h4>
                            <ul class="text-xs text-blue-800 dark:text-blue-400 space-y-1 list-disc list-inside">
                                <li>User dengan role <strong>waka_kesiswaan</strong> harus terdaftar</li>
                                <li>Nomor HP waka harus diisi di profil pengguna</li>
                                <li>WhatsApp Gateway harus aktif</li>
                            </ul>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button" onclick="sendWakaNow('masuk')"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Masuk Sekarang
                            </button>
                            <button type="button" onclick="sendWakaNow('pulang')"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Pulang Sekarang
                            </button>
                        </div>
                        <div id="wakaResult" class="hidden"></div>
                    </div>
                </div>
            </x-card>

            {{-- Ringkasan Kepala Sekolah --}}
            <x-card>
                <div class="flex items-center mb-6">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-crown text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ringkasan ke Kepala Sekolah</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Laporan executive harian: total + detail alpha & belum pulang per kelas</p>
                    </div>
                </div>
                <div class="space-y-6">
                    {{-- Toggle aktif --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Aktifkan Ringkasan Otomatis ke Kepala Sekolah</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kepala sekolah menerima laporan harian (masuk & pulang) secara otomatis</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="settings[kepsek_summary_enabled]"
                                       value="1"
                                       id="kepsekSummaryEnabled"
                                       @if(old('settings.kepsek_summary_enabled', $settings['notification']['kepsek_summary_enabled'] ?? '0') == '1') checked @endif
                                       onchange="toggleKepsekFields()"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Sub-settings --}}
                    <div id="kepsekFields" class="space-y-4 {{ old('settings.kepsek_summary_enabled', $settings['notification']['kepsek_summary_enabled'] ?? '0') == '1' ? '' : 'opacity-40 pointer-events-none' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input
                                type="time"
                                name="settings[kepsek_summary_time]"
                                label="🌅 Jam Kirim Masuk"
                                :value="old('settings.kepsek_summary_time', $settings['notification']['kepsek_summary_time'] ?? '08:30')"
                                helper="Laporan kehadiran masuk dikirim pada jam ini"
                            />
                            <x-input
                                type="time"
                                name="settings[kepsek_summary_pulang_time]"
                                label="🌆 Jam Kirim Pulang"
                                :value="old('settings.kepsek_summary_pulang_time', $settings['notification']['kepsek_summary_pulang_time'] ?? '15:30')"
                                helper="Laporan kepulangan dikirim pada jam ini"
                            />
                        </div>

                        {{-- Hari aktif --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📅 Hari Pengiriman</label>
                            @php
                                $kepsekDays    = old('settings.kepsek_summary_send_days', $settings['notification']['kepsek_summary_send_days'] ?? '1,2,3,4,5');
                                $kepsekDaysArr = explode(',', $kepsekDays);
                            @endphp
                            <input type="hidden" name="settings[kepsek_summary_send_days]" value="" id="kepsekDaysHidden">
                            <div class="flex flex-wrap gap-2">
                                @foreach(['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'] as $num => $name)
                                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition-all
                                        {{ in_array($num, $kepsekDaysArr) ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}"
                                        id="kepsekDayLabel{{ $num }}">
                                        <input type="checkbox" name="kepsek_days[]" value="{{ $num }}"
                                               {{ in_array($num, $kepsekDaysArr) ? 'checked' : '' }}
                                               onchange="updateKepsekDayStyle(this, '{{ $num }}')"
                                               class="accent-purple-500">
                                        {{ $name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="p-4 bg-purple-50 dark:bg-purple-900/20 border-l-4 border-purple-500 rounded">
                            <h4 class="font-semibold text-purple-900 dark:text-purple-300 mb-2">📋 Persyaratan:</h4>
                            <ul class="text-xs text-purple-800 dark:text-purple-400 space-y-1 list-disc list-inside">
                                <li>User dengan role <strong>kepala_sekolah</strong> harus terdaftar</li>
                                <li>Nomor HP kepala sekolah harus diisi di profil pengguna</li>
                                <li>WhatsApp Gateway harus aktif</li>
                            </ul>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button" onclick="sendKepsekNow('masuk')"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Masuk Sekarang
                            </button>
                            <button type="button" onclick="sendKepsekNow('pulang')"
                                    class="inline-flex items-center px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Pulang Sekarang
                            </button>
                        </div>
                        <div id="kepsekResult" class="hidden"></div>
                    </div>
                </div>
            </x-card>


            <x-card>
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">⚠️ Peringatan Keterlambatan</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kirim peringatan khusus untuk siswa yang sering terlambat</p>
                    </div>
                </div>

                <div class="space-y-5">
                    {{-- Toggle Enable Late Warning --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">
                                Aktifkan Peringatan Keterlambatan
                            </label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kirim peringatan otomatis ke orang tua saat siswa sering terlambat</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                       name="settings[late_warning_enabled]"
                                       value="1"
                                       id="lateWarningEnabled"
                                       @if(old('settings.late_warning_enabled', $settings['notification']['late_warning_enabled'] ?? '0') == '1') checked @endif
                                       onchange="toggleLateWarningFields()"
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Sub-settings --}}
                    <div id="lateWarningFields" class="space-y-4 @if(old('settings.late_warning_enabled', $settings['notification']['late_warning_enabled'] ?? '0') != '1') opacity-40 pointer-events-none @endif">
                        
                        {{-- Threshold Settings --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input
                                type="number"
                                name="settings[late_warning_threshold_minutes]"
                                label="⏱️ Batas Keterlambatan (menit)"
                                :value="old('settings.late_warning_threshold_minutes', $settings['notification']['late_warning_threshold_minutes'] ?? '30')"
                                min="1"
                                max="120"
                                helper="Peringatan dikirim jika siswa terlambat minimal X menit"
                            />

                            <x-input
                                type="number"
                                name="settings[late_warning_min_count]"
                                label="🔢 Jumlah Minimal Keterlambatan"
                                :value="old('settings.late_warning_min_count', $settings['notification']['late_warning_min_count'] ?? '3')"
                                min="1"
                                max="20"
                                helper="Peringatan dikirim setelah terlambat X kali dalam sebulan"
                            />
                        </div>

                        {{-- Info Box --}}
                        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded">
                            <h4 class="font-semibold text-amber-900 dark:text-amber-300 mb-3">📊 Cara Kerja Peringatan Keterlambatan:</h4>
                            <div class="space-y-2 text-sm text-amber-800 dark:text-amber-200">
                                <p>• <strong>Real-time:</strong> Peringatan dikirim saat siswa check-in dengan status terlambat</p>
                                <p>• <strong>Kondisi:</strong> Hanya dikirim jika siswa sudah terlambat minimal <strong class="text-amber-900 dark:text-amber-100">{{ old('settings.late_warning_min_count', $settings['notification']['late_warning_min_count'] ?? '3') }}x</strong> dalam bulan ini</p>
                                <p>• <strong>Threshold:</strong> Terlambat minimal <strong class="text-amber-900 dark:text-amber-100">{{ old('settings.late_warning_threshold_minutes', $settings['notification']['late_warning_threshold_minutes'] ?? '30') }} menit</strong> dari jam masuk</p>
                                <p>• <strong>Statistik:</strong> Pesan berisi total keterlambatan, akumulasi menit, dan trend</p>
                            </div>
                        </div>

                        {{-- Preview Message --}}
                        <div class="p-4 bg-white dark:bg-gray-900 border-2 border-amber-200 dark:border-amber-800 rounded-lg">
                            <h4 class="font-semibold text-amber-900 dark:text-amber-300 mb-3 flex items-center gap-2">
                                <i class="fab fa-whatsapp text-green-500"></i>
                                Contoh Pesan Peringatan:
                            </h4>
                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 border border-gray-200 dark:border-gray-700">
                                <div class="font-mono text-xs text-gray-800 dark:text-gray-200 whitespace-pre-line">🏫 <strong>{{ $settings['general']['school_name'] ?? 'SMK Negeri 1' }}</strong>
⚠️ <strong>PERINGATAN KETERLAMBATAN</strong>

Siswa: <strong>Ahmad Rizki</strong>
Kelas: X Busana

📊 <strong>Statistik Bulan Ini:</strong>
• Total Terlambat: <strong>3x</strong>
• Akumulasi Waktu: <strong>95 menit</strong>
• Trend: 📈 <strong>Meningkat</strong>

⚠️ Mohon perhatian lebih untuk kedisiplinan waktu.
Keterlambatan berulang dapat mempengaruhi prestasi belajar.

<em>Pesan otomatis dari sistem absensi</em></div>
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

            {{-- [Kamera dipindah ke /attendance/kamera] --}}




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

        // ===== Toggle summary wali kelas fields =====
        function toggleSummaryFields() {
            const checkbox = document.getElementById('summaryWaliKelasEnabled');
            const fields   = document.getElementById('summaryWaliKelasFields');
            if (checkbox.checked) {
                fields.classList.remove('opacity-40', 'pointer-events-none');
            } else {
                fields.classList.add('opacity-40', 'pointer-events-none');
            }
        }

        function updateSummaryDayStyle(checkbox, num) {
            const label = document.getElementById('summaryDayLabel' + num);
            if (checkbox.checked) {
                label.className = label.className.replace(/border-gray-300[^'"]*/g, '');
                label.classList.add('border-green-500', 'bg-green-50', 'text-green-700');
                label.classList.remove('border-gray-300', 'text-gray-600');
            } else {
                label.classList.remove('border-green-500', 'bg-green-50', 'text-green-700');
                label.classList.add('border-gray-300', 'text-gray-600');
            }
        }

        async function sendSummaryNow() {
            const btn    = event.currentTarget;
            const result = document.getElementById('summaryResult');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
            result.className = 'mt-3 p-3 rounded-lg text-sm bg-blue-50 text-blue-700';
            result.textContent = 'Sedang mengirim ringkasan ke wali kelas...';
            result.classList.remove('hidden');

            try {
                const res = await fetch('{{ route("attendance.settings.send-summary") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                result.className = 'mt-3 p-3 rounded-lg text-sm ' +
                    (data.success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
                result.innerHTML = '<strong>' + data.message + '</strong>' +
                    (data.output ? '<pre class="mt-2 text-xs whitespace-pre-wrap">' + data.output + '</pre>' : '');
            } catch (e) {
                result.className = 'mt-3 p-3 rounded-lg text-sm bg-red-50 text-red-700';
                result.textContent = 'Gagal terhubung ke server.';
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Ringkasan Sekarang';
        }

        // ===== Waka Kesiswaan =====
        function toggleWakaFields() {
            const cb = document.getElementById('wakaSummaryEnabled');
            const f  = document.getElementById('wakaFields');
            cb.checked ? f.classList.remove('opacity-40','pointer-events-none')
                       : f.classList.add('opacity-40','pointer-events-none');
        }

        function updateWakaDayStyle(checkbox, num) {
            const label = document.getElementById('wakaDayLabel' + num);
            if (checkbox.checked) {
                label.classList.remove('border-gray-300','dark:border-gray-600','text-gray-600','dark:text-gray-400');
                label.classList.add('border-blue-500','bg-blue-50','dark:bg-blue-900/20','text-blue-700','dark:text-blue-300');
            } else {
                label.classList.remove('border-blue-500','bg-blue-50','dark:bg-blue-900/20','text-blue-700','dark:text-blue-300');
                label.classList.add('border-gray-300','dark:border-gray-600','text-gray-600','dark:text-gray-400');
            }
        }

        async function sendWakaNow(type) {
            const btn    = event.currentTarget;
            const result = document.getElementById('wakaResult');
            const label  = type === 'pulang' ? 'Laporan Pulang Waka' : 'Laporan Masuk Waka';
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
            result.className = 'mt-3 p-3 rounded-lg text-sm bg-blue-50 text-blue-700';
            result.textContent = 'Sedang mengirim ' + label + '...';
            result.classList.remove('hidden');
            try {
                const res  = await fetch('{{ route("attendance.settings.send-waka-summary") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ type })
                });
                const data = await res.json();
                result.className = 'mt-3 p-3 rounded-lg text-sm ' +
                    (data.success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
                result.innerHTML = '<strong>' + data.message + '</strong>' +
                    (data.output ? '<pre class="mt-2 text-xs whitespace-pre-wrap">' + data.output + '</pre>' : '');
            } catch (e) {
                result.className = 'mt-3 p-3 rounded-lg text-sm bg-red-50 text-red-700';
                result.textContent = 'Gagal terhubung ke server.';
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim ' + (type === 'pulang' ? 'Pulang' : 'Masuk') + ' Sekarang';
        }

        // ===== Kepala Sekolah =====
        function toggleKepsekFields() {
            const cb = document.getElementById('kepsekSummaryEnabled');
            const f  = document.getElementById('kepsekFields');
            cb.checked ? f.classList.remove('opacity-40','pointer-events-none')
                       : f.classList.add('opacity-40','pointer-events-none');
        }

        function updateKepsekDayStyle(checkbox, num) {
            const label = document.getElementById('kepsekDayLabel' + num);
            if (checkbox.checked) {
                label.classList.remove('border-gray-300','dark:border-gray-600','text-gray-600','dark:text-gray-400');
                label.classList.add('border-purple-500','bg-purple-50','dark:bg-purple-900/20','text-purple-700','dark:text-purple-300');
            } else {
                label.classList.remove('border-purple-500','bg-purple-50','dark:bg-purple-900/20','text-purple-700','dark:text-purple-300');
                label.classList.add('border-gray-300','dark:border-gray-600','text-gray-600','dark:text-gray-400');
            }
        }

        async function sendKepsekNow(type) {
            const btn    = event.currentTarget;
            const result = document.getElementById('kepsekResult');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
            result.className = 'mt-3 p-3 rounded-lg text-sm bg-purple-50 text-purple-700';
            result.textContent = 'Sedang mengirim laporan ke kepala sekolah...';
            result.classList.remove('hidden');
            try {
                const res  = await fetch('{{ route("attendance.settings.send-kepsek-summary") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ type })
                });
                const data = await res.json();
                result.className = 'mt-3 p-3 rounded-lg text-sm ' +
                    (data.success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
                result.innerHTML = '<strong>' + data.message + '</strong>' +
                    (data.output ? '<pre class="mt-2 text-xs whitespace-pre-wrap">' + data.output + '</pre>' : '');
            } catch (e) {
                result.className = 'mt-3 p-3 rounded-lg text-sm bg-red-50 text-red-700';
                result.textContent = 'Gagal terhubung ke server.';
            }
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim ' + (type === 'pulang' ? 'Pulang' : 'Masuk') + ' Sekarang';
        }

        // ===== Toggle late warning fields =====
        function toggleLateWarningFields() {
            const checkbox = document.getElementById('lateWarningEnabled');
            const fields   = document.getElementById('lateWarningFields');
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

                // Kumpulkan hari ringkasan ke settings[summary_send_days]
                const summaryChecked = [...document.querySelectorAll('input[name="summary_days[]"]:checked')].map(el => el.value);
                document.getElementById('summaryDaysHidden').value = summaryChecked.join(',');

                // Kumpulkan hari waka
                const wakaChecked = [...document.querySelectorAll('input[name="waka_days[]"]:checked')].map(el => el.value);
                document.getElementById('wakaDaysHidden').value = wakaChecked.join(',');

                // Kumpulkan hari kepsek
                const kepsekChecked = [...document.querySelectorAll('input[name="kepsek_days[]"]:checked')].map(el => el.value);
                document.getElementById('kepsekDaysHidden').value = kepsekChecked.join(',');
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

        // ===== Initialize toggle states on page load =====
        document.addEventListener('DOMContentLoaded', function() {
            toggleAbsentNotifyFields();
            toggleLateWarningFields();
        });

            initCameraDropdowns(); // auto-load kamera saat halaman buka
        });
    </script>
    @endpush
</x-app-layout>
