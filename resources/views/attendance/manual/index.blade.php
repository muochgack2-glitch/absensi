<x-app-layout>
    <x-slot name="title">Input Absensi Manual</x-slot>
    <x-slot name="pageTitle">Input Absensi Manual</x-slot>

    <div class="space-y-6">

        {{-- Flash --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 text-sm">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📝 Input Absensi Manual</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Tandai hadir / izin / sakit / alpha langsung dari admin — tanpa QR scan</p>
            </div>
            <a href="{{ route('attendance.dashboard') }}"
               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Dashboard
            </a>
        </div>

        {{-- Filter: Tanggal & Kelas --}}
        <x-card>
            <form method="GET" action="{{ route('attendance.manual.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4" id="filterForm">
                <x-input
                    type="date"
                    name="date"
                    label="Tanggal"
                    :value="$date"
                    max="{{ now()->format('Y-m-d') }}"
                    onchange="this.form.submit()"
                />
                <x-select name="class_id" label="Kelas" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                            {{ $class->nama_kelas }}
                        </option>
                    @endforeach
                </x-select>
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 shadow-md transition-all">
                        <i class="fas fa-filter mr-2"></i> Tampilkan
                    </button>
                </div>
            </form>
        </x-card>

        {{-- Tabel Input Absensi --}}
        @if($classId && $students->isNotEmpty())
            <form method="POST" action="{{ route('attendance.manual.store') }}" id="manualForm">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="class_id" value="{{ $classId }}">

                <x-card>
                    {{-- Info header --}}
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white">
                                    {{ $classes->find($classId)?->nama_kelas ?? '' }} —
                                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $students->count() }} siswa aktif</p>
                            </div>
                        </div>

                        {{-- Quick fill buttons --}}
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs text-gray-500 dark:text-gray-400 mr-1">Isi semua:</span>
                            @foreach(['hadir'=>['green','Hadir'], 'izin'=>['blue','Izin'], 'sakit'=>['purple','Sakit'], 'alpha'=>['red','Alpha']] as $val => [$color, $label])
                                <button type="button"
                                        onclick="fillAll('{{ $val }}')"
                                        class="px-3 py-1.5 text-xs font-medium rounded-lg border transition-all
                                            {{ $color === 'green'  ? 'border-green-300 bg-green-50 text-green-700 hover:bg-green-100 dark:border-green-700 dark:bg-green-900/20 dark:text-green-400' : '' }}
                                            {{ $color === 'blue'   ? 'border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100 dark:border-blue-700 dark:bg-blue-900/20 dark:text-blue-400' : '' }}
                                            {{ $color === 'purple' ? 'border-purple-300 bg-purple-50 text-purple-700 hover:bg-purple-100 dark:border-purple-700 dark:bg-purple-900/20 dark:text-purple-400' : '' }}
                                            {{ $color === 'red'    ? 'border-red-300 bg-red-50 text-red-700 hover:bg-red-100 dark:border-red-700 dark:bg-red-900/20 dark:text-red-400' : '' }}">
                                    {{ $label }} Semua
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tabel --}}
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-8">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Nama Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-28">NIS</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-44">Status Kehadiran</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-28">Jam Masuk</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Keterangan</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($students as $i => $student)
                                    @php
                                        $existing = $records->get($student->id);
                                        $hasRecord = !is_null($existing);
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors {{ $hasRecord ? 'bg-blue-50/30 dark:bg-blue-900/10' : '' }}"
                                        id="row_{{ $student->id }}">
                                        <td class="px-4 py-3 text-gray-400 dark:text-gray-500 text-xs">{{ $i + 1 }}</td>

                                        {{-- Nama --}}
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                                    {{ strtoupper(substr($student->nama, 0, 1)) }}
                                                </div>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $student->nama }}</span>
                                            </div>
                                        </td>

                                        {{-- NIS --}}
                                        <td class="px-4 py-3 font-mono text-gray-500 dark:text-gray-400 text-xs">{{ $student->nis }}</td>

                                        {{-- Status radio --}}
                                        <td class="px-4 py-3">
                                            <input type="hidden" name="entries[{{ $i }}][student_id]" value="{{ $student->id }}">
                                            <div class="flex gap-1 justify-center flex-wrap">
                                                @php
                                                    $currentStatus = $existing?->status ?? 'skip';
                                                    $statusOpts = [
                                                        'hadir'     => ['bg-green-500', 'H'],
                                                        'terlambat' => ['bg-yellow-500', 'T'],
                                                        'izin'      => ['bg-blue-500', 'I'],
                                                        'sakit'     => ['bg-purple-500', 'S'],
                                                        'alpha'     => ['bg-red-500', 'A'],
                                                        'skip'      => ['bg-gray-300 dark:bg-gray-600', '—'],
                                                    ];
                                                @endphp
                                                @foreach($statusOpts as $val => [$bg, $lbl])
                                                    <label class="relative cursor-pointer" title="{{ ucfirst($val) }}">
                                                        <input type="radio"
                                                               name="entries[{{ $i }}][status]"
                                                               value="{{ $val }}"
                                                               class="sr-only status-radio"
                                                               data-row="{{ $student->id }}"
                                                               {{ $currentStatus === $val ? 'checked' : '' }}
                                                               onchange="updateRowStyle('{{ $student->id }}', '{{ $val }}')">
                                                        <span class="flex items-center justify-center w-8 h-8 rounded-full text-white text-xs font-bold transition-all ring-2 ring-transparent peer-checked:ring-gray-800
                                                            {{ $bg }} {{ $currentStatus === $val ? 'ring-2 ring-offset-1 ring-gray-700 dark:ring-gray-200 scale-110' : 'opacity-40 hover:opacity-100' }}"
                                                              id="badge_{{ $student->id }}_{{ $val }}">
                                                            {{ $lbl }}
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </td>

                                        {{-- Jam masuk --}}
                                        <td class="px-4 py-3">
                                            <input type="time"
                                                   name="entries[{{ $i }}][check_in_time]"
                                                   value="{{ $existing?->check_in_time ? \Carbon\Carbon::parse($existing->check_in_time)->format('H:i') : '' }}"
                                                   class="w-full text-xs px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-400">
                                        </td>

                                        {{-- Keterangan --}}
                                        <td class="px-4 py-3">
                                            <input type="text"
                                                   name="entries[{{ $i }}][notes]"
                                                   value="{{ $existing?->notes ?? '' }}"
                                                   placeholder="Keterangan opsional..."
                                                   class="w-full text-xs px-2 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-primary-400">
                                        </td>

                                        {{-- Hapus record (jika ada) --}}
                                        <td class="px-4 py-3 text-center">
                                            @if($hasRecord)
                                                <form action="{{ route('attendance.manual.destroy', $existing->id) }}" method="POST" class="inline"
                                                      onsubmit="return confirm('Hapus record absensi {{ $student->nama }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center px-2 py-1.5 text-xs rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 transition-all"
                                                            title="Hapus record">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Legend --}}
                    <div class="flex items-center gap-4 mt-3 text-xs text-gray-500 dark:text-gray-400 flex-wrap">
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-green-500 inline-block"></span> H = Hadir</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-yellow-500 inline-block"></span> T = Terlambat</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-blue-500 inline-block"></span> I = Izin</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-purple-500 inline-block"></span> S = Sakit</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-red-500 inline-block"></span> A = Alpha</span>
                        <span class="flex items-center gap-1"><span class="w-5 h-5 rounded-full bg-gray-300 dark:bg-gray-600 inline-block"></span> — = Tidak diubah</span>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-between mt-5 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Status <strong>—</strong> berarti baris tersebut tidak akan diubah.
                            Record yang sudah ada akan diperbarui (Update).
                        </p>
                        <button type="submit"
                                class="inline-flex items-center px-8 py-3 text-sm font-semibold rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 text-white hover:from-indigo-600 hover:to-purple-700 shadow-lg hover:shadow-xl transition-all">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Absensi
                        </button>
                    </div>
                </x-card>
            </form>

        @elseif($classId && $students->isEmpty())
            <x-card>
                <div class="text-center py-12 text-gray-400 dark:text-gray-600">
                    <i class="fas fa-users text-4xl mb-3"></i>
                    <p class="font-medium">Tidak ada siswa aktif di kelas ini.</p>
                </div>
            </x-card>

        @else
            {{-- Belum pilih kelas --}}
            <x-card>
                <div class="text-center py-14">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 flex items-center justify-center text-4xl mx-auto mb-4">
                        📝
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-300 mb-2">Pilih Tanggal & Kelas</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 max-w-xs mx-auto">
                        Pilih tanggal dan kelas di atas untuk menampilkan daftar siswa yang bisa diisi absensinya secara manual.
                    </p>
                </div>
            </x-card>
        @endif

    </div>

    @push('scripts')
    <script>
        // ===== Style update saat radio dipilih =====
        const allStatuses = ['hadir','terlambat','izin','sakit','alpha','skip'];
        const statusBg = {
            hadir:     'bg-green-500',
            terlambat: 'bg-yellow-500',
            izin:      'bg-blue-500',
            sakit:     'bg-purple-500',
            alpha:     'bg-red-500',
            skip:      'bg-gray-300 dark:bg-gray-600',
        };

        function updateRowStyle(studentId, selectedStatus) {
            allStatuses.forEach(s => {
                const badge = document.getElementById('badge_' + studentId + '_' + s);
                if (!badge) return;
                if (s === selectedStatus) {
                    badge.classList.remove('opacity-40');
                    badge.classList.add('ring-2', 'ring-offset-1', 'ring-gray-700', 'dark:ring-gray-200', 'scale-110');
                } else {
                    badge.classList.add('opacity-40');
                    badge.classList.remove('ring-2', 'ring-offset-1', 'ring-gray-700', 'dark:ring-gray-200', 'scale-110');
                }
            });
        }

        // ===== Isi semua baris dengan satu status =====
        function fillAll(status) {
            document.querySelectorAll('.status-radio[value="' + status + '"]').forEach(radio => {
                radio.checked = true;
                updateRowStyle(radio.dataset.row, status);
            });
        }
    </script>
    @endpush
</x-app-layout>
