<x-app-layout>
    <x-slot name="title">Log Pesan WhatsApp</x-slot>
    <x-slot name="pageTitle">Log Pesan WhatsApp</x-slot>

    <div class="space-y-5">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-lg shadow">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                    Log Pesan WhatsApp
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-12">Riwayat semua pesan yang dikirim sistem</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('whatsapp.logs') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <i class="fas fa-sync-alt text-xs"></i> Refresh
                </a>
                <a href="{{ route('whatsapp.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 flex-shrink-0">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white leading-none">{{ number_format((\$stats['total'] ?? 0)) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total Hari Ini</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400 flex-shrink-0">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 leading-none">{{ number_format((\$stats['sent'] ?? 0)) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Terkirim</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 flex-shrink-0">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 leading-none">{{ number_format((\$stats['failed'] ?? 0)) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Gagal</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex items-center gap-3 shadow-sm">
                <div class="w-10 h-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-yellow-600 dark:text-yellow-400 flex-shrink-0">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 leading-none">{{ number_format((\$stats['pending'] ?? 0)) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pending</p>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <x-card>
            <form method="GET" action="{{ route('whatsapp.logs') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    <div class="lg:col-span-2 relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari no HP atau isi pesan..."
                               class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <select name="status" class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Status</option>
                        <option value="sent"    {{ request('status') === 'sent'    ? 'selected' : '' }}>Terkirim</option>
                        <option value="failed"  {{ request('status') === 'failed'  ? 'selected' : '' }}>Gagal</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                    <select name="type" class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        <option value="">Semua Tipe</option>
                        <option value="check_in"  {{ request('type') === 'check_in'  ? 'selected' : '' }}>Check-In</option>
                        <option value="check_out" {{ request('type') === 'check_out' ? 'selected' : '' }}>Check-Out</option>
                        <option value="absent"    {{ request('type') === 'absent'    ? 'selected' : '' }}>Alpha</option>
                        <option value="broadcast" {{ request('type') === 'broadcast' ? 'selected' : '' }}>Broadcast</option>
                        <option value="manual"    {{ request('type') === 'manual'    ? 'selected' : '' }}>Manual</option>
                    </select>
                    <div class="relative">
                        <i class="fas fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                        <input type="date" name="date_from" value="{{ request('date_from') }}"
                               class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 text-sm bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition flex items-center justify-center gap-1.5 font-medium">
                            <i class="fas fa-search text-xs"></i> Filter
                        </button>
                        @if(request()->hasAny(['search','status','type','date_from']))
                        <a href="{{ route('whatsapp.logs') }}"
                           class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 transition" title="Reset filter">
                            <i class="fas fa-times"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </x-card>

        {{-- Log Table --}}
        <x-card>
            <div class="flex items-center justify-between mb-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Menampilkan <span class="font-semibold text-gray-900 dark:text-white">{{ $logs->firstItem() ?? 0 }}&ndash;{{ $logs->lastItem() ?? 0 }}</span>
                    dari <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($logs->total()) }}</span> log
                </p>
            </div>

            {{-- ===== DESKTOP TABLE ===== --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-gray-200 dark:border-gray-700 text-xs uppercase tracking-wide">
                            <th class="text-left py-3 px-3 text-gray-500 dark:text-gray-400 font-semibold w-8">#</th>
                            <th class="text-left py-3 px-3 text-gray-500 dark:text-gray-400 font-semibold">Penerima</th>
                            <th class="text-left py-3 px-3 text-gray-500 dark:text-gray-400 font-semibold">Pesan <span class="normal-case font-normal text-gray-400">(klik detail)</span></th>
                            <th class="text-left py-3 px-3 text-gray-500 dark:text-gray-400 font-semibold w-28">Tipe</th>
                            <th class="text-left py-3 px-3 text-gray-500 dark:text-gray-400 font-semibold w-36">Status</th>
                            <th class="text-left py-3 px-3 text-gray-500 dark:text-gray-400 font-semibold w-24">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($logs as $i => $log)
                        @php
                            $tc = match($log->type) {
                                'check_in'  => ['bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400', 'fa-sign-in-alt'],
                                'check_out' => ['bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400', 'fa-sign-out-alt'],
                                'absent'    => ['bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400', 'fa-user-times'],
                                'broadcast' => ['bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400', 'fa-bullhorn'],
                                'manual'    => ['bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300', 'fa-edit'],
                                default     => ['bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400', 'fa-bell'],
                            };
                            $sc = $log->status === 'sent'
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'
                                : ($log->status === 'failed'
                                    ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'
                                    : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400');
                            $si = $log->status === 'sent' ? 'fa-check' : ($log->status === 'failed' ? 'fa-times' : 'fa-clock');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                            <td class="py-3 px-3 text-gray-400 text-xs">{{ $logs->firstItem() + $i }}</td>
                            <td class="py-3 px-3">
                                <div class="flex items-center gap-2.5">
                                    @if($log->student)
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                            {{ mb_strtoupper(mb_substr($log->student->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white text-xs leading-tight">{{ $log->student->nama }}</p>
                                            <p class="font-mono text-gray-400 text-[11px]">{{ $log->phone }}</p>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-xs text-gray-500"></i>
                                        </div>
                                        <p class="font-mono text-gray-600 dark:text-gray-400 text-xs">{{ $log->phone }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-3 max-w-xs">
                                <button type="button"
                                        onclick="showMessage({{ json_encode($log->message) }})"
                                        class="text-left text-gray-600 dark:text-gray-400 text-xs hover:text-primary-600 dark:hover:text-primary-400 transition cursor-pointer group-hover:underline line-clamp-2">
                                    {{ Str::limit($log->message, 80) }}
                                </button>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $tc[0] }}">
                                    <i class="fas {{ $tc[1] }} text-[9px]"></i> {{ $log->type_label }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $sc }}">
                                    <i class="fas {{ $si }} text-[9px]"></i> {{ $log->status_label }}
                                </span>
                                @if($log->error_message)
                                    <p class="text-[10px] text-red-500 mt-0.5 max-w-[140px] truncate" title="{{ $log->error_message }}">
                                        {{ Str::limit($log->error_message, 35) }}
                                    </p>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap" title="{{ $log->created_at->format('d/m/Y H:i:s') }}">
                                {{ $log->created_at->format('d/m H:i') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-500">
                                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <i class="fas fa-inbox text-3xl opacity-50"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium">Belum ada log pesan</p>
                                        @if(request()->hasAny(['search','status','type','date_from']))
                                            <a href="{{ route('whatsapp.logs') }}" class="text-sm text-primary-600 hover:underline mt-1 block">Reset filter</a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===== MOBILE CARDS ===== --}}
            <div class="md:hidden space-y-3">
                @forelse($logs as $log)
                @php
                    $tc = match($log->type) {
                        'check_in'  => ['bg-blue-100 text-blue-700', 'fa-sign-in-alt'],
                        'check_out' => ['bg-green-100 text-green-700', 'fa-sign-out-alt'],
                        'absent'    => ['bg-red-100 text-red-700', 'fa-user-times'],
                        'broadcast' => ['bg-purple-100 text-purple-700', 'fa-bullhorn'],
                        'manual'    => ['bg-gray-100 text-gray-700', 'fa-edit'],
                        default     => ['bg-orange-100 text-orange-700', 'fa-bell'],
                    };
                    $borderColor = $log->status === 'sent' ? 'border-l-green-400' : ($log->status === 'failed' ? 'border-l-red-400' : 'border-l-yellow-400');
                    $scm = $log->status === 'sent' ? 'bg-green-100 text-green-700' : ($log->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                    $sim = $log->status === 'sent' ? 'fa-check' : ($log->status === 'failed' ? 'fa-times' : 'fa-clock');
                @endphp
                <div class="border border-gray-200 dark:border-gray-700 border-l-4 {{ $borderColor }} rounded-xl p-3.5">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="flex items-center gap-2 min-w-0">
                            @if($log->student)
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ mb_strtoupper(mb_substr($log->student->nama, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">{{ $log->student->nama }}</p>
                                    <p class="font-mono text-gray-400 text-[11px]">{{ $log->phone }}</p>
                                </div>
                            @else
                                <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user text-xs text-gray-500"></i>
                                </div>
                                <p class="font-mono text-gray-700 dark:text-gray-300 text-xs truncate">{{ $log->phone }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $scm }}">
                                <i class="fas {{ $sim }} text-[9px]"></i> {{ $log->status_label }}
                            </span>
                            <span class="text-[10px] text-gray-400">{{ $log->created_at->format('d/m H:i') }}</span>
                        </div>
                    </div>
                    <button onclick="showMessage({{ json_encode(Str::limit($log->message, 500)) }})"
                            class="text-xs text-gray-600 dark:text-gray-400 text-left line-clamp-2 mb-2 hover:text-primary-600 transition w-full">
                        {{ Str::limit($log->message, 100) }}
                    </button>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-full {{ $tc[0] }}">
                            <i class="fas {{ $tc[1] }} text-[9px]"></i> {{ $log->type_label }}
                        </span>
                        @if($log->error_message)
                            <p class="text-[10px] text-red-500 truncate max-w-[180px]" title="{{ $log->error_message }}">{{ Str::limit($log->error_message, 40) }}</p>
                        @endif
                    </div>
                </div>
                @empty
                <div class="py-12 text-center text-gray-400 dark:text-gray-500">
                    <i class="fas fa-inbox text-3xl mb-2 block opacity-50"></i>
                    <p>Belum ada log pesan</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($logs->hasPages())
                <div class="mt-5 pt-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $logs->links() }}
                </div>
            @endif
        </x-card>
    </div>

    {{-- Modal Isi Pesan --}}
    <div id="msgModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fab fa-whatsapp text-green-500 text-lg"></i> Isi Pesan Lengkap
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-5 py-4">
                <pre id="msgContent" class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-sans leading-relaxed bg-gray-50 dark:bg-gray-900 rounded-xl p-4 max-h-80 overflow-y-auto border border-gray-200 dark:border-gray-700"></pre>
            </div>
            <div class="px-5 pb-4 flex justify-end gap-2">
                <button onclick="copyMessage()" id="btnCopy" class="px-4 py-2 text-sm bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center gap-1.5">
                    <i class="fas fa-copy"></i> Salin
                </button>
                <button onclick="closeModal()" class="px-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let currentMessage = '';

        function showMessage(msg) {
            currentMessage = msg;
            document.getElementById('msgContent').textContent = msg;
            const modal = document.getElementById('msgModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal() {
            const modal = document.getElementById('msgModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        function copyMessage() {
            navigator.clipboard.writeText(currentMessage).then(() => {
                const btn = document.getElementById('btnCopy');
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Disalin!';
                setTimeout(() => btn.innerHTML = orig, 2000);
            });
        }

        document.getElementById('msgModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
    </script>
    @endpush
</x-app-layout>

