<x-app-layout>
    <x-slot name="title">Notifikasi</x-slot>
    <x-slot name="pageTitle">Notifikasi</x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🔔 Setting Notifikasi</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Konfigurasi notifikasi WhatsApp ke orang tua siswa</p>
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

        <form action="{{ route('attendance.notifikasi.update') }}" method="POST" id="notifForm">
            @csrf
            @method('PUT')

            {{-- Notifikasi Ortu --}}
            <x-card class="mb-6">
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
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Kirim Notifikasi ke Orang Tua</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Aktifkan notifikasi WhatsApp otomatis saat siswa absen</p>
                        </div>
                        <div>
                            <input type="hidden" name="settings[enable_parent_notification]" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[enable_parent_notification]" value="1"
                                       {{ old('settings.enable_parent_notification', $settings['notification']['enable_parent_notification'] ?? '1') == '1' ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Include Photo --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Sertakan Foto dalam Notifikasi</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kirim foto absensi bersama dengan pesan WhatsApp</p>
                        </div>
                        <div>
                            <input type="hidden" name="settings[include_photo_in_notification]" value="0">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[include_photo_in_notification]" value="1"
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
                            <input type="text" id="test_phone" placeholder="628123456789" pattern="^628[0-9]{9,12}$"
                                   class="flex-1 px-4 py-2 border border-yellow-300 dark:border-yellow-700 rounded-lg focus:ring-2 focus:ring-yellow-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                            <button type="button" onclick="testNotification()"
                                    class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-all duration-200 font-medium">
                                <i class="fas fa-paper-plane mr-2"></i>Kirim Test
                            </button>
                        </div>
                        <p class="text-xs text-yellow-800 dark:text-yellow-200 mt-2">Pastikan WhatsApp Gateway sudah berjalan di http://localhost:3002</p>
                    </div>
                </div>
            </x-card>

            {{-- Notifikasi Check-In Real-time --}}
            <x-card class="mb-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">⚡ Notifikasi Check-In (Real-time)</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">WA dikirim <strong>langsung saat siswa scan QR</strong> — bukan terjadwal</p>
                    </div>
                </div>
                <div class="space-y-4">
                    {{-- Toggle Kirim Semua Check-in --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Kirim Notif Semua Check-In</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">WA dikirim ke ortu untuk semua siswa yang scan — hadir, toleransi, maupun terlambat</p>
                        </div>
                        <div>
                            <input type="hidden" name="settings[notify_all_checkin]" value="false">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[notify_all_checkin]" value="true" id="notifyAllCheckin"
                                       @if(old('settings.notify_all_checkin', $settings['notification']['notify_all_checkin'] ?? $settings['general']['notify_all_checkin'] ?? 'false') === 'true') checked @endif
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-500"></div>
                            </label>
                        </div>
                    </div>
                    {{-- Toggle Terlambat --}}
                    <div class="flex items-center justify-between p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg border border-yellow-200 dark:border-yellow-700">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">⚡ Notifikasi Terlambat Saja</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">WA dikirim <strong>hanya jika</strong> siswa scan dengan status Terlambat — yang hadir tepat waktu tidak dapat notif</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[late_notify_enabled]" value="true" id="lateNotifyEnabled"
                                       @if(old('settings.late_notify_enabled', $settings['notification']['late_notify_enabled'] ?? $settings['general']['late_notify_enabled'] ?? 'false') === 'true') checked @endif
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 dark:peer-focus:ring-yellow-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-500"></div>
                            </label>
                        </div>
                    </div>
                    {{-- Info box --}}
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg text-xs text-blue-800 dark:text-blue-200">
                        💡 <strong>Tips:</strong> Aktifkan <em>Kirim Semua</em> jika ingin ortu selalu tahu saat anak scan. Aktifkan <em>Terlambat Saja</em> untuk hemat kuota WA — hanya kirim jika ada masalah.
                    </div>
                </div>
            </x-card>

            {{-- Notifikasi Alpha Otomatis --}}
            <x-card class="mb-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-orange-500 flex items-center justify-center text-white text-2xl mr-4">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Notifikasi Ketidakhadiran Otomatis</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Kirim WA ke orang tua secara otomatis saat siswa alpha — <strong>terjadwal via cron</strong></p>
                    </div>
                </div>
                <div class="space-y-5">
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Aktifkan Notifikasi Alpha Otomatis</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sistem otomatis kirim WA ke orang tua saat siswa tidak hadir</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[auto_absent_notify]" value="1" id="autoAbsentNotify"
                                       @if(old('settings.auto_absent_notify', $settings['notification']['auto_absent_notify'] ?? $settings['general']['auto_absent_notify'] ?? '0') == '1') checked @endif
                                       onchange="toggleAbsentNotifyFields()" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 dark:peer-focus:ring-red-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500"></div>
                            </label>
                        </div>
                    </div>

                    <div id="absentNotifyFields" class="space-y-4 {{ old('settings.auto_absent_notify', $settings['notification']['auto_absent_notify'] ?? '0') == '1' ? '' : 'opacity-40 pointer-events-none' }}">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input type="time" name="settings[absent_notify_time]" label="Jam Pengiriman Notifikasi Alpha"
                                :value="old('settings.absent_notify_time', $settings['notification']['absent_notify_time'] ?? '09:00')"
                                helper="WA dikirim ke orang tua pada jam ini setiap hari aktif" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">📅 Hari Aktif Pengiriman</label>
                            @php
                                $activeDays = old('settings.absent_notify_days', $settings['notification']['absent_notify_days'] ?? '1,2,3,4,5');
                                $activeDaysArr = explode(',', $activeDays);
                                $dayNames = ['1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu'];
                            @endphp
                            <input type="hidden" name="settings[absent_notify_days]" value="">
                            <div class="flex flex-wrap gap-2">
                                @foreach($dayNames as $num => $name)
                                    <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 cursor-pointer transition-all {{ in_array($num, $activeDaysArr) ? 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400' }}" id="dayLabel{{ $num }}">
                                        <input type="checkbox" name="absent_days[]" value="{{ $num }}" {{ in_array($num, $activeDaysArr) ? 'checked' : '' }} onchange="updateDayStyle(this, '{{ $num }}')" class="accent-red-500">
                                        {{ $name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Cron Setup Panel --}}
                        <div class="mt-2 rounded-xl border border-orange-200 dark:border-orange-800 overflow-hidden">
                            <div class="flex items-center gap-3 px-4 py-3 bg-orange-50 dark:bg-orange-900/30 border-b border-orange-200 dark:border-orange-800">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-sm" style="background:#ea580c;"><i class="fas fa-terminal"></i></div>
                                <div>
                                    <div class="text-sm font-bold text-orange-900 dark:text-orange-200">⚙️ Setup Cron Job di Server (Wajib 1x)</div>
                                    <div class="text-xs text-orange-700 dark:text-orange-400">Tanpa ini, notifikasi otomatis tidak akan berjalan</div>
                                </div>
                            </div>
                            <div class="p-4 space-y-3 bg-white dark:bg-gray-800">
                                <div class="flex gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center">1</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">SSH ke server lalu buka crontab:</p>
                                        <div class="flex items-center gap-2 mt-1.5 bg-gray-900 rounded-lg px-3 py-2">
                                            <code class="flex-1 text-xs text-green-400 font-mono select-all" id="cronStep1">crontab -e</code>
                                            <button type="button" onclick="copyCmd('cronStep1', this)" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors"><i class="fas fa-copy text-xs"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center">2</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Tambahkan baris ini:</p>
                                        <div class="flex items-center gap-2 mt-1.5 bg-gray-900 rounded-lg px-3 py-2">
                                            <code class="flex-1 text-xs text-green-400 font-mono select-all break-all" id="cronStep2">* * * * * cd /www/wwwroot/absensi &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</code>
                                            <button type="button" onclick="copyCmd('cronStep2', this)" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors"><i class="fas fa-copy text-xs"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <span class="flex-shrink-0 w-6 h-6 rounded-full bg-orange-500 text-white text-xs font-bold flex items-center justify-center">3</span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Verifikasi cron aktif:</p>
                                        <div class="flex items-center gap-2 mt-1.5 bg-gray-900 rounded-lg px-3 py-2">
                                            <code class="flex-1 text-xs text-green-400 font-mono select-all" id="cronStep3">crontab -l</code>
                                            <button type="button" onclick="copyCmd('cronStep3', this)" class="flex-shrink-0 text-gray-400 hover:text-white transition-colors"><i class="fas fa-copy text-xs"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Peringatan Keterlambatan --}}
            <x-card class="mb-6">
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
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Aktifkan Peringatan Keterlambatan</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kirim peringatan otomatis ke orang tua saat siswa sering terlambat</p>
                        </div>
                        <div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="settings[late_warning_enabled]" value="1" id="lateWarningEnabled"
                                       @if(old('settings.late_warning_enabled', $settings['notification']['late_warning_enabled'] ?? '0') == '1') checked @endif
                                       onchange="toggleLateWarningFields()" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-amber-300 dark:peer-focus:ring-amber-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-500"></div>
                            </label>
                        </div>
                    </div>
                    <div id="lateWarningFields" class="space-y-4 @if(old('settings.late_warning_enabled', $settings['notification']['late_warning_enabled'] ?? '0') != '1') opacity-40 pointer-events-none @endif">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-input type="number" name="settings[late_warning_threshold_minutes]" label="⏱️ Batas Keterlambatan (menit)"
                                :value="old('settings.late_warning_threshold_minutes', $settings['notification']['late_warning_threshold_minutes'] ?? '30')"
                                min="1" max="120" helper="Peringatan dikirim jika siswa terlambat minimal X menit" />
                            <x-input type="number" name="settings[late_warning_min_count]" label="🔢 Jumlah Minimal Keterlambatan"
                                :value="old('settings.late_warning_min_count', $settings['notification']['late_warning_min_count'] ?? '3')"
                                min="1" max="20" helper="Peringatan dikirim setelah terlambat X kali dalam sebulan" />
                        </div>
                        <div class="p-4 bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded">
                            <h4 class="font-semibold text-amber-900 dark:text-amber-300 mb-2">📊 Cara Kerja:</h4>
                            <div class="space-y-1 text-sm text-amber-800 dark:text-amber-200">
                                <p>• <strong>Real-time:</strong> Peringatan dikirim saat siswa check-in dengan status terlambat</p>
                                <p>• <strong>Kondisi:</strong> Hanya dikirim jika sudah terlambat minimal <strong>{{ old('settings.late_warning_min_count', $settings['notification']['late_warning_min_count'] ?? '3') }}x</strong> bulan ini</p>
                                <p>• <strong>Threshold:</strong> Terlambat minimal <strong>{{ old('settings.late_warning_threshold_minutes', $settings['notification']['late_warning_threshold_minutes'] ?? '30') }} menit</strong> dari jam masuk</p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Action Button --}}
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow">
                    <i class="fas fa-save mr-2"></i>Simpan Setting Notifikasi
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
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

    function toggleAbsentNotifyFields() {
        const checkbox = document.getElementById('autoAbsentNotify');
        const fields   = document.getElementById('absentNotifyFields');
        checkbox.checked ? fields.classList.remove('opacity-40', 'pointer-events-none')
                         : fields.classList.add('opacity-40', 'pointer-events-none');
    }

    function updateDayStyle(checkbox, num) {
        const label = document.getElementById('dayLabel' + num);
        if (checkbox.checked) {
            label.className = label.className.replace('border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400', 'border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300');
        } else {
            label.className = label.className.replace('border-red-500 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300', 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400');
        }
    }

    function copyCmd(elementId, btn) {
        const text = document.getElementById(elementId).innerText;
        const decoded = text.replace(/&amp;/g, '&').replace(/&gt;/g, '>').replace(/&lt;/g, '<');
        navigator.clipboard.writeText(decoded).then(() => {
            const icon = btn.querySelector('i');
            icon.className = 'fas fa-check text-xs text-green-400';
            setTimeout(() => { icon.className = 'fas fa-copy text-xs'; }, 2000);
        });
    }

    // Kumpulkan hari aktif sebelum submit
    document.getElementById('notifForm').addEventListener('submit', function() {
        const checked = [...document.querySelectorAll('input[name="absent_days[]"]:checked')].map(el => el.value);
        document.querySelector('input[name="settings[absent_notify_days]"]').value = checked.join(',');
    });

    function toggleLateWarningFields() {
        const checkbox = document.getElementById('lateWarningEnabled');
        const fields   = document.getElementById('lateWarningFields');
        checkbox.checked ? fields.classList.remove('opacity-40','pointer-events-none')
                         : fields.classList.add('opacity-40','pointer-events-none');
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleAbsentNotifyFields();
        toggleLateWarningFields();
    });
    </script>
    @endpush
</x-app-layout>
