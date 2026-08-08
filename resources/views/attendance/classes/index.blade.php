<x-app-layout>
    <x-slot name="title">Data Kelas</x-slot>
    <x-slot name="pageTitle">Manajemen Kelas</x-slot>

    <div class="space-y-6">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Kelas</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola data kelas dan organisasi siswa</p>
            </div>
            
            <a
                href="{{ route('attendance.classes.create') }}"
                class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold rounded-xl transition-all duration-200 bg-gradient-to-r from-primary-500 to-blue-600 text-white hover:from-primary-600 hover:to-blue-700 shadow-lg hover:shadow-xl hover:-translate-y-0.5"
            >
                <i class="fas fa-plus mr-2"></i>
                Tambah Kelas
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('warning'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-300">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-lg"></i>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-stat-card
                title="Total Kelas"
                :value="\App\Models\AttendanceClass::count()"
                icon="fas fa-school"
                color="blue"
            />
            
            <x-stat-card
                title="Kelas Aktif"
                :value="\App\Models\AttendanceClass::where('is_active', true)->count()"
                icon="fas fa-check-circle"
                color="success"
            />
            
            <x-stat-card
                title="Total Siswa"
                :value="\App\Models\AttendanceStudent::count()"
                icon="fas fa-users"
                color="purple"
            />
            
            <x-stat-card
                title="Rata-rata/Kelas"
                :value="round(\App\Models\AttendanceStudent::count() / max(\App\Models\AttendanceClass::count(), 1))"
                icon="fas fa-chart-line"
                color="info"
            />
        </div>

        {{-- Search & Filter --}}
        <x-card>
            <form method="GET" action="{{ route('attendance.classes.index') }}" class="space-y-4" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div class="md:col-span-2">
                        <x-input
                            type="text"
                            name="search"
                            :value="request('search')"
                            placeholder="Cari nama kelas atau jurusan..."
                            icon="fa-search"
                            label="Pencarian"
                            id="searchInput"
                        />
                    </div>
                    
                    {{-- Tingkat --}}
                    <div>
                        <x-select
                            name="tingkat"
                            label="Tingkat"
                            onchange="document.getElementById('filterForm').submit()"
                        >
                            <option value="">Semua Tingkat</option>
                            @foreach(\App\Models\AttendanceClass::select('tingkat')->distinct()->orderBy('tingkat')->pluck('tingkat') as $tkt)
                                <option value="{{ $tkt }}" {{ request('tingkat') == $tkt ? 'selected' : '' }}>Kelas {{ $tkt }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    
                    {{-- Status --}}
                    <div>
                        <x-select
                            name="is_active"
                            label="Status"
                            onchange="document.getElementById('filterForm').submit()"
                        >
                            <option value="">Semua</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Non-Aktif</option>
                        </x-select>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <a href="{{ route('attendance.classes.index') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 border-2 border-primary-500 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20">
                        <i class="fas fa-redo mr-2"></i> Reset Filter
                    </a>
                </div>
            </form>
        </x-card>

        @push('scripts')
        <script>
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const filterForm  = document.getElementById('filterForm');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() { filterForm.submit(); }, 500);
                });
            }
        </script>
        @endpush

        {{-- Classes Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($classes as $class)
                @php
                    $tingkatColors = [
                        'X'   => ['from-blue-500 to-cyan-500', 'bg-blue-500'],
                        'XI'  => ['from-purple-500 to-indigo-500', 'bg-purple-500'],
                        'XII' => ['from-emerald-500 to-teal-500', 'bg-emerald-500'],
                    ];
                    $gradientClass = $tingkatColors[$class->tingkat][0] ?? 'from-primary-500 to-blue-600';
                    $accentClass   = $tingkatColors[$class->tingkat][1] ?? 'bg-primary-500';
                @endphp
                <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    {{-- Top Accent Bar --}}
                    <div class="h-1.5 bg-gradient-to-r {{ $gradientClass }}"></div>

                    <div class="p-5 space-y-4">
                        {{-- Header --}}
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-gradient-to-br {{ $gradientClass }} rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                    {{ $class->tingkat }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                                        {{ $class->nama_kelas }}
                                    </h3>
                                    @if($class->jurusan)
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $class->jurusan }}</p>
                                    @endif
                                </div>
                            </div>
                            
                            @if($class->is_active)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>
                                    Non-Aktif
                                </span>
                            @endif
                        </div>

                        {{-- Wali Kelas --}}
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user-tie text-white text-xs"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[10px] uppercase tracking-wider text-gray-400 dark:text-gray-500 font-semibold">Wali Kelas</p>
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ $class->waliKelas ? $class->waliKelas->name : 'Belum ditentukan' }}
                                </p>
                            </div>
                        </div>

                        {{-- Students Count --}}
                        <div class="relative p-4 rounded-xl bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-600 overflow-hidden">
                            <div class="absolute top-2 right-3 text-gray-200 dark:text-gray-600/30">
                                <i class="fas fa-users text-4xl"></i>
                            </div>
                            <div class="relative">
                                <div class="text-4xl font-extrabold bg-gradient-to-r {{ $gradientClass }} bg-clip-text text-transparent">
                                    {{ $class->students_count ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">Siswa Terdaftar</div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <a href="{{ route('attendance.students.index', ['kelas_id' => $class->id]) }}"
                               class="flex-1 inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 bg-blue-50 dark:bg-blue-900/40 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/60 hover:shadow-sm"
                               title="Lihat Siswa">
                                <i class="fas fa-users mr-1.5"></i>
                                <span class="hidden sm:inline">Siswa</span>
                            </a>
                            <a href="{{ route('attendance.classes.edit', $class->id) }}"
                               class="flex-1 inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 bg-amber-50 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/60 hover:shadow-sm"
                               title="Edit Kelas">
                                <i class="fas fa-edit mr-1.5"></i>
                                <span class="hidden sm:inline">Edit</span>
                            </a>
                            <form action="{{ route('attendance.classes.destroy', $class->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus kelas {{ $class->nama_kelas }}?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-full inline-flex items-center justify-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 bg-red-50 dark:bg-red-900/40 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/60 hover:shadow-sm"
                                        title="Hapus Kelas">
                                    <i class="fas fa-trash mr-1.5"></i>
                                    <span class="hidden sm:inline">Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <x-card class="text-center py-12">
                        <x-empty-state
                            icon="fas fa-school"
                            message="Belum ada data kelas"
                        >
                            <x-slot name="action">
                                <a
                                    href="{{ route('attendance.classes.create') }}"
                                    class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold rounded-xl transition-all duration-200 bg-gradient-to-r from-primary-500 to-blue-600 text-white hover:from-primary-600 hover:to-blue-700 shadow-lg hover:shadow-xl mt-4"
                                >
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Kelas Pertama
                                </a>
                            </x-slot>
                        </x-empty-state>
                    </x-card>
                </div>
            @endforelse
        </div>

        {{-- Pagination Info + Links --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Menampilkan {{ $classes->firstItem() ?? 0 }}-{{ $classes->lastItem() ?? 0 }} dari {{ $classes->total() }} kelas
            </p>
            @if($classes->hasPages())
                <div>{{ $classes->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
