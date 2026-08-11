<x-app-layout>
    <x-slot name="title">Settings WhatsApp</x-slot>
    <x-slot name="pageTitle">Pengaturan WhatsApp</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">⚙️ Pengaturan WhatsApp</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Konfigurasi notifikasi WhatsApp ke orang tua siswa</p>
            </div>
            <a href="{{ route('whatsapp.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 text-green-700 dark:text-green-400">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @php
            $settings = [];
            $settings['notification'] = \App\Models\AttendanceSetting::getGroup('notification');
            $settings['general'] = \App\Models\AttendanceSetting::getGroup('general');
        @endphp

        <form action="{{ route('attendance.settings.update') }}" method="POST">
            @csrf
            @method('PUT')


            {{-- Notifikasi WhatsApp --}}
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
                                       {{ old('settings.enable_parent_notification', \App\Models\AttendanceSetting::get('enable_parent_notification', '1', 'notification')) == '1' ? 'checked' : '' }}
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
                                       {{ old('settings.include_photo_in_notification', \App\Models\AttendanceSetting::get('include_photo_in_notification', '0', 'notification')) == '1' ? 'checked' : '' }}
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

                        {{-- Cron Info --}}
                        <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded">
                            <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-2">ℹ️ Catatan Setup Cron Job</h4>
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                Fitur notifikasi alpha otomatis memerlukan cron job di server. Lihat dokumentasi lengkap di halaman <strong>Settings > Pengaturan Sistem</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </x-card>


            {{-- Late Warning Settings --}}
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
                    </div>
                </div>
            </x-card>

            {{-- Submit Button --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('whatsapp.index') }}" class="px-6 py-2.5 text-sm font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 text-sm font-semibold rounded-lg bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 shadow-lg hover:shadow-xl transition-all">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
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
        document.querySelector('form')?.addEventListener('submit', function() {
            const checked = [...document.querySelectorAll('input[name="absent_days[]"]:checked')]
                .map(el => el.value);
            document.querySelector('input[name="settings[absent_notify_days]"]').value = checked.join(',');
        });

        // ===== Test Notification =====
        function testNotification() {
            const phone = document.getElementById('test_phone').value;
            if (!phone || !phone.match(/^628[0-9]{9,12}$/)) {
                alert('Masukkan nomor WhatsApp yang valid (format: 628123456789)');
                return;
            }

            const btn = event.target.closest('button');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';

            fetch('{{ route("whatsapp.send.submit") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    phone: phone,
                    message: '🧪 Test Notifikasi dari Sistem Absensi QR\n\nJika Anda menerima pesan ini, WhatsApp Gateway berfungsi dengan baik! ✅'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Test notifikasi berhasil dikirim!');
                } else {
                    alert('❌ Gagal mengirim: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                alert('❌ Error: ' + error.message);
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            });
        }

        // ===== Initialize toggle states on page load =====
        document.addEventListener('DOMContentLoaded', function() {
            toggleAbsentNotifyFields();
            toggleLateWarningFields();
        });
    </script>
    @endpush
</x-app-layout>
