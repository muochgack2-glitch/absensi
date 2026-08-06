<x-app-layout>
    <x-slot name="title">Broadcast</x-slot>
    <x-slot name="pageTitle">Broadcast WhatsApp</x-slot>

    <div class="max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📢 Broadcast</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Kirim pesan massal ke orang tua siswa</p>
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
        @if($errors->any())
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 text-red-700 dark:text-red-400">
                <i class="fas fa-times-circle mr-2"></i>{{ $errors->first() }}
            </div>
        @endif

        {{-- Warning --}}
        <div class="rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 p-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-600 dark:text-amber-400 mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-300">Perhatian</p>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">Broadcast akan mengirim pesan ke semua orang tua siswa yang memiliki nomor HP. Proses ini memerlukan waktu dan tidak bisa dibatalkan.</p>
                </div>
            </div>
        </div>

        <x-card>
            <form action="{{ route('whatsapp.broadcast.submit') }}" method="POST" class="space-y-5"
                  onsubmit="return confirm('Kirim broadcast ke semua orang tua? Proses ini tidak bisa dibatalkan.')">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Kelas</label>
                    <select name="class_id"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->nama_kelas }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pilih kelas tertentu atau kosongkan untuk semua kelas</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Template (Opsional)</label>
                    <select id="broadcastTemplate" onchange="applyTemplate()"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Tulis Manual --</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}" data-message="{{ $template->message }}">{{ $template->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pesan Broadcast</label>
                    <textarea name="message" id="broadcastMessage" rows="8" required placeholder="Tulis pesan broadcast..."
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('message') }}</textarea>
                </div>

                <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-amber-600 text-white font-medium rounded-lg hover:bg-amber-700 transition">
                    <i class="fas fa-bullhorn mr-2"></i>Kirim Broadcast
                </button>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function applyTemplate() {
            const select = document.getElementById('broadcastTemplate');
            const option = select.options[select.selectedIndex];
            if (option.dataset.message) {
                document.getElementById('broadcastMessage').value = option.dataset.message;
            }
        }
    </script>
    @endpush
</x-app-layout>
