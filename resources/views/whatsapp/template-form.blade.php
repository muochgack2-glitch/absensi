<x-app-layout>
    <x-slot name="title">{{ $template ? 'Edit Template' : 'Buat Template' }}</x-slot>
    <x-slot name="pageTitle">{{ $template ? 'Edit Template' : 'Buat Template Baru' }}</x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $template ? '✏️ Edit Template' : '➕ Buat Template' }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $template ? 'Ubah template pesan' : 'Buat template pesan baru untuk notifikasi' }}</p>
            </div>
            <a href="{{ route('whatsapp.templates') }}" class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <x-card>
            <form action="{{ $template ? route('whatsapp.templates.update', $template->id) : route('whatsapp.templates.store') }}" method="POST" class="space-y-5">
                @csrf
                @if($template) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama (ID)</label>
                        <input type="text" name="name" required placeholder="check_in_notification"
                            value="{{ old('name', $template->name ?? '') }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Identifier unik (huruf kecil, underscore)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Label</label>
                        <input type="text" name="label" required placeholder="Notifikasi Check-In"
                            value="{{ old('label', $template->label ?? '') }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe</label>
                        <select name="type" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="check_in" {{ old('type', $template->type ?? '') === 'check_in' ? 'selected' : '' }}>Check-In</option>
                            <option value="check_out" {{ old('type', $template->type ?? '') === 'check_out' ? 'selected' : '' }}>Check-Out</option>
                            <option value="absent" {{ old('type', $template->type ?? '') === 'absent' ? 'selected' : '' }}>Alpha</option>
                            <option value="reminder" {{ old('type', $template->type ?? '') === 'reminder' ? 'selected' : '' }}>Pengingat</option>
                            <option value="custom" {{ old('type', $template->type ?? '') === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                        <input type="text" name="description" placeholder="Opsional"
                            value="{{ old('description', $template->description ?? '') }}"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Isi Pesan</label>
                    <textarea name="message" rows="8" required placeholder="Tulis pesan template..."
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 font-mono text-sm">{{ old('message', $template->message ?? '') }}</textarea>
                    <div class="mt-2 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                        <p class="text-xs font-medium text-blue-700 dark:text-blue-400 mb-1">Variabel yang tersedia:</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach(['nama', 'kelas', 'waktu', 'tanggal', 'status', 'terlambat', 'sekolah', 'pesan'] as $var)
                                <code class="px-2 py-0.5 text-xs rounded bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-300 cursor-pointer hover:bg-blue-200 dark:hover:bg-blue-700 transition"
                                    onclick="insertVar('{{ $var }}')">{!! '{' . $var . '}' !!}</code>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $template->is_active ?? true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="auto_send" value="1"
                            {{ old('auto_send', $template->auto_send ?? false) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-amber-600 focus:ring-amber-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Auto Send</span>
                    </label>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i>{{ $template ? 'Update' : 'Simpan' }}
                    </button>
                    <a href="{{ route('whatsapp.templates') }}" class="inline-flex items-center px-6 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Batal
                    </a>
                </div>
            </form>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function insertVar(varName) {
            const textarea = document.querySelector('textarea[name="message"]');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            textarea.value = text.substring(0, start) + '{' + varName + '}' + text.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + varName.length + 2;
            textarea.focus();
        }
    </script>
    @endpush
</x-app-layout>
