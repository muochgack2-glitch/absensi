<x-app-layout>
    <x-slot name="title">Dashboard Wali Kelas</x-slot>
    <x-slot name="pageTitle">Dashboard — {{ $kelas->nama_kelas }}</x-slot>

    <div class="space-y-5">

        {{-- Class Info Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $kelas->nama_kelas }}</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">{{ $kelas->jurusan ?? '' }} · {{ count($siswaList) }} siswa</p>
            </div>
            <a href="{{ route('attendance.izin.index') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/50 transition-all">
                <i class="fas fa-file-medical mr-2"></i>
                Lihat Izin Masuk
            </a>
        </div>

        {{-- Stat cards hari ini --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            @php
                $stats = [
                    ['label'=>'Hadir',     'val'=>$todayStats['hadir'],     'color'=>'green',  'icon'=>'fa-check-circle'],
                    ['label'=>'Terlambat', 'val'=>$todayStats['terlambat'], 'color'=>'yellow', 'icon'=>'fa-clock'],
                    ['label'=>'Izin',      'val'=>$todayStats['izin'],      'color'=>'blue',   'icon'=>'fa-file-alt'],
                    ['label'=>'Sakit',     'val'=>$todayStats['sakit'],     'color'=>'purple', 'icon'=>'fa-briefcase-medical'],
                    ['label'=>'Alpha',     'val'=>$todayStats['alpha'],     'color'=>'red',    'icon'=>'fa-times-circle'],
                ];
            @endphp
            @foreach($stats as $s)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">{{ $s['label'] }}</span>
                    <i class="fas {{ $s['icon'] }} text-{{ $s['color'] }}-500"></i>
                </div>
                <p class="text-2xl sm:text-3xl font-extrabold text-{{ $s['color'] }}-600 dark:text-{{ $s['color'] }}-400">{{ $s['val'] }}</p>
                <p class="text-xs text-gray-400 mt-1">hari ini</p>
            </div>
            @endforeach
        </div>

        {{-- Chart 7 hari --}}
        <x-card>
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Kehadiran 7 Hari Terakhir — {{ $kelas->nama_kelas }}
            </h3>
            <canvas id="waliChart" height="80"></canvas>
        </x-card>

        {{-- Daftar siswa bulan ini --}}
        <x-card>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-users text-teal-500 mr-2"></i>
                    Siswa {{ $kelas->nama_kelas }}
                    <span class="text-gray-400 font-normal">({{ count($siswaList) }} siswa)</span>
                </h3>
                <span class="text-xs text-gray-500 dark:text-gray-400">Rekap bulan ini</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 rounded-l-lg">Nama</th>
                            <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 hidden sm:table-cell">NIS</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600 dark:text-green-400">Hadir</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-yellow-600 dark:text-yellow-400">Terlambat</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-blue-600 dark:text-blue-400 hidden sm:table-cell">Izin</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-purple-600 dark:text-purple-400 hidden sm:table-cell">Sakit</th>
                            <th class="px-4 py-2.5 text-center text-xs font-semibold text-red-600 dark:text-red-400 rounded-r-lg">Alpha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($siswaList as $s)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $s->alpha > 3 ? 'bg-red-50/30 dark:bg-red-900/10' : '' }}">
                            <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white">{{ $s->nama }}</td>
                            <td class="px-4 py-2.5 text-xs text-gray-500 dark:text-gray-400 font-mono hidden sm:table-cell">{{ $s->nis }}</td>
                            <td class="px-4 py-2.5 text-center font-bold text-green-600 dark:text-green-400">{{ $s->hadir }}</td>
                            <td class="px-4 py-2.5 text-center text-yellow-600 dark:text-yellow-400">{{ $s->terlambat }}</td>
                            <td class="px-4 py-2.5 text-center text-blue-600 dark:text-blue-400 hidden sm:table-cell">{{ $s->izin ?? 0 }}</td>
                            <td class="px-4 py-2.5 text-center text-purple-600 dark:text-purple-400 hidden sm:table-cell">{{ $s->sakit ?? 0 }}</td>
                            <td class="px-4 py-2.5 text-center font-bold text-red-600 dark:text-red-400">{{ $s->alpha }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('waliChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartDays) !!},
                datasets: [{
                    label: 'Hadir',
                    data: {!! json_encode($chartHadir) !!},
                    backgroundColor: 'rgba(99,102,241,0.7)',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
