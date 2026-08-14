<x-app-layout>
    <x-slot name="title">Hari Libur</x-slot>
    <x-slot name="pageTitle">Manajemen Hari Libur</x-slot>

    <div class="space-y-6">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-800 dark:text-green-300">
            <i class="fas fa-check-circle text-xl text-green-500"></i>
            <div>
                <p class="font-semibold text-sm">Berhasil!</p>
                <p class="text-xs mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="flex items-center gap-3 px-5 py-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-800 dark:text-red-300">
            <i class="fas fa-exclamation-circle text-xl text-red-500"></i>
            <div>
                <p class="font-semibold text-sm">Gagal!</p>
                <p class="text-xs mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- Header Card --}}
        <x-card>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center text-white text-2xl shadow-lg">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Hari Libur</p>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $holidays->where('is_active', true)->count() }} hari</h2>
                        @if($lastSync)
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                <i class="fas fa-sync-alt mr-1"></i>
                                Terakhir sync: {{ \Carbon\Carbon::parse($lastSync)->diffForHumans() }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="flex gap-2 flex-wrap">
                    {{-- Sync Button --}}
                    <form action="{{ route('holidays.sync') }}" method="POST" id="syncForm">
                        @csrf
                        <button type="submit" id="syncBtn"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-semibold text-sm transition-all shadow-lg hover:shadow-xl">
                            <i class="fas fa-sync-alt" id="syncIcon"></i>
                            Sinkron dari E-Kaldik
                        </button>
                    </form>
                    {{-- Manual Add Button --}}
                    <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-semibold text-sm transition-all shadow">
                        <i class="fas fa-plus"></i>
                        Tambah Manual
                    </button>
                </div>
            </div>
        </x-card>

        {{-- Holiday Today Banner --}}
        @php $todayHoliday = $holidays->first(fn($h) => $h->is_active && $h->start_date <= now() && $h->end_date >= now()); @endphp
        @if($todayHoliday)
        <div class="flex items-center gap-3 px-5 py-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-300">
            <i class="fas fa-exclamation-triangle text-xl text-amber-500"></i>
            <div>
                <p class="font-semibold text-sm">Hari Ini Libur!</p>
                <p class="text-xs mt-0.5">{{ $todayHoliday->name }} — Scan absensi dinonaktifkan otomatis</p>
            </div>
        </div>
        @endif

        {{-- Table --}}
        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">#</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Nama</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Tanggal</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Durasi</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Jenis</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Sumber</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Status</th>
                            <th class="text-center py-3 px-4 font-semibold text-gray-600 dark:text-gray-300">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($holidays as $index => $holiday)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors {{ !$holiday->is_active ? 'opacity-50' : '' }}">
                            <td class="py-3 px-4 text-gray-500">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $holiday->name }}</p>
                                    @if($holiday->description)
                                        <p class="text-xs text-gray-500 mt-0.5 truncate max-w-[200px]">{{ $holiday->description }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300 whitespace-nowrap">
                                @if($holiday->start_date->equalTo($holiday->end_date))
                                    {{ $holiday->start_date->translatedFormat('d M Y') }}
                                @else
                                    {{ $holiday->start_date->translatedFormat('d M') }} - {{ $holiday->end_date->translatedFormat('d M Y') }}
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                {{ $holiday->duration }} hari
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $holiday->type ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($holiday->source === 'ekaldik')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                        <i class="fas fa-link text-[10px]"></i> E-Kaldik
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300">
                                        <i class="fas fa-pen text-[10px]"></i> Manual
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($holiday->isOngoing())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 animate-pulse">
                                        <i class="fas fa-circle text-[6px]"></i> Berlangsung
                                    </span>
                                @elseif($holiday->start_date->isFuture())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                        <i class="fas fa-clock text-[10px]"></i> Mendatang
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                        <i class="fas fa-check text-[10px]"></i> Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <form action="{{ route('holidays.destroy', $holiday) }}" method="POST"
                                      onsubmit="return confirm('Hapus hari libur {{ $holiday->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs text-red-600 hover:text-white hover:bg-red-500 border border-red-200 hover:border-red-500 rounded-lg transition-all">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-calendar-times text-4xl mb-3 text-gray-300 dark:text-gray-600"></i>
                                <p class="font-medium">Belum ada hari libur</p>
                                <p class="text-xs mt-1">Klik "Sinkron dari E-Kaldik" atau "Tambah Manual"</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    {{-- Modal Tambah Manual --}}
    <div id="modalTambah" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-plus-circle text-green-500 mr-2"></i>Tambah Hari Libur
                </h3>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form action="{{ route('holidays.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Libur <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="contoh: Libur HUT RI"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" required
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" required
                               class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis</label>
                    <input type="text" name="type" placeholder="contoh: Hari Libur Nasional"
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                    <textarea name="description" rows="2" placeholder="Opsional..."
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-xl shadow transition-all">
                        <i class="fas fa-save mr-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Sync button loading state
        document.getElementById('syncForm')?.addEventListener('submit', function() {
            const btn = document.getElementById('syncBtn');
            const icon = document.getElementById('syncIcon');
            btn.disabled = true;
            btn.classList.add('opacity-75');
            icon.classList.add('fa-spin');
            btn.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> Menyinkronkan...';
        });
    </script>
    @endpush
</x-app-layout>
