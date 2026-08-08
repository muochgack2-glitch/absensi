<x-app-layout>
    <x-slot name="title">Dashboard Absensi</x-slot>
    <x-slot name="pageTitle">Dashboard</x-slot>

    <div class="space-y-6" id="dashboard-content">
        {{-- Filters Section --}}
        <x-card>
            <form method="GET" action="{{ route('attendance.dashboard') }}" id="filterForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-input
                    type="date"
                    name="date"
                    label="Tanggal"
                    :value="$selectedDate"
                    icon="fa-calendar"
                    onchange="document.getElementById('filterForm').submit()"
                />

                <x-select
                    name="class"
                    label="Kelas"
                    :value="$selectedClass ?? ''"
                    icon="fa-school"
                    onchange="document.getElementById('filterForm').submit()"
                >
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $selectedClass == $class->id ? 'selected' : '' }}>
                            {{ $class->nama_kelas }}
                        </option>
                    @endforeach
                </x-select>
            </form>
        </x-card>

        {{-- Statistics Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Students --}}
            <x-stat-card
                title="Total Siswa"
                :value="$stats['total'] ?? 0"
                icon="fa-users"
                color="blue"
            />

            {{-- Present --}}
            <x-stat-card
                title="Hadir"
                :value="$stats['present'] ?? 0"
                icon="fa-check-circle"
                color="green"
            />

            {{-- Late --}}
            <x-stat-card
                title="Terlambat"
                :value="$stats['late'] ?? 0"
                icon="fa-clock"
                color="yellow"
            />

            {{-- Alpha --}}
            <x-stat-card
                title="Alpha"
                :value="$stats['alpha'] ?? 0"
                icon="fa-times-circle"
                color="red"
            />
        </div>

        {{-- ======================== CHARTS ======================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Bar Chart: 7 Hari Terakhir --}}
            <div class="lg:col-span-2">
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 dark:text-white text-base">Tren Kehadiran 7 Hari</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Hadir vs Alpha (hari kerja)</p>
                            </div>
                        </div>
                        {{-- Filter Kelas --}}
                        <select id="chartClassFilter"
                                onchange="loadChartByClass(this.value)"
                                class="text-xs px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-blue-400">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="height:220px; position:relative;">
                        <canvas id="chartBar"></canvas>
                    </div>
                </x-card>
            </div>

            {{-- Donut Chart: Status Hari Ini --}}
            <div>
                <x-card>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 dark:text-white text-base">Status Hari Ini</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Total: {{ $totalToday }} record
                            </p>
                        </div>
                    </div>
                    <div style="height:200px; position:relative;">
                        <canvas id="chartDonut"></canvas>
                    </div>
                    {{-- Legend --}}
                    <div class="mt-3 grid grid-cols-2 gap-1 text-xs">
                        @php
                            $statusColors = ['hadir'=>'#22c55e','terlambat'=>'#f59e0b','alpha'=>'#ef4444','izin'=>'#3b82f6','sakit'=>'#a855f7'];
                            $statusLabels = ['hadir'=>'Hadir','terlambat'=>'Terlambat','alpha'=>'Alpha','izin'=>'Izin','sakit'=>'Sakit'];
                        @endphp
                        @foreach($donutData as $key => $val)
                            <div class="flex items-center gap-1.5">
                                <span class="inline-block w-3 h-3 rounded-full" style="background:{{ $statusColors[$key] }}"></span>
                                <span class="text-gray-600 dark:text-gray-400">{{ $statusLabels[$key] }}: <strong class="text-gray-800 dark:text-white">{{ $val }}</strong></span>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>
        </div>
        {{-- ======================== END CHARTS ======================== --}}

        {{-- Attendance Records Table --}}
        <x-card>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-clipboard-list mr-2 text-primary-600"></i>
                    Data Absensi
                </h3>
                <button onclick="refreshDashboard()" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition text-sm">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">
                                NIS
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Nama
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">
                                Kelas
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Check In
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">
                                Check Out
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">
                                Foto
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($attendanceRecords as $record)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white hidden sm:table-cell">
                                    {{ $record->student->nis }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $record->student->nama }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden md:table-cell">
                                    {{ $record->student->kelas->nama_kelas ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 hidden sm:table-cell">
                                    {{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($record->status === 'hadir')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                            ✅ Hadir
                                        </span>
                                    @elseif($record->status === 'terlambat')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                            ⏰ Terlambat
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ ucfirst($record->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm hidden md:table-cell">
                                    <div class="flex items-center gap-2">
                                        @if($record->check_in_photo)
                                            <button onclick="viewPhoto('{{ $record->check_in_photo_url }}', '{{ addslashes($record->student->nama) }}', 'Check In')"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded hover:bg-green-200 dark:hover:bg-green-900/50 transition-colors"
                                                    title="Lihat foto check in">
                                                <i class="fas fa-sign-in-alt text-sm"></i>
                                            </button>
                                        @else
                                            <div class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded">
                                                <i class="fas fa-sign-in-alt text-sm"></i>
                                            </div>
                                        @endif
                                        
                                        @if($record->check_out_photo)
                                            <button onclick="viewPhoto('{{ $record->check_out_photo_url }}', '{{ addslashes($record->student->nama) }}', 'Check Out')"
                                                    class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded hover:bg-blue-200 dark:hover:bg-blue-900/50 transition-colors"
                                                    title="Lihat foto check out">
                                                <i class="fas fa-sign-out-alt text-sm"></i>
                                            </button>
                                        @else
                                            <div class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 dark:bg-gray-800 text-gray-400 rounded">
                                                <i class="fas fa-sign-out-alt text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-inbox text-4xl mb-3 text-gray-300 dark:text-gray-600"></i>
                                    <p>Belum ada data absensi untuk tanggal ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Absent Students (if any) --}}
        @if($absentStudents->count() > 0)
            <x-card>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">
                    <i class="fas fa-user-times mr-2 text-red-600"></i>
                    Siswa Belum Absen ({{ $absentStudents->count() }})
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($absentStudents as $student)
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $student->nama }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $student->nis }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-500">{{ $student->kelas->nama_kelas ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </x-card>
        @endif
    </div>

    {{-- Photo Modal --}}
    <div id="photoModal" class="hidden fixed inset-0 bg-black bg-opacity-75 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closePhotoModal()">
        <div class="relative max-w-2xl w-full" onclick="event.stopPropagation()">
            {{-- Close Button --}}
            <button onclick="closePhotoModal()" 
                    class="absolute -top-8 right-0 text-white hover:text-gray-300 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
            
            {{-- Photo Container --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl overflow-hidden">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-primary-500 to-purple-600 px-4 py-2 text-white">
                    <h3 class="text-base font-bold" id="photoModalTitle">Foto Absensi</h3>
                    <p class="text-xs opacity-90" id="photoModalSubtitle"></p>
                </div>
                
                {{-- Photo --}}
                <div class="p-3 flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                    <img id="photoModalImage" src="" alt="Foto" class="max-w-full max-h-[40vh] rounded-lg shadow-lg object-contain">
                </div>
                
                {{-- Footer --}}
                <div class="px-4 py-2 bg-gray-100 dark:bg-gray-700 flex justify-between items-center">
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        Klik di luar untuk menutup
                    </div>
                    <button onclick="downloadPhoto()" 
                            class="px-3 py-1.5 bg-primary-500 hover:bg-primary-600 text-white text-xs rounded-lg transition-colors flex items-center gap-1.5">
                        <i class="fas fa-download"></i>
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        /**
         * Manual refresh dashboard data
         */
        function refreshDashboard() {
            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);
            
            fetch('{{ route("attendance.dashboard.refresh") }}?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Refresh error:', error));
        }

        // ============================================================================
        // PHOTO MODAL FUNCTIONS
        // ============================================================================

        function viewPhoto(photoUrl, studentName, type) {
            console.log('viewPhoto called:', { photoUrl, studentName, type });
            
            const modal = document.getElementById('photoModal');
            const image = document.getElementById('photoModalImage');
            const title = document.getElementById('photoModalTitle');
            const subtitle = document.getElementById('photoModalSubtitle');
            
            // Set image source and alt
            image.src = photoUrl;
            image.alt = `Foto ${type} - ${studentName}`;
            image.onerror = function() {
                console.error('Failed to load image:', photoUrl);
                this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect fill="%23ddd" width="400" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" text-anchor="middle" dominant-baseline="middle" font-family="sans-serif" font-size="18"%3EGagal memuat foto%3C/text%3E%3C/svg%3E';
            };
            
            title.textContent = `Foto ${type}`;
            subtitle.textContent = studentName;
            
            modal.classList.remove('hidden');
            
            // Add fade-in animation
            modal.style.animation = 'fadeIn 0.2s ease-out';
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            modal.style.animation = 'fadeOut 0.2s ease-out';
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
        
        function downloadPhoto() {
            const image = document.getElementById('photoModalImage');
            const link = document.createElement('a');
            link.href = image.src;
            link.download = 'foto-absensi-' + Date.now() + '.jpg';
            link.click();
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Alt+R to refresh
            if (e.altKey && e.key === 'r') {
                e.preventDefault();
                refreshDashboard();
            }
            // ESC to close modal
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
    
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            from {
                opacity: 1;
            }
            to {
                opacity: 0;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor  = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';
        const labelColor = isDark ? '#9ca3af' : '#6b7280';

        // ===== Data Bar Chart =====
        const chartBarData = @json($chartData);

        // ===== Donut Chart: Status Hari Ini =====
        const donutData = @json(array_values($donutData));
        const donutLabels = ['Hadir','Terlambat','Alpha','Izin','Sakit'];
        const donutColors = ['#22c55e','#f59e0b','#ef4444','#3b82f6','#a855f7'];

        new Chart(document.getElementById('chartDonut'), {
            type: 'doughnut',
            data: {
                labels: donutLabels,
                datasets: [{
                    data: donutData,
                    backgroundColor: donutColors,
                    borderWidth: 2,
                    borderColor: isDark ? '#1f2937' : '#ffffff',
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${ctx.raw} siswa`
                        }
                    }
                },
            },
        });

        // ===== Bar Chart: bisa diupdate via AJAX =====
        const barChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { labels: { color: labelColor, font: { size: 11 } } },
            },
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: labelColor } },
                y: { grid: { color: gridColor }, ticks: { color: labelColor, stepSize: 1 }, beginAtZero: true },
            },
        };

        function makeBarDatasets(d) {
            return [
                { label: 'Hadir',     data: d.hadir,     backgroundColor: 'rgba(34,197,94,0.8)',  borderRadius: 5 },
                { label: 'Terlambat', data: d.terlambat, backgroundColor: 'rgba(245,158,11,0.8)', borderRadius: 5 },
                { label: 'Alpha',     data: d.alpha,     backgroundColor: 'rgba(239,68,68,0.8)',  borderRadius: 5 },
            ];
        }

        const barChart = new Chart(document.getElementById('chartBar'), {
            type: 'bar',
            data: {
                labels:   chartBarData.labels,
                datasets: makeBarDatasets(chartBarData),
            },
            options: barChartOptions,
        });

        // ===== AJAX: Filter Per Kelas =====
        async function loadChartByClass(classId) {
            const select = document.getElementById('chartClassFilter');
            select.disabled = true;

            try {
                const url = `{{ route('attendance.dashboard.chart-data') }}?class_id=${classId}`;
                const resp = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await resp.json();

                barChart.data.labels   = data.labels;
                barChart.data.datasets = makeBarDatasets(data);
                barChart.update('active');

                // Update judul subtitle
                const subtitle = document.querySelector('#chartBar').closest('.x-card, div[class*="card"]')
                    ?.querySelector('p.text-xs');
                if (subtitle) {
                    subtitle.textContent = classId
                        ? select.options[select.selectedIndex].text + ' — 7 hari terakhir'
                        : 'Hadir vs Alpha (hari kerja)';
                }
            } catch(e) {
                console.error('Gagal load chart data:', e);
            } finally {
                select.disabled = false;
            }
        }
    </script>
    @endpush
</x-app-layout>

