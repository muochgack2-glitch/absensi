@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="title">Setting Waktu</x-slot>
    <x-slot name="pageTitle">Setting Waktu</x-slot>

    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">⏰ Setting Waktu</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Konfigurasi jam masuk, pulang, dan toleransi absensi</p>
            </div>
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
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('attendance.setting-waktu.update') }}" method="POST">
            @csrf
            @method('PUT')

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
                        <x-input
                            type="time"
                            name="settings[check_in_time]"
                            label="Jam Masuk"
                            :value="old('settings.check_in_time', $settings['time']['check_in_time'] ?? $settings['schedule']['check_in_time'] ?? '07:00')"
                            helper="Jam mulai absensi masuk"
                            required
                        />
                        <x-input
                            type="time"
                            name="settings[check_out_time]"
                            label="Jam Pulang (Resmi)"
                            :value="old('settings.check_out_time', $settings['time']['check_out_time'] ?? $settings['schedule']['check_out_time'] ?? '15:00')"
                            helper="Jam resmi pulang — untuk menandai pulang cepat/tepat waktu"
                            required
                        />
                        <x-input
                            type="time"
                            name="settings[check_out_start_time]"
                            label="⏱️ Jam Mulai Scanner Pulang"
                            :value="old('settings.check_out_start_time', $settings['time']['check_out_start_time'] ?? '12:00')"
                            helper="Scanner otomatis beralih ke mode PULANG mulai jam ini"
                            required
                        />
                        <x-input
                            type="number"
                            name="settings[tolerance_minutes]"
                            label="Toleransi Keterlambatan (menit)"
                            :value="old('settings.tolerance_minutes', $settings['tolerance']['tolerance_minutes'] ?? $settings['schedule']['tolerance_minutes'] ?? '15')"
                            min="0"
                            max="60"
                            helper="Siswa dianggap terlambat jika melewati toleransi ini"
                            required
                        />
                        <x-input
                            type="time"
                            name="settings[cutoff_time]"
                            label="Batas Waktu Alpha"
                            :value="old('settings.cutoff_time', $settings['time']['cutoff_time'] ?? $settings['schedule']['cutoff_time'] ?? '09:00')"
                            helper="Siswa otomatis alpha jika belum absen sampai jam ini"
                            required
                        />
                        <x-input
                            type="number"
                            name="settings[modal_auto_close]"
                            label="⏱️ Popup Auto-tutup (detik)"
                            :value="old('settings.modal_auto_close', $settings['general']['modal_auto_close'] ?? $settings['time']['modal_auto_close'] ?? '3')"
                            min="1"
                            max="10"
                            helper="Popup scan otomatis tutup setelah X detik (1–10)"
                        />
                    </div>

                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded">
                        <h4 class="font-semibold text-blue-900 dark:text-blue-300 mb-3">💡 Contoh Timeline:</h4>
                        <div class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                            <p>• <strong>07:00 - 07:15:</strong> Siswa dianggap <span class="text-green-600 dark:text-green-400 font-semibold">✅ Hadir</span></p>
                            <p>• <strong>07:16 - 09:00:</strong> Siswa dianggap <span class="text-yellow-600 dark:text-yellow-400 font-semibold">⏰ Terlambat</span></p>
                            <p>• <strong>Setelah 09:00:</strong> Siswa otomatis <span class="text-red-600 dark:text-red-400 font-semibold">❌ Alpha</span></p>
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Setting Waktu
                        </button>
                    </div>
                </div>
            </x-card>
        </form>
    </div>
</x-app-layout>
