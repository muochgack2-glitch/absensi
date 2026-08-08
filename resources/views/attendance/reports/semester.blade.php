@php use Illuminate\Support\Facades\Storage; @endphp
<x-app-layout>
    <x-slot name="title">Rekap Semester</x-slot>
    <x-slot name="pageTitle">Rekap Kehadiran Semester</x-slot>

    <div class="space-y-6">

        {{-- FILTER FORM --}}
        <x-card>
            <div class="flex items-center mb-5">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl mr-4">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Filter Rekap Semester</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Pilih semester, tahun ajaran, dan kelas</p>
                </div>
            </div>

            <form method="GET" action="{{ route('attendance.reports.semester') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Semester --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Semester</label>
                    <select name="semester" id="semesterSelect" onchange="updateTahunAjaran()"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 text-sm">
                        <option value="ganjil" {{ $semester === 'ganjil' ? 'selected' : '' }}>Ganjil (Jul – Des)</option>
                        <option value="genap"  {{ $semester === 'genap'  ? 'selected' : '' }}>Genap  (Jan – Jun)</option>
                    </select>
                </div>

                {{-- Tahun Ajaran --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Ajaran</label>
                    <select name="tahun_ajaran"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 text-sm">
                        @for ($y = $currentYear + 1; $y >= $currentYear - 3; $y--)
                            @php $ta = $y . '/' . ($y + 1); @endphp
                            <option value="{{ $ta }}" {{ $tahunAjaran === $ta ? 'selected' : '' }}>{{ $ta }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Kelas --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kelas</label>
                    <select name="class_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ $classId == $cls->id ? 'selected' : '' }}>{{ $cls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Submit --}}
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium text-sm transition-all shadow hover:shadow-lg">
                        <i class="fas fa-search mr-2"></i>Tampilkan
                    </button>
                </div>
            </form>

            @if(!empty($rekap))
            {{-- Info periode --}}
            <div class="mt-4 flex flex-wrap gap-3 text-xs">
                <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full font-medium">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                </span>
                <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full font-medium">
                    <i class="fas fa-school mr-1"></i>{{ $totalHari }} hari sekolah
                </span>
                <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full font-medium">
                    <i class="fas fa-users mr-1"></i>{{ count($rekap) }} siswa
                </span>
            </div>
            @endif
        </x-card>

        @if(!empty($rekap))
        {{-- TABEL REKAP --}}
        <x-card>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <h3 class="font-bold text-gray-900 dark:text-white text-base">
                    📊 Rekap Semester {{ ucfirst($semester) }} — {{ $tahunAjaran }}
                </h3>
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Export PDF --}}
                    <a href="{{ route('attendance.reports.semester.pdf', request()->query()) }}"
                       target="_blank"
                       class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-all shadow">
                        <i class="fas fa-file-pdf mr-1.5"></i>PDF
                    </a>
                    {{-- Export Excel --}}
                    <a href="{{ route('attendance.reports.semester.excel', request()->query()) }}"
                       class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-all shadow">
                        <i class="fas fa-file-excel mr-1.5"></i>Excel
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-400 rounded-l-lg">No</th>
                            <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-400 hidden sm:table-cell">NIS</th>
                            <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-400">Nama Siswa</th>
                            <th class="px-3 py-2 font-semibold text-gray-600 dark:text-gray-400 hidden md:table-cell">Kelas</th>
                            <th class="px-3 py-2 font-semibold text-center text-green-600 dark:text-green-400">Hadir</th>
                            <th class="px-3 py-2 font-semibold text-center text-yellow-600 dark:text-yellow-400">Terlambat</th>
                            <th class="px-3 py-2 font-semibold text-center text-blue-600 dark:text-blue-400">Izin</th>
                            <th class="px-3 py-2 font-semibold text-center text-purple-600 dark:text-purple-400">Sakit</th>
                            <th class="px-3 py-2 font-semibold text-center text-red-600 dark:text-red-400">Alpha</th>
                            <th class="px-3 py-2 font-semibold text-center text-gray-600 dark:text-gray-400">% Hadir</th>
                            <th class="px-3 py-2 font-semibold text-center text-gray-600 dark:text-gray-400 rounded-r-lg">Ket</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($rekap as $i => $row)
                        @php
                            $persen = $totalHari > 0 ? round(($row['hadir'] / $totalHari) * 100, 1) : 0;
                            $ket = $persen >= 75 ? 'BAIK' : ($persen >= 50 ? 'CUKUP' : 'KURANG');
                            $ketColor = $persen >= 75 ? 'text-green-600 dark:text-green-400' : ($persen >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');
                            $bgRow = $persen < 75 ? 'bg-red-50/30 dark:bg-red-900/10' : '';
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $bgRow }}">
                            <td class="px-3 py-2 text-gray-500 dark:text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400 font-mono hidden sm:table-cell">{{ $row['nis'] }}</td>
                            <td class="px-3 py-2 font-medium text-gray-900 dark:text-white">{{ $row['nama'] }}</td>
                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400 hidden md:table-cell">{{ $row['kelas'] }}</td>
                            <td class="px-3 py-2 text-center font-bold text-green-600 dark:text-green-400">{{ $row['hadir'] }}</td>
                            <td class="px-3 py-2 text-center text-yellow-600 dark:text-yellow-400">{{ $row['terlambat'] }}</td>
                            <td class="px-3 py-2 text-center text-blue-600 dark:text-blue-400">{{ $row['izin'] }}</td>
                            <td class="px-3 py-2 text-center text-purple-600 dark:text-purple-400">{{ $row['sakit'] }}</td>
                            <td class="px-3 py-2 text-center font-bold text-red-600 dark:text-red-400">{{ $row['alpha'] }}</td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $persen >= 75 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : ($persen >= 50 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400') }}">
                                    {{ $persen }}%
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center font-semibold {{ $ketColor }}">{{ $ket }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 dark:bg-gray-700 font-semibold">
                            <td colspan="4" class="px-3 py-2 text-gray-700 dark:text-gray-300 rounded-l-lg">TOTAL</td>
                            <td class="px-3 py-2 text-center text-green-600 dark:text-green-400">{{ collect($rekap)->sum('hadir') }}</td>
                            <td class="px-3 py-2 text-center text-yellow-600 dark:text-yellow-400">{{ collect($rekap)->sum('terlambat') }}</td>
                            <td class="px-3 py-2 text-center text-blue-600 dark:text-blue-400">{{ collect($rekap)->sum('izin') }}</td>
                            <td class="px-3 py-2 text-center text-purple-600 dark:text-purple-400">{{ collect($rekap)->sum('sakit') }}</td>
                            <td class="px-3 py-2 text-center text-red-600 dark:text-red-400">{{ collect($rekap)->sum('alpha') }}</td>
                            <td colspan="2" class="px-3 py-2 rounded-r-lg"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-card>
        @elseif(request()->hasAny(['semester', 'class_id']))
        <x-card>
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <i class="fas fa-inbox text-4xl mb-3 block"></i>
                <p>Tidak ada data absensi untuk filter yang dipilih.</p>
            </div>
        </x-card>
        @else
        <x-card>
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <i class="fas fa-graduation-cap text-5xl mb-4 block opacity-30"></i>
                <p class="text-base font-medium">Pilih semester, tahun ajaran, dan kelas lalu klik <strong>Tampilkan</strong></p>
                <p class="text-sm mt-1">Data rekap kehadiran se-semester akan muncul di sini</p>
            </div>
        </x-card>
        @endif

    </div>
</x-app-layout>
