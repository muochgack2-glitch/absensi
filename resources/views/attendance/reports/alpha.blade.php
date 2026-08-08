<x-app-layout>
    <x-slot name="title">Laporan Alpha</x-slot>
    <x-slot name="pageTitle">Laporan Siswa Sering Alpha</x-slot>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">⚠️ Laporan Siswa Alpha</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Daftar siswa yang paling sering tidak hadir — kirim notifikasi WA ke orang tua langsung dari sini</p>
            </div>
            <a href="{{ route('attendance.reports.index') }}"
               class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300 text-sm">
                <i class="fas fa-check-circle text-green-500"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter --}}
        <x-card>
            <form method="GET" action="{{ route('attendance.reports.alpha') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-input type="month" name="month" label="Bulan" :value="$month" />

                <x-select name="class_id" label="Kelas">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                            {{ $class->nama_kelas }}
                        </option>
                    @endforeach
                </x-select>

                <x-input
                    type="number"
                    name="min_alpha"
                    label="Min. Alpha (hari)"
                    :value="$minAlpha"
                    min="1" max="31"
                    helper="Tampilkan siswa dengan alpha ≥ angka ini"
                />

                <div class="flex items-end">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-2.5 text-sm font-medium rounded-lg bg-gradient-to-r from-red-500 to-orange-500 text-white hover:from-red-600 hover:to-orange-600 shadow-md transition-all">
                        <i class="fas fa-search mr-2"></i> Filter
                    </button>
                </div>
            </form>
        </x-card>

        {{-- Tabel + Bulk WA --}}
        <x-card>
            <form action="{{ route('attendance.reports.alpha.notify') }}" method="POST" id="notifyForm">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">

                {{-- Toolbar --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-lg"
                             style="background: linear-gradient(135deg, #ef4444, #f97316);">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white">
                                {{ $students->count() }} Siswa Alpha
                                @if($classId) — {{ $classes->find($classId)?->nama_kelas }} @endif
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Periode: {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }} · min. {{ $minAlpha }}× alpha
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Select all --}}
                        <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"
                                   class="rounded border-gray-300 text-red-500 focus:ring-red-400">
                            Pilih Semua
                        </label>
                        {{-- Kirim WA Terpilih --}}
                        <button type="submit" id="btnNotify"
                                onclick="return confirm('Kirim WA ke orang tua siswa yang dipilih?')"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg text-white transition-all shadow-md disabled:opacity-40"
                                style="background: linear-gradient(135deg, #25d366, #128c7e);">
                            <i class="fab fa-whatsapp mr-2"></i>
                            Kirim WA Terpilih
                        </button>
                    </div>
                </div>

                {{-- Tabel --}}
                @if($students->isEmpty())
                    <div class="text-center py-16 text-gray-400 dark:text-gray-600">
                        <i class="fas fa-check-circle text-4xl mb-3 text-green-400"></i>
                        <p class="font-medium">Tidak ada siswa dengan alpha ≥ {{ $minAlpha }} hari pada bulan ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-4 py-3 text-center w-10">
                                        <i class="fas fa-check-square text-gray-400"></i>
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">NIS</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Siswa</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Kelas</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jml Alpha</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">No HP Ortu</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($students as $i => $student)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" name="student_ids[]"
                                                   value="{{ $student->id }}"
                                                   class="row-checkbox rounded border-gray-300 text-red-500 focus:ring-red-400"
                                                   onchange="updateBtnState()">
                                        </td>
                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-mono text-gray-700 dark:text-gray-300 hidden sm:table-cell">{{ $student->nis }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $student->nama }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 hidden md:table-cell">{{ $student->kelas->nama_kelas }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-bold text-white"
                                                  style="background: {{ $student->alpha_count >= 5 ? '#dc2626' : ($student->alpha_count >= 3 ? '#f97316' : '#eab308') }}">
                                                {{ $student->alpha_count }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 hidden sm:table-cell">
                                            @if($student->no_hp_ortu)
                                                <span class="text-gray-700 dark:text-gray-300 text-xs font-mono">{{ $student->no_hp_ortu }}</span>
                                            @else
                                                <span class="text-xs text-red-400 italic">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('attendance.reports.student', $student->id) }}"
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 transition-all">
                                                <i class="fas fa-chart-bar mr-1"></i> Riwayat
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </form>
        </x-card>

    </div>

    @push('scripts')
    <script>
        function toggleSelectAll(master) {
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = master.checked);
            updateBtnState();
        }

        function updateBtnState() {
            const any = document.querySelectorAll('.row-checkbox:checked').length > 0;
            document.getElementById('btnNotify').disabled = !any;
        }

        // Disable tombol saat load jika tidak ada yang dipilih
        document.addEventListener('DOMContentLoaded', updateBtnState);
    </script>
    @endpush
</x-app-layout>
