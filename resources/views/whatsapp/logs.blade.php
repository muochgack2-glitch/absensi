<x-app-layout>
    <x-slot name="title">Log Pesan</x-slot>
    <x-slot name="pageTitle">Log Pesan WhatsApp</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📋 Log Pesan</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Riwayat semua pesan WhatsApp yang dikirim</p>
            </div>
            <a href="{{ route('whatsapp.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        {{-- Filters --}}
        <x-card>
            <form method="GET" action="{{ route('whatsapp.logs') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no HP / pesan..."
                    class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <select name="status" class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Semua Status</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Terkirim</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
                <select name="type" class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Semua Tipe</option>
                    <option value="check_in" {{ request('type') === 'check_in' ? 'selected' : '' }}>Check-In</option>
                    <option value="check_out" {{ request('type') === 'check_out' ? 'selected' : '' }}>Check-Out</option>
                    <option value="absent" {{ request('type') === 'absent' ? 'selected' : '' }}>Alpha</option>
                    <option value="manual" {{ request('type') === 'manual' ? 'selected' : '' }}>Manual</option>
                    <option value="broadcast" {{ request('type') === 'broadcast' ? 'selected' : '' }}>Broadcast</option>
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <button type="submit" class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-search mr-1.5"></i>Filter
                </button>
            </form>
        </x-card>

        {{-- Logs Table --}}
        <x-card>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-3 text-gray-600 dark:text-gray-400 font-medium hidden sm:table-cell">No HP</th>
                            <th class="text-left py-3 px-3 text-gray-600 dark:text-gray-400 font-medium">Siswa</th>
                            <th class="text-left py-3 px-3 text-gray-600 dark:text-gray-400 font-medium hidden md:table-cell">Pesan</th>
                            <th class="text-left py-3 px-3 text-gray-600 dark:text-gray-400 font-medium">Tipe</th>
                            <th class="text-left py-3 px-3 text-gray-600 dark:text-gray-400 font-medium">Status</th>
                            <th class="text-left py-3 px-3 text-gray-600 dark:text-gray-400 font-medium">Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr class="border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="py-3 px-3 font-mono text-xs text-gray-900 dark:text-white hidden sm:table-cell">{{ $log->phone }}</td>
                            <td class="py-3 px-3 text-gray-600 dark:text-gray-400">{{ $log->student->nama ?? '-' }}</td>
                            <td class="py-3 px-3 text-gray-600 dark:text-gray-400 max-w-xs hidden md:table-cell">
                                <span class="truncate block" title="{{ $log->message }}">{{ Str::limit($log->message, 60) }}</span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                                    {{ $log->type === 'check_in' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' :
                                       ($log->type === 'check_out' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                                       ($log->type === 'absent' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
                                       'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400')) }}">
                                    {{ $log->type_label }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full
                                    {{ $log->status === 'sent' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                                       ($log->status === 'failed' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
                                       'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400') }}">
                                    <i class="fas {{ $log->status === 'sent' ? 'fa-check' : ($log->status === 'failed' ? 'fa-times' : 'fa-clock') }} text-[10px]"></i>
                                    {{ $log->status_label }}
                                </span>
                                @if($log->error_message)
                                    <p class="text-xs text-red-500 mt-1">{{ Str::limit($log->error_message, 40) }}</p>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap">{{ $log->created_at->format('d/m H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 dark:text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3"></i>
                                <p>Belum ada log pesan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="mt-4">{{ $logs->links() }}</div>
            @endif
        </x-card>
    </div>
</x-app-layout>
