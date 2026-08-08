<x-app-layout>
    <x-slot name="title">Settings WA</x-slot>
    <x-slot name="pageTitle">Pengaturan WhatsApp Gateway</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">⚙️ Pengaturan Gateway</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Konfigurasi WhatsApp Gateway server</p>
            </div>
            <a href="{{ route('whatsapp.index') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 text-green-700 dark:text-green-400">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <form action="{{ route('whatsapp.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            @foreach($groups as $group => $items)
            <x-card>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center
                        {{ $group === 'general' ? 'bg-blue-100 dark:bg-blue-900/30' :
                           ($group === 'connection' ? 'bg-cyan-100 dark:bg-cyan-900/30' :
                           ($group === 'notification' ? 'bg-amber-100 dark:bg-amber-900/30' :
                           'bg-red-100 dark:bg-red-900/30')) }}">
                        <i class="fas {{ $group === 'general' ? 'fa-cog text-blue-600 dark:text-blue-400' :
                           ($group === 'connection' ? 'fa-plug text-cyan-600 dark:text-cyan-400' :
                           ($group === 'notification' ? 'fa-bell text-amber-600 dark:text-amber-400' :
                           'fa-tools text-red-600 dark:text-red-400')) }}"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $items->first()->group_label }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $group === 'general' ? 'Pengaturan umum gateway' : ($group === 'connection' ? 'Pengaturan koneksi & failover' : ($group === 'notification' ? 'Pengaturan rate limit & delay' : 'Pengaturan lanjutan')) }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    @foreach($items as $setting)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 items-start">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $setting->label }}</label>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                        </div>
                        <div class="md:col-span-2">
                            @if($setting->type === 'boolean')
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="hidden" name="settings[{{ $setting->key }}]" value="false">
                                    <input type="checkbox" name="settings[{{ $setting->key }}]" value="true"
                                        {{ filter_var($setting->value, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            @elseif($setting->type === 'integer')
                                <input type="number" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                    class="w-full px-4 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </x-card>
            @endforeach

            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
            </button>
        </form>
    </div>
</x-app-layout>
