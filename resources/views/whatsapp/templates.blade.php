<x-app-layout>
    <x-slot name="title">Templates</x-slot>
    <x-slot name="pageTitle">Template Pesan WhatsApp</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📝 Template Pesan</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola template pesan notifikasi WhatsApp</p>
            </div>
            <a href="{{ route('whatsapp.templates.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i>Buat Template
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 text-green-700 dark:text-green-400">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($templates as $template)
            <x-card>
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $template->label }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $template->name }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full
                            {{ $template->type === 'check_in' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' :
                               ($template->type === 'check_out' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' :
                               ($template->type === 'absent' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' :
                               'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400')) }}">
                            {{ $template->type_label }}
                        </span>
                        @if($template->is_active)
                            <span class="w-2 h-2 rounded-full bg-green-500" title="Aktif"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-gray-400" title="Nonaktif"></span>
                        @endif
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 mb-3 text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line font-mono text-xs max-h-32 overflow-y-auto">{{ $template->message }}</div>
                
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-3">
                        <span><i class="fas fa-chart-bar mr-1"></i>{{ $template->usage_count }}x dipakai</span>
                        @if($template->auto_send)
                            <span class="text-amber-600 dark:text-amber-400"><i class="fas fa-bolt mr-1"></i>Auto</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('whatsapp.templates.edit', $template->id) }}" class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-blue-600 dark:text-blue-400 transition" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('whatsapp.templates.delete', $template->id) }}" method="POST" onsubmit="return confirm('Hapus template ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-600 text-red-600 dark:text-red-400 transition" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </x-card>
            @empty
            <div class="col-span-2 text-center py-12 text-gray-400 dark:text-gray-500">
                <i class="fas fa-file-alt text-4xl mb-3"></i>
                <p>Belum ada template. <a href="{{ route('whatsapp.templates.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Buat sekarang</a></p>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
