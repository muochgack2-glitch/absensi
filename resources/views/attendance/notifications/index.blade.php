<x-app-layout>
    <x-slot name="title">Notifikasi</x-slot>

    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Pusat Notifikasi</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Ringkasan semua hal yang membutuhkan perhatian Anda</p>
        </div>

        @php
            $pendingIzin = \App\Models\AttendanceIzin::with('student')->where('status', 'pending')->orderByDesc('created_at')->get();
            
            $todayAlpha = \App\Models\AttendanceRecord::with('student')
                ->whereDate('date', today())
                ->where('status', 'alpha')
                ->get();

            $waFailed = \App\Models\WhatsAppLog::where('status', 'failed')
                ->whereDate('created_at', today())
                ->count();
        @endphp

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Izin Pending --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-yellow-200 dark:border-yellow-800 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                        <i class="fas fa-envelope text-yellow-600 dark:text-yellow-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $pendingIzin->count() }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Izin Menunggu</div>
                    </div>
                </div>
                @if($pendingIzin->count() > 0)
                <a href="{{ route('attendance.izin.index') }}" class="text-sm text-yellow-600 dark:text-yellow-400 hover:underline font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> Kelola Izin
                </a>
                @endif
            </div>

            {{-- Alpha Hari Ini --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-red-200 dark:border-red-800 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-user-times text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $todayAlpha->count() }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Alpha Hari Ini</div>
                    </div>
                </div>
                @if($todayAlpha->count() > 0)
                <a href="{{ route('attendance.dashboard') }}" class="text-sm text-red-600 dark:text-red-400 hover:underline font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> Lihat Dashboard
                </a>
                @endif
            </div>

            {{-- WA Gagal --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-blue-200 dark:border-blue-800 p-5 shadow-sm">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                        <i class="fab fa-whatsapp text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $waFailed }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">WA Gagal Kirim</div>
                    </div>
                </div>
                @if($waFailed > 0)
                <a href="{{ route('whatsapp.logs') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium">
                    <i class="fas fa-arrow-right mr-1"></i> Lihat Log WA
                </a>
                @endif
            </div>
        </div>

        {{-- Izin Pending Detail --}}
        @if($pendingIzin->count() > 0)
        <x-card>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-envelope-open-text mr-2 text-yellow-500"></i>
                Izin Menunggu Persetujuan
            </h3>
            <div class="space-y-3">
                @foreach($pendingIzin as $izin)
                <div class="flex items-center justify-between p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-800">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white font-bold text-sm">
                            {{ substr($izin->student->nama ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white">{{ $izin->student->nama ?? '-' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ ucfirst($izin->jenis) }} • {{ $izin->tanggal_mulai->format('d M') }}
                                @if($izin->tanggal_mulai != $izin->tanggal_selesai)
                                    - {{ $izin->tanggal_selesai->format('d M') }}
                                @endif
                                • {{ $izin->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('attendance.izin.approve', $izin->id) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-500 hover:bg-green-600 text-white transition-colors">
                                <i class="fas fa-check mr-1"></i> Setujui
                            </button>
                        </form>
                        <form method="POST" action="{{ route('attendance.izin.reject', $izin->id) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-500 hover:bg-red-600 text-white transition-colors">
                                <i class="fas fa-times mr-1"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </x-card>
        @endif

        {{-- Alpha Hari Ini Detail --}}
        @if($todayAlpha->count() > 0)
        <x-card>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                <i class="fas fa-user-times mr-2 text-red-500"></i>
                Siswa Alpha Hari Ini ({{ now()->translatedFormat('l, d M Y') }})
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($todayAlpha as $record)
                <div class="flex items-center gap-3 p-3 rounded-lg bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-800">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white font-bold text-xs">
                        {{ substr($record->student->nama ?? '?', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $record->student->nama ?? '-' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $record->student->kelas->nama_kelas ?? '-' }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </x-card>
        @endif

        {{-- Semua Aman --}}
        @if($pendingIzin->count() == 0 && $todayAlpha->count() == 0 && $waFailed == 0)
        <x-card>
            <div class="text-center py-8">
                <div class="w-16 h-16 rounded-full bg-green-100 dark:bg-green-900/20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-3xl text-green-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Semua Aman! 🎉</h3>
                <p class="text-gray-500 dark:text-gray-400">Tidak ada notifikasi yang memerlukan perhatian Anda saat ini.</p>
            </div>
        </x-card>
        @endif
    </div>
</x-app-layout>
