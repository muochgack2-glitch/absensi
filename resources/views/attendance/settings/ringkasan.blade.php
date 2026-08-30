<x-app-layout>
    <x-slot name="title">Ringkasan</x-slot>
    <x-slot name="pageTitle">Ringkasan</x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📊 Setting Ringkasan</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Konfigurasi ringkasan kehadiran ke Wali Kelas, Waka, dan Kepala Sekolah</p>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <span class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                <ul class="text-sm text-red-700 dark:text-red-400 list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('attendance.ringkasan.update') }}" method="POST" id="ringkasanForm">
            @csrf
            @method('PUT')

            {{-- Wali Kelas --}}
            <x-card class="mb-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">📊 Ringkasan ke Wali Kelas</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kirim ringkasan harian (hadir/izin/alfa) ke wali kelas masing-masing kelas via WhatsApp</p>
                    </div>
                </div>
                <div class="space-y-5">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Aktifkan Ringkasan Otomatis ke Wali Kelas</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sistem akan kirim WA ringkasan kehadiran ke setiap wali kelas secara otomatis</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[summary_wali_kelas_enabled]" value="1" id="summaryWaliKelasEnabled"
                                       @if(old('settings.summary_wali_kelas_enabled', $settings['notification']['summary_wali_kelas_enabled'] ?? '0') == '1') checked @endif
                                       onchange="toggleSummaryFields()" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 dark:peer-focus:ring-green-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                        </div>
                    </div>
                    <div id="summaryWaliKelasFields" class="space-y-4 {{ old('settings.summary_wali_kelas_enabled', $settings['notification']['summary_wali_kelas_enabled'] ?? '0') == '1' ? '' : 'opacity-40 pointer-events-none' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input type="time" name="settings[summary_send_time]" label="🌅 Jam Ringkasan Masuk"
                                :value="old('settings.summary_send_time', $settings['notification']['summary_send_time'] ?? '09:00')"
                                helper="Ringkasan kehadiran masuk dikirim pada jam ini" />
                            <x-input type="time" name="settings[summary_pulang_send_time]" label="🌆 Jam Ringkasan Pulang"
                                :value="old('settings.summary_pulang_send_time', $settings['notification']['summary_pulang_send_time'] ?? '15:00')"
                                helper="Ringkasan kehadiran pulang dikirim pada jam ini" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📅 Hari Pengiriman</label>
                            @php
                                $summaryDays = old('settings.summary_send_days', $settings['notification']['summary_send_days'] ?? '1,2,3,4,5');
                                $summaryDaysArr = explode(',', $summaryDays);
                                $dayNames = ['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'];
                            @endphp
                            <input type="hidden" name="settings[summary_send_days]" value="" id="summaryDaysHidden">
                            <div class="flex flex-wrap gap-2">
                                @foreach($dayNames as $num => $name)
                                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition-all {{ in_array($num, $summaryDaysArr) ? 'border-green-500 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-300' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}" id="summaryDayLabel{{ $num }}">
                                        <input type="checkbox" name="summary_days[]" value="{{ $num }}" {{ in_array($num, $summaryDaysArr) ? 'checked' : '' }} onchange="updateSummaryDayStyle(this, '{{ $num }}')" class="accent-green-500">
                                        {{ $name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded">
                            <h4 class="font-semibold text-green-900 dark:text-green-300 mb-2">📋 Persyaratan:</h4>
                            <ul class="text-xs text-green-800 dark:text-green-400 space-y-1 list-disc list-inside">
                                <li>Setiap kelas harus memiliki wali kelas yang terdaftar di sistem</li>
                                <li>Nomor HP wali kelas harus diisi di profil pengguna</li>
                                <li>WhatsApp Gateway harus aktif saat jam pengiriman</li>
                                <li>Cron job Laravel harus terpasang di server</li>
                            </ul>

                        {{-- Preview Pesan --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <button type="button" onclick="togglePreview('previewWali')"
                                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <span class="flex items-center gap-2"><i class="fab fa-whatsapp text-green-500"></i> Preview Pesan WA</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" id="previewWaliIcon"></i>
                            </button>
                            <div id="previewWali" class="hidden p-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-900">
                                {{-- Masuk --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">📨 Pesan Masuk</p>
                                    <div class="bg-[#dcf8c6] dark:bg-green-900/40 rounded-2xl rounded-tl-sm px-4 py-3 text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap shadow-sm">
*RINGKASAN KEHADIRAN MASUK*
Kelas  : *[Nama Kelas]*
Tanggal: [Hari, DD Bulan YYYY]

Hadir tepat waktu : [N] siswa
Terlambat         : [N] siswa
Izin              : [N] siswa
Alfa              : [N] siswa
Total             : [N] siswa
Kehadiran         : [N]%

*Siswa tidak hadir (alfa):*
1. [Nama Siswa]

_Sistem Absensi SMK PGRI Blora_</div>
                                </div>
                                {{-- Pulang --}}
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">📨 Pesan Pulang</p>
                                    <div class="bg-[#dcf8c6] dark:bg-green-900/40 rounded-2xl rounded-tl-sm px-4 py-3 text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap shadow-sm">
*RINGKASAN KEPULANGAN*
Kelas  : *[Nama Kelas]*
Tanggal: [Hari, DD Bulan YYYY]

Hadir hari ini     : [N] siswa
Pulang tepat waktu : [N] siswa
Pulang lebih awal  : [N] siswa
Belum pulang       : [N] siswa
Izin               : [N] siswa
Alfa               : [N] siswa
Total              : [N] siswa

_Sistem Absensi SMK PGRI Blora_</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" onclick="sendSummaryNow()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200">
                                <i class="fas fa-paper-plane mr-2"></i>Kirim Ringkasan Sekarang
                            </button>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Kirim manual ke semua wali kelas hari ini</p>
                        </div>
                        <div id="summaryResult" class="hidden"></div>
                    </div>
                </div>
            </x-card>

            {{-- Waka Kesiswaan --}}
            <x-card class="mb-6">
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
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Aktifkan Ringkasan Otomatis ke Waka Kesiswaan</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Waka menerima laporan harian (masuk & pulang) secara otomatis</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[waka_summary_enabled]" value="1" id="wakaSummaryEnabled"
                                       @if(old('settings.waka_summary_enabled', $settings['notification']['waka_summary_enabled'] ?? '0') == '1') checked @endif
                                       onchange="toggleWakaFields()" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                    </div>
                    <div id="wakaFields" class="space-y-4 {{ old('settings.waka_summary_enabled', $settings['notification']['waka_summary_enabled'] ?? '0') == '1' ? '' : 'opacity-40 pointer-events-none' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input type="time" name="settings[waka_summary_masuk_time]" label="🌅 Jam Kirim Masuk"
                                :value="old('settings.waka_summary_masuk_time', $settings['notification']['waka_summary_masuk_time'] ?? '08:00')"
                                helper="Laporan kehadiran masuk dikirim pada jam ini" />
                            <x-input type="time" name="settings[waka_summary_pulang_time]" label="🌆 Jam Kirim Pulang"
                                :value="old('settings.waka_summary_pulang_time', $settings['notification']['waka_summary_pulang_time'] ?? '15:00')"
                                helper="Laporan kepulangan dikirim pada jam ini" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📅 Hari Pengiriman</label>
                            @php $wakaDays = old('settings.waka_summary_send_days', $settings['notification']['waka_summary_send_days'] ?? '1,2,3,4,5'); $wakaDaysArr = explode(',', $wakaDays); @endphp
                            <input type="hidden" name="settings[waka_summary_send_days]" value="" id="wakaDaysHidden">
                            <div class="flex flex-wrap gap-2">
                                @foreach(['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'] as $num => $name)
                                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition-all {{ in_array($num, $wakaDaysArr) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}" id="wakaDayLabel{{ $num }}">
                                        <input type="checkbox" name="waka_days[]" value="{{ $num }}" {{ in_array($num, $wakaDaysArr) ? 'checked' : '' }} onchange="updateWakaDayStyle(this, '{{ $num }}')" class="accent-blue-500">{{ $name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Preview Pesan Waka --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <button type="button" onclick="togglePreview('previewWaka')"
                                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <span class="flex items-center gap-2"><i class="fab fa-whatsapp text-green-500"></i> Preview Pesan WA</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" id="previewWakaIcon"></i>
                            </button>
                            <div id="previewWaka" class="hidden p-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-900">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">📨 Pesan Masuk</p>
                                    <div class="bg-[#dcf8c6] dark:bg-green-900/40 rounded-2xl rounded-tl-sm px-4 py-3 text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap shadow-sm">
📊 *LAPORAN KEHADIRAN HARIAN*
*[Nama Sekolah]*
[Hari, DD Bulan YYYY]

👥 Total Siswa   : [N] orang
✅ Hadir         : [N] ([N]%)
   ↳ Tepat waktu : [N] siswa
   ↳ Terlambat   : [N] siswa
❌ Alpha         : [N] siswa
📋 Izin          : [N] siswa
🤒 Sakit         : [N] siswa

Status: [Baik/Perhatian]

*Detail Siswa Alpha:*
📚 *[Kelas]*
   Wali Kelas: [Nama]
   1. [Nama Siswa]

_Sistem Absensi Otomatis_</div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">📨 Pesan Pulang</p>
                                    <div class="bg-[#dcf8c6] dark:bg-green-900/40 rounded-2xl rounded-tl-sm px-4 py-3 text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap shadow-sm">
🌆 *LAPORAN KEPULANGAN HARIAN*
*[Nama Sekolah]*
[Hari, DD Bulan YYYY]

👥 Total Siswa     : [N] orang
🏫 Hadir hari ini  : [N] siswa
✅ Sudah pulang    : [N] siswa
   ↳ Tepat waktu  : [N] siswa
   ↳ Pulang cepat : [N] siswa
⏳ Belum pulang   : [N] siswa

*Detail Belum Pulang:*
📚 *[Kelas]*
   Wali Kelas: [Nama]
   1. [Nama Siswa]

_Sistem Absensi Otomatis_</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button" onclick="sendWakaNow('masuk')" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200"><i class="fas fa-paper-plane mr-2"></i>Kirim Masuk Sekarang</button>
                            <button type="button" onclick="sendWakaNow('pulang')" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-all duration-200"><i class="fas fa-paper-plane mr-2"></i>Kirim Pulang Sekarang</button>
                        </div>
                        <div id="wakaResult" class="hidden"></div>
                    </div>
                </div>
            </x-card>

            {{-- Kepala Sekolah --}}
            <x-card class="mb-6">
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
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Aktifkan Ringkasan Otomatis ke Kepala Sekolah</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kepala sekolah menerima laporan harian (masuk & pulang) secara otomatis</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[kepsek_summary_enabled]" value="1" id="kepsekSummaryEnabled"
                                       @if(old('settings.kepsek_summary_enabled', $settings['notification']['kepsek_summary_enabled'] ?? '0') == '1') checked @endif
                                       onchange="toggleKepsekFields()" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 dark:peer-focus:ring-purple-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                            </label>
                        </div>
                    </div>
                    <div id="kepsekFields" class="space-y-4 {{ old('settings.kepsek_summary_enabled', $settings['notification']['kepsek_summary_enabled'] ?? '0') == '1' ? '' : 'opacity-40 pointer-events-none' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input type="time" name="settings[kepsek_summary_time]" label="🌅 Jam Kirim Masuk"
                                :value="old('settings.kepsek_summary_time', $settings['notification']['kepsek_summary_time'] ?? '08:30')"
                                helper="Laporan kehadiran masuk dikirim pada jam ini" />
                            <x-input type="time" name="settings[kepsek_summary_pulang_time]" label="🌆 Jam Kirim Pulang"
                                :value="old('settings.kepsek_summary_pulang_time', $settings['notification']['kepsek_summary_pulang_time'] ?? '15:30')"
                                helper="Laporan kepulangan dikirim pada jam ini" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📅 Hari Pengiriman</label>
                            @php $kepsekDays = old('settings.kepsek_summary_send_days', $settings['notification']['kepsek_summary_send_days'] ?? '1,2,3,4,5'); $kepsekDaysArr = explode(',', $kepsekDays); @endphp
                            <input type="hidden" name="settings[kepsek_summary_send_days]" value="" id="kepsekDaysHidden">
                            <div class="flex flex-wrap gap-2">
                                @foreach(['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'] as $num => $name)
                                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition-all {{ in_array($num, $kepsekDaysArr) ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}" id="kepsekDayLabel{{ $num }}">
                                        <input type="checkbox" name="kepsek_days[]" value="{{ $num }}" {{ in_array($num, $kepsekDaysArr) ? 'checked' : '' }} onchange="updateKepsekDayStyle(this, '{{ $num }}')" class="accent-purple-500">{{ $name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Preview Pesan Kepsek --}}
                        <div class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
                            <button type="button" onclick="togglePreview('previewKepsek')"
                                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <span class="flex items-center gap-2"><i class="fab fa-whatsapp text-green-500"></i> Preview Pesan WA</span>
                                <i class="fas fa-chevron-down text-xs transition-transform" id="previewKepsekIcon"></i>
                            </button>
                            <div id="previewKepsek" class="hidden p-4 grid grid-cols-1 md:grid-cols-2 gap-4 bg-white dark:bg-gray-900">
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">📨 Pesan Masuk</p>
                                    <div class="bg-[#dcf8c6] dark:bg-green-900/40 rounded-2xl rounded-tl-sm px-4 py-3 text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap shadow-sm">
📊 *LAPORAN KEHADIRAN HARIAN*
*[Nama Sekolah]*
[Hari, DD Bulan YYYY]

👥 Total Siswa   : [N] orang
✅ Hadir         : [N] ([N]%)
   ↳ Tepat waktu : [N] siswa
   ↳ Terlambat   : [N] siswa
❌ Alpha         : [N] siswa
📋 Izin          : [N] siswa
🤒 Sakit         : [N] siswa

Status: [Baik/Perhatian]

*Detail Siswa Alpha:*
📚 *[Kelas]*
   Wali Kelas: [Nama]
   1. [Nama Siswa]

_Sistem Absensi Otomatis_</div>
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wide">📨 Pesan Pulang</p>
                                    <div class="bg-[#dcf8c6] dark:bg-green-900/40 rounded-2xl rounded-tl-sm px-4 py-3 text-sm font-mono text-gray-800 dark:text-gray-200 whitespace-pre-wrap shadow-sm">
🌆 *LAPORAN KEPULANGAN HARIAN*
*[Nama Sekolah]*
[Hari, DD Bulan YYYY]

👥 Total Siswa     : [N] orang
🏫 Hadir hari ini  : [N] siswa
✅ Sudah pulang    : [N] siswa
   ↳ Tepat waktu  : [N] siswa
   ↳ Pulang cepat : [N] siswa
⏳ Belum pulang   : [N] siswa

*Detail Belum Pulang:*
📚 *[Kelas]*
   Wali Kelas: [Nama]
   1. [Nama Siswa]

_Sistem Absensi Otomatis_</div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button type="button" onclick="sendKepsekNow('masuk')" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-all duration-200"><i class="fas fa-paper-plane mr-2"></i>Kirim Masuk Sekarang</button>
                            <button type="button" onclick="sendKepsekNow('pulang')" class="inline-flex items-center px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-all duration-200"><i class="fas fa-paper-plane mr-2"></i>Kirim Pulang Sekarang</button>
                        </div>
                        <div id="kepsekResult" class="hidden"></div>
                    </div>
                </div>
            </x-card>

            {{-- Action Button --}}
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow">
                    <i class="fas fa-save mr-2"></i>Simpan Setting Ringkasan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    function toggleSummaryFields() {
        const cb = document.getElementById('summaryWaliKelasEnabled');
        const f  = document.getElementById('summaryWaliKelasFields');
        cb.checked ? f.classList.remove('opacity-40','pointer-events-none') : f.classList.add('opacity-40','pointer-events-none');
    }
    function updateSummaryDayStyle(checkbox, num) {
        const label = document.getElementById('summaryDayLabel' + num);
        if (checkbox.checked) { label.classList.remove('border-gray-300','text-gray-600'); label.classList.add('border-green-500','bg-green-50','text-green-700'); }
        else { label.classList.remove('border-green-500','bg-green-50','text-green-700'); label.classList.add('border-gray-300','text-gray-600'); }
    }
    async function sendSummaryNow() {
        const btn = event.currentTarget, result = document.getElementById('summaryResult');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
        result.className = 'mt-3 p-3 rounded-lg text-sm bg-blue-50 text-blue-700';
        result.textContent = 'Sedang mengirim ringkasan ke wali kelas...'; result.classList.remove('hidden');
        try {
            const res = await fetch('{{ route("attendance.settings.send-summary") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' } });
            const data = await res.json();
            result.className = 'mt-3 p-3 rounded-lg text-sm ' + (data.success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
            result.innerHTML = '<strong>' + data.message + '</strong>' + (data.output ? '<pre class="mt-2 text-xs whitespace-pre-wrap">' + data.output + '</pre>' : '');
        } catch(e) { result.className = 'mt-3 p-3 rounded-lg text-sm bg-red-50 text-red-700'; result.textContent = 'Gagal terhubung ke server.'; }
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim Ringkasan Sekarang';
    }
    function toggleWakaFields() {
        const cb = document.getElementById('wakaSummaryEnabled'), f = document.getElementById('wakaFields');
        cb.checked ? f.classList.remove('opacity-40','pointer-events-none') : f.classList.add('opacity-40','pointer-events-none');
    }
    function updateWakaDayStyle(checkbox, num) {
        const label = document.getElementById('wakaDayLabel' + num);
        if (checkbox.checked) { label.classList.remove('border-gray-300','text-gray-600'); label.classList.add('border-blue-500','bg-blue-50','text-blue-700'); }
        else { label.classList.remove('border-blue-500','bg-blue-50','text-blue-700'); label.classList.add('border-gray-300','text-gray-600'); }
    }
    async function sendWakaNow(type) {
        const btn = event.currentTarget, result = document.getElementById('wakaResult');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
        result.className = 'mt-3 p-3 rounded-lg text-sm bg-blue-50 text-blue-700'; result.textContent = 'Sedang mengirim...'; result.classList.remove('hidden');
        try {
            const res = await fetch('{{ route("attendance.settings.send-waka-summary") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ type }) });
            const data = await res.json();
            result.className = 'mt-3 p-3 rounded-lg text-sm ' + (data.success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
            result.innerHTML = '<strong>' + data.message + '</strong>' + (data.output ? '<pre class="mt-2 text-xs whitespace-pre-wrap">' + data.output + '</pre>' : '');
        } catch(e) { result.className = 'mt-3 p-3 rounded-lg text-sm bg-red-50 text-red-700'; result.textContent = 'Gagal terhubung ke server.'; }
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim ' + (type === 'pulang' ? 'Pulang' : 'Masuk') + ' Sekarang';
    }
    function toggleKepsekFields() {
        const cb = document.getElementById('kepsekSummaryEnabled'), f = document.getElementById('kepsekFields');
        cb.checked ? f.classList.remove('opacity-40','pointer-events-none') : f.classList.add('opacity-40','pointer-events-none');
    }
    function updateKepsekDayStyle(checkbox, num) {
        const label = document.getElementById('kepsekDayLabel' + num);
        if (checkbox.checked) { label.classList.remove('border-gray-300','text-gray-600'); label.classList.add('border-purple-500','bg-purple-50','text-purple-700'); }
        else { label.classList.remove('border-purple-500','bg-purple-50','text-purple-700'); label.classList.add('border-gray-300','text-gray-600'); }
    }
    async function sendKepsekNow(type) {
        const btn = event.currentTarget, result = document.getElementById('kepsekResult');
        btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
        result.className = 'mt-3 p-3 rounded-lg text-sm bg-purple-50 text-purple-700'; result.textContent = 'Sedang mengirim...'; result.classList.remove('hidden');
        try {
            const res = await fetch('{{ route("attendance.settings.send-kepsek-summary") }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Content-Type': 'application/json', 'Accept': 'application/json' }, body: JSON.stringify({ type }) });
            const data = await res.json();
            result.className = 'mt-3 p-3 rounded-lg text-sm ' + (data.success ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700');
            result.innerHTML = '<strong>' + data.message + '</strong>' + (data.output ? '<pre class="mt-2 text-xs whitespace-pre-wrap">' + data.output + '</pre>' : '');
        } catch(e) { result.className = 'mt-3 p-3 rounded-lg text-sm bg-red-50 text-red-700'; result.textContent = 'Gagal terhubung ke server.'; }
        btn.disabled = false; btn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Kirim ' + (type === 'pulang' ? 'Pulang' : 'Masuk') + ' Sekarang';
    }

    // Kumpulkan hari sebelum submit
    document.getElementById('ringkasanForm').addEventListener('submit', function() {
        document.getElementById('summaryDaysHidden').value = [...document.querySelectorAll('input[name="summary_days[]"]:checked')].map(el => el.value).join(',');
        document.getElementById('wakaDaysHidden').value    = [...document.querySelectorAll('input[name="waka_days[]"]:checked')].map(el => el.value).join(',');
        document.getElementById('kepsekDaysHidden').value  = [...document.querySelectorAll('input[name="kepsek_days[]"]:checked')].map(el => el.value).join(',');
    });

    document.addEventListener('DOMContentLoaded', function() {
        toggleSummaryFields();
        toggleWakaFields();
        toggleKepsekFields();
    });

    function togglePreview(id) {
        const el   = document.getElementById(id);
        const icon = document.getElementById(id + 'Icon');
        el.classList.toggle('hidden');
        icon.style.transform = el.classList.contains('hidden') ? '' : 'rotate(180deg)';
    }
    </script>
    @endpush
</x-app-layout>
