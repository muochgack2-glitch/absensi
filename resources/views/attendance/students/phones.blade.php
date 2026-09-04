@php
    $pageTitle = 'Update Nomor HP Orang Tua';
    $breadcrumbs = [
        ['label' => 'Data Siswa', 'url' => route('attendance.students.index')],
        ['label' => 'Update No HP']
    ];
@endphp

<x-app-layout>
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📱 Update Nomor HP Orang Tua</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Edit nomor HP ortu/wali siswa per kelas secara cepat</p>
            </div>
            <a href="{{ route('attendance.students.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition text-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Success Flash --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-xl text-green-800 dark:text-green-300">
                <i class="fas fa-check-circle text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Filter Kelas --}}
        <x-card>
            <form method="GET" action="{{ route('attendance.students.phones') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Pilih Kelas
                    </label>
                    <select name="kelas_id" id="kelas_id"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($classes as $kelas)
                            <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                    <i class="fas fa-filter"></i> Tampilkan
                </button>
            </form>
        </x-card>

        {{-- Tabel Edit HP --}}
        @if($kelasId && $students->count() > 0)
        <form method="POST" action="{{ route('attendance.students.phones.save') }}" id="phoneForm">
            @csrf
            <input type="hidden" name="kelas_id" value="{{ $kelasId }}">

            <x-card>
                {{-- Header tabel --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h2 class="font-bold text-gray-900 dark:text-white text-lg">
                            {{ $classes->find($kelasId)?->nama_kelas ?? '' }}
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $students->count() }} siswa aktif</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" id="btnFillFormat"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2"
                                title="Format semua nomor ke 628xxx">
                            <i class="fas fa-magic"></i> Format Otomatis
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition flex items-center gap-2">
                            <i class="fas fa-save"></i> Simpan Semua
                        </button>
                    </div>
                </div>

                {{-- Tips --}}
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg text-sm text-blue-700 dark:text-blue-300">
                    <i class="fas fa-info-circle mr-1"></i>
                    Format nomor: <strong>628XXXXXXXXX</strong> (tanpa tanda + atau spasi).
                    Contoh: <code class="bg-blue-100 dark:bg-blue-800 px-1 rounded">6281234567890</code>
                    &nbsp;|&nbsp; No HP 2 opsional (jika ada 2 nomor ortu/wali).
                </div>

                {{-- Tabel --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-700">
                                <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-8">#</th>
                                <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nama Siswa</th>
                                <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-24">NIS</th>
                                <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">No HP Ortu (Utama)</th>
                                <th class="py-3 px-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">No HP Ortu 2 (Alternatif)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @foreach($students as $i => $student)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors group">
                                <td class="py-2.5 px-3 text-gray-400 dark:text-gray-500 text-xs">{{ $i + 1 }}</td>
                                <td class="py-2.5 px-3">
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $student->nama }}</span>
                                </td>
                                <td class="py-2.5 px-3 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $student->nis }}</td>
                                <td class="py-2.5 px-3">
                                    <input type="text"
                                           name="phones[{{ $student->id }}][no_hp_ortu]"
                                           value="{{ $student->no_hp_ortu }}"
                                           placeholder="628..."
                                           class="phone-input w-full px-3 py-1.5 text-sm border rounded-lg
                                                  bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                                  border-gray-300 dark:border-gray-600
                                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                                  transition font-mono
                                                  {{ $student->no_hp_ortu ? 'border-green-300 dark:border-green-700' : 'border-red-300 dark:border-red-700' }}"
                                           data-original="{{ $student->no_hp_ortu }}"
                                           oninput="markDirty(this)">
                                </td>
                                <td class="py-2.5 px-3">
                                    <input type="text"
                                           name="phones[{{ $student->id }}][no_hp_ortu2]"
                                           value="{{ $student->no_hp_ortu2 }}"
                                           placeholder="628... (opsional)"
                                           class="phone-input w-full px-3 py-1.5 text-sm border rounded-lg
                                                  bg-white dark:bg-gray-800 text-gray-900 dark:text-white
                                                  border-gray-300 dark:border-gray-600
                                                  focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                                  transition font-mono"
                                           data-original="{{ $student->no_hp_ortu2 }}"
                                           oninput="markDirty(this)">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer simpan --}}
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400" id="dirtyCounter">Belum ada perubahan</p>
                    <button type="submit"
                            class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition flex items-center gap-2 shadow-sm">
                        <i class="fas fa-save"></i> Simpan Semua Perubahan
                    </button>
                </div>
            </x-card>
        </form>

        @elseif($kelasId && $students->count() === 0)
            <x-card>
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <i class="fas fa-users text-3xl mb-2 opacity-40"></i>
                    <p>Tidak ada siswa aktif di kelas ini.</p>
                </div>
            </x-card>
        @elseif(!$kelasId)
            <x-card>
                <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                    <i class="fas fa-hand-pointer text-4xl mb-3 opacity-40"></i>
                    <p class="text-base font-medium">Pilih kelas di atas untuk menampilkan daftar siswa</p>
                </div>
            </x-card>
        @endif

    </div>

    @push('scripts')
    <script>
        let dirtyCount = 0;

        function markDirty(input) {
            const original = input.dataset.original ?? '';
            const isDirty  = input.value !== original;

            if (isDirty) {
                input.classList.add('border-yellow-400', 'dark:border-yellow-500');
                input.classList.remove('border-gray-300', 'dark:border-gray-600',
                                       'border-green-300', 'dark:border-green-700',
                                       'border-red-300', 'dark:border-red-700');
            } else {
                // restore
                input.classList.remove('border-yellow-400', 'dark:border-yellow-500');
                if (original) {
                    input.classList.add('border-green-300', 'dark:border-green-700');
                } else {
                    input.classList.add('border-gray-300', 'dark:border-gray-600');
                }
            }
            updateDirtyCounter();
        }

        function updateDirtyCounter() {
            const changed = Array.from(document.querySelectorAll('.phone-input'))
                .filter(i => i.value !== (i.dataset.original ?? '')).length;
            const el = document.getElementById('dirtyCounter');
            if (el) {
                el.textContent = changed > 0
                    ? `${changed} field diubah — jangan lupa simpan!`
                    : 'Belum ada perubahan';
                el.className = changed > 0
                    ? 'text-xs text-yellow-600 dark:text-yellow-400 font-medium'
                    : 'text-xs text-gray-500 dark:text-gray-400';
            }
        }

        // Format otomatis: 08xxx → 628xxx
        document.getElementById('btnFillFormat')?.addEventListener('click', function () {
            document.querySelectorAll('.phone-input').forEach(function (input) {
                let val = input.value.trim().replace(/\D/g, '');
                if (!val) return;
                if (val.startsWith('0'))       val = '62' + val.slice(1);
                else if (val.startsWith('8'))  val = '62' + val;
                input.value = val;
                markDirty(input);
            });
        });
    </script>
    @endpush
</x-app-layout>
