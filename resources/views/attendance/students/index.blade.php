<x-app-layout>
    <x-slot name="title">Data Siswa</x-slot>
    <x-slot name="pageTitle">Manajemen Siswa</x-slot>

    <div class="space-y-6">
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
        @if(session('info'))
            <div class="flex items-center gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-blue-800 dark:text-blue-300">
                <i class="fas fa-info-circle text-blue-500 text-lg"></i>
                <span class="font-medium">{{ session('info') }}</span>
            </div>
        @endif
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Siswa</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Kelola data siswa dan QR Code absensi</p>
            </div>

            <div class="flex flex-col gap-2">
                {{-- Baris 1: Download / Import / Cetak --}}
                <div class="flex flex-wrap gap-2">
                    {{-- Download Template: admin + waka --}}
                    <a
                        href="{{ route('attendance.students.export.template') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-green-600 hover:bg-green-700 text-white shadow-sm"
                    >
                        <i class="fas fa-file-excel mr-2"></i>
                        Download Template
                    </a>

                    {{-- Import Excel: admin only --}}
                    @if(auth()->user()?->isAdmin())
                    <a
                        href="{{ route('attendance.students.import.form') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-blue-600 hover:bg-blue-700 text-white shadow-sm"
                    >
                        <i class="fas fa-file-import mr-2"></i>
                        Import Excel
                    </a>
                    @endif

                    {{-- Export Excel: admin + waka --}}
                    <a
                        href="{{ route('attendance.students.export.excel', request()->only('class', 'status')) }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm"
                    >
                        <i class="fas fa-file-download mr-2"></i>
                        Export Excel
                    </a>

                    {{-- Cetak Kartu: admin + waka --}}
                    <a
                        href="{{ route('attendance.students.card') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-purple-600 hover:bg-purple-700 text-white shadow-sm"
                    >
                        <i class="fas fa-id-card mr-2"></i>
                        Cetak Kartu
                    </a>
                </div>

                {{-- Baris 2: Generate QR Massal + Upload Foto + Download Kartu + Tambah Siswa (admin only) --}}
                @if(auth()->user()?->isAdmin())
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        onclick="document.getElementById('modalBulkQR').classList.remove('hidden')"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-teal-700 hover:bg-teal-800 text-white shadow-sm"
                    >
                        <i class="fas fa-qrcode mr-2"></i>
                        Generate QR Massal
                    </button>

                    <button
                        type="button"
                        onclick="document.getElementById('modalBulkFoto').classList.remove('hidden')"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-pink-600 hover:bg-pink-700 text-white shadow-sm"
                    >
                        <i class="fas fa-images mr-2"></i>
                        Upload Foto Massal
                    </button>

                    <button
                        type="button"
                        onclick="document.getElementById('modalBulkKartuQR').classList.remove('hidden')"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-orange-600 hover:bg-orange-700 text-white shadow-sm"
                    >
                        <i class="fas fa-id-card mr-2"></i>
                        Download Kartu QR
                    </button>

                    <a
                        href="{{ route('attendance.students.create') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Siswa
                    </a>

                    <a
                        href="{{ route('attendance.students.phones') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-cyan-600 hover:bg-cyan-700 text-white shadow-sm"
                    >
                        <i class="fas fa-phone-alt mr-2"></i>
                        Update No HP
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <x-stat-card
                title="Total Siswa"
                :value="\App\Models\AttendanceStudent::count()"
                icon="fas fa-users"
                color="blue"
            />
            
            <x-stat-card
                title="Siswa Aktif"
                :value="\App\Models\AttendanceStudent::where('is_active', true)->count()"
                icon="fas fa-user-check"
                color="success"
            />
            
            <x-stat-card
                title="Tidak Aktif"
                :value="\App\Models\AttendanceStudent::where('is_active', false)->count()"
                icon="fas fa-user-slash"
                color="danger"
            />
            
            <x-stat-card
                title="QR Code Dibuat"
                :value="\App\Models\AttendanceStudent::whereNotNull('qr_code_path')->count()"
                icon="fas fa-qrcode"
                color="purple"
            />
        </div>

        {{-- Quick Filter Tingkat --}}
        @php
            $tingkatList = \App\Models\AttendanceClass::select('tingkat')->distinct()->orderBy('tingkat')->pluck('tingkat');
        @endphp
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('attendance.students.index', request()->except(['tingkat', 'page'])) }}"
               class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ !request('tingkat') ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                Semua Tingkat
            </a>
            @foreach($tingkatList as $tkt)
                <a href="{{ route('attendance.students.index', array_merge(request()->except('page'), ['tingkat' => $tkt])) }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ request('tingkat') == $tkt ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                    Kelas {{ $tkt }}
                </a>
            @endforeach
        </div>

        {{-- Search & Filter Card --}}
        <x-card>
            <form method="GET" action="{{ route('attendance.students.index') }}" class="space-y-4" id="filterForm">
                {{-- Preserve tingkat filter --}}
                @if(request('tingkat'))
                    <input type="hidden" name="tingkat" value="{{ request('tingkat') }}">
                @endif
                {{-- Preserve sort params --}}
                @if(request('sort_by'))
                    <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                    <input type="hidden" name="sort_dir" value="{{ request('sort_dir', 'asc') }}">
                @endif
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    {{-- Search Input --}}
                    <div class="md:col-span-2">
                        <x-input
                            type="text"
                            name="search"
                            :value="request('search')"
                            placeholder="Cari nama atau NIS..."
                            icon="fa-search"
                            label="Pencarian"
                            id="searchInput"
                        />
                    </div>
                    
                    {{-- Class Filter --}}
                    <div>
                        <x-select
                            name="kelas_id"
                            label="Filter Kelas"
                            onchange="document.getElementById('filterForm').submit()"
                        >
                            <option value="">Semua Kelas</option>
                            @foreach(\App\Models\AttendanceClass::orderBy('tingkat')->orderBy('nama_kelas')->get() as $class)
                                <option value="{{ $class->id }}" {{ request('kelas_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->nama_kelas }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                    
                    {{-- Status Filter --}}
                    <div>
                        <x-select
                            name="status"
                            label="Status"
                            onchange="document.getElementById('filterForm').submit()"
                        >
                            <option value="">Semua</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        </x-select>
                    </div>
                    
                    {{-- QR Code Filter --}}
                    <div>
                        <x-select
                            name="qr"
                            label="QR Code"
                            onchange="document.getElementById('filterForm').submit()"
                        >
                            <option value="">Semua</option>
                            <option value="has_qr" {{ request('qr') == 'has_qr' ? 'selected' : '' }}>Sudah Punya QR</option>
                            <option value="no_qr" {{ request('qr') == 'no_qr' ? 'selected' : '' }}>Belum Punya QR</option>
                        </x-select>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    {{-- Per-page Selector --}}
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Tampilkan</span>
                        <select name="per_page" onchange="document.getElementById('filterForm').submit()"
                                class="rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-primary-500 focus:border-primary-500 py-1.5 px-2">
                            @foreach([15, 25, 50, 100] as $pp)
                                <option value="{{ $pp }}" {{ request('per_page', 15) == $pp ? 'selected' : '' }}>{{ $pp }}</option>
                            @endforeach
                        </select>
                        <span class="text-sm text-gray-600 dark:text-gray-400">data</span>
                    </div>
                    
                    <a
                        href="{{ route('attendance.students.index') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 border-2 border-primary-500 text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20"
                    >
                        <i class="fas fa-redo mr-2"></i>
                        Reset Filter
                    </a>
                </div>
            </form>
        </x-card>

        @push('scripts')
        <script>
            // Debounce untuk search input
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const filterForm  = document.getElementById('filterForm');

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        filterForm.submit();
                    }, 500);
                });
            }

            // ===== QR Modal =====
            function showQrModal(imgUrl, nama, nis, downloadUrl) {
                document.getElementById('qrImageEl').src        = imgUrl;
                document.getElementById('qrStudentName').textContent = nama;
                document.getElementById('qrStudentNis').textContent  = 'NIS: ' + nis;
                document.getElementById('qrDownloadLink').href   = downloadUrl;
                document.getElementById('modalQrViewer').classList.remove('hidden');
            }

            function closeQrModal() {
                document.getElementById('modalQrViewer').classList.add('hidden');
                document.getElementById('qrImageEl').src = '';
            }

            // Tutup modal QR dengan ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeQrModal();
                    document.getElementById('modalBulkQR').classList.add('hidden');
                }
            });

            // ===== Bulk Action =====
            function updateBulkToolbar() {
                const checked = document.querySelectorAll('.row-check:checked');
                const toolbar = document.getElementById('bulkToolbar');
                const counter = document.getElementById('selectedCount');
                const master  = document.getElementById('selectAll');

                counter.textContent = checked.length;

                if (checked.length > 0) {
                    toolbar.classList.remove('hidden');
                    toolbar.classList.add('flex');
                } else {
                    toolbar.classList.add('hidden');
                    toolbar.classList.remove('flex');
                    master.checked = false;
                }

                // Update master checkbox state
                const allChecks = document.querySelectorAll('.row-check');
                master.indeterminate = checked.length > 0 && checked.length < allChecks.length;
                if (checked.length === allChecks.length && allChecks.length > 0) master.checked = true;
            }

            function toggleSelectAll(master) {
                document.querySelectorAll('.row-check').forEach(cb => cb.checked = master.checked);
                updateBulkToolbar();
            }
        </script>
        @endpush

        {{-- Students Table --}}
        <form action="{{ route('attendance.students.bulk-action') }}" method="POST" id="bulkForm">
        @csrf

        {{-- Bulk Action Toolbar (muncul saat ada yang dipilih) --}}
        <div id="bulkToolbar"
             class="hidden items-center justify-between p-4 mb-3 rounded-xl border-2 border-primary-400 bg-primary-50 dark:bg-primary-900/20 shadow-md">
            <div class="flex items-center gap-3">
                <span class="text-sm font-semibold text-primary-700 dark:text-primary-300">
                    <span id="selectedCount">0</span> siswa dipilih
                </span>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" name="action" value="activate"
                        onclick="return confirm('Aktifkan siswa yang dipilih?')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-green-600 hover:bg-green-700 text-white transition-all shadow">
                    <i class="fas fa-user-check mr-2"></i> Aktifkan
                </button>
                <button type="submit" name="action" value="deactivate"
                        onclick="return confirm('Nonaktifkan siswa yang dipilih?')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-yellow-500 hover:bg-yellow-600 text-white transition-all shadow">
                    <i class="fas fa-user-minus mr-2"></i> Nonaktifkan
                </button>
                <button type="submit" name="action" value="delete"
                        onclick="return confirm('HAPUS PERMANEN siswa yang dipilih? Data tidak bisa dikembalikan!')"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg bg-red-600 hover:bg-red-700 text-white transition-all shadow">
                    <i class="fas fa-trash mr-2"></i> Hapus
                </button>
            </div>
        </div>

        <x-section-card title="Daftar Siswa">
            <x-table>
                <x-slot name="header">
                    <x-table.header class="w-10">
                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"
                               class="rounded border-gray-300 dark:border-gray-600 text-primary-500 focus:ring-primary-400">
                    </x-table.header>
                    <x-table.header>Foto</x-table.header>
                    @php
                        $currentSort = request('sort_by', 'nama');
                        $currentDir = request('sort_dir', 'asc');
                        $sortParams = request()->except(['sort_by', 'sort_dir', 'page']);
                    @endphp
                    <x-table.header>
                        <a href="{{ route('attendance.students.index', array_merge($sortParams, ['sort_by' => 'nis', 'sort_dir' => ($currentSort === 'nis' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}"
                           class="flex items-center gap-1 hover:text-primary-500 transition-colors">
                            NIS
                            @if($currentSort === 'nis')
                                <i class="fas fa-sort-{{ $currentDir === 'asc' ? 'up' : 'down' }} text-primary-500"></i>
                            @else
                                <i class="fas fa-sort text-gray-400 text-xs"></i>
                            @endif
                        </a>
                    </x-table.header>
                    <x-table.header>
                        <a href="{{ route('attendance.students.index', array_merge($sortParams, ['sort_by' => 'nama', 'sort_dir' => ($currentSort === 'nama' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}"
                           class="flex items-center gap-1 hover:text-primary-500 transition-colors">
                            Nama
                            @if($currentSort === 'nama')
                                <i class="fas fa-sort-{{ $currentDir === 'asc' ? 'up' : 'down' }} text-primary-500"></i>
                            @else
                                <i class="fas fa-sort text-gray-400 text-xs"></i>
                            @endif
                        </a>
                    </x-table.header>
                    <x-table.header>
                        <a href="{{ route('attendance.students.index', array_merge($sortParams, ['sort_by' => 'kelas_id', 'sort_dir' => ($currentSort === 'kelas_id' && $currentDir === 'asc') ? 'desc' : 'asc'])) }}"
                           class="flex items-center gap-1 hover:text-primary-500 transition-colors">
                            Kelas
                            @if($currentSort === 'kelas_id')
                                <i class="fas fa-sort-{{ $currentDir === 'asc' ? 'up' : 'down' }} text-primary-500"></i>
                            @else
                                <i class="fas fa-sort text-gray-400 text-xs"></i>
                            @endif
                        </a>
                    </x-table.header>
                    <x-table.header>No HP Ortu</x-table.header>
                    <x-table.header>QR Code</x-table.header>
                    <x-table.header>Status</x-table.header>
                    <x-table.header>Aksi</x-table.header>
                </x-slot>

                @forelse($students as $student)
                    <x-table.row>
                        {{-- Checkbox --}}
                        <x-table.cell>
                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                   class="row-check rounded border-gray-300 dark:border-gray-600 text-primary-500 focus:ring-primary-400"
                                   onchange="updateBulkToolbar()">
                        </x-table.cell>
                        {{-- Photo --}}
                        <x-table.cell>
                            @if($student->foto_profil)
                                <img 
                                    src="{{ asset('storage/' . $student->foto_profil) }}" 
                                    alt="{{ $student->nama }}"
                                    class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600"
                                >
                            @else
                                @php
                                    $avatarColors = [
                                        ['from-blue-400', 'to-blue-600'],
                                        ['from-emerald-400', 'to-emerald-600'],
                                        ['from-purple-400', 'to-purple-600'],
                                        ['from-rose-400', 'to-rose-600'],
                                        ['from-amber-400', 'to-amber-600'],
                                        ['from-cyan-400', 'to-cyan-600'],
                                        ['from-indigo-400', 'to-indigo-600'],
                                        ['from-pink-400', 'to-pink-600'],
                                    ];
                                    $colorIdx = crc32($student->nama) % count($avatarColors);
                                    $colorIdx = abs($colorIdx);
                                @endphp
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $avatarColors[$colorIdx][0] }} {{ $avatarColors[$colorIdx][1] }} flex items-center justify-center text-white text-sm font-semibold">
                                    {{ strtoupper(substr($student->nama, 0, 1)) }}
                                </div>
                            @endif
                        </x-table.cell>
                        
                        {{-- NIS --}}
                        <x-table.cell>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $student->nis }}</span>
                        </x-table.cell>
                        
                        {{-- Name --}}
                        <x-table.cell>
                            <span class="text-gray-900 dark:text-white">{{ $student->nama }}</span>
                        </x-table.cell>
                        
                        {{-- Class --}}
                        <x-table.cell>
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $student->kelas->nama_kelas }}
                            </span>
                        </x-table.cell>
                        
                        {{-- Parent Phone (shown on all devices) --}}
                        <x-table.cell>
                            <span class="text-gray-600 dark:text-gray-400 text-sm">
                                {{ $student->no_hp_ortu ?? '-' }}
                            </span>
                            @if($student->no_hp_ortu2)
                            <span class="block text-xs text-purple-500 dark:text-purple-400 mt-0.5">
                                <i class="fas fa-user-shield mr-1"></i>{{ $student->no_hp_ortu2 }}
                            </span>
                            @endif
                        </x-table.cell>
                        
                        {{-- QR Code (shown on all devices) --}}
                        <x-table.cell>
                            @if($student->qr_code_path)
                                <button
                                    type="button"
                                    onclick="showQrModal('{{ asset('storage/' . $student->qr_code_path) }}', '{{ addslashes($student->nama) }}', '{{ $student->nis }}', '{{ route('attendance.qr.download', $student->nis) }}')"
                                    class="inline-flex items-center text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium transition-colors"
                                    title="Lihat QR Code {{ $student->nama }}"
                                >
                                    <i class="fas fa-qrcode mr-1"></i>
                                    Lihat
                                </button>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 text-sm">Belum ada</span>
                            @endif
                        </x-table.cell>
                        
                        {{-- Status --}}
                        <x-table.cell>
                            @if($student->is_active)
                                <x-badge variant="success">Aktif</x-badge>
                            @else
                                <x-badge variant="danger">Tidak Aktif</x-badge>
                            @endif
                        </x-table.cell>
                        
                        {{-- Actions --}}
                        <x-table.cell>
                            <div class="flex items-center space-x-2">
                                {{-- Download QR Card PDF: admin only --}}
                                @if(auth()->user()?->isAdmin() && $student->qr_code_path)
                                    <a 
                                        href="{{ route('attendance.qr.download-card-pdf', $student->nis) }}" 
                                        class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
                                        title="Unduh Kartu QR PDF"
                                    >
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif

                                {{-- Print QR Kartu: admin + waka --}}
                                <a 
                                    href="{{ route('attendance.students.print-qr', $student->id) }}" 
                                    class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300"
                                    title="Cetak Kartu QR"
                                    target="_blank"
                                >
                                    <i class="fas fa-id-card"></i>
                                </a>

                                {{-- View --}}
                                <a 
                                    href="{{ route('attendance.students.show', $student->id) }}" 
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                                    title="Lihat Detail"
                                >
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                {{-- Edit --}}
                                <a 
                                    href="{{ route('attendance.students.edit', $student->id) }}" 
                                    class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-700 dark:hover:text-yellow-300"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                {{-- Delete: admin only --}}
                                @if(auth()->user()?->isAdmin())
                                <button 
                                    type="button"
                                    onclick="deleteStudent('{{ route('attendance.students.destroy', $student->id) }}', '{{ addslashes($student->nama) }}')"
                                    class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
                                    title="Hapus"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </x-table.cell>
                    </x-table.row>
                @empty
                    <x-table.row>
                        <x-table.cell colspan="8" class="text-center py-12">
                            <x-empty-state 
                                icon="fas fa-users"
                                message="Belum ada data siswa"
                            >
                                <x-slot name="action">
                                    @if(auth()->user()?->isAdmin())
                                    <a
                                        href="{{ route('attendance.students.create') }}"
                                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-150 bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm mt-4"
                                    >
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Siswa Pertama
                                    </a>
                                    @endif
                                </x-slot>
                            </x-empty-state>
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table>
            
            {{-- Pagination Info + Links --}}
            <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Menampilkan {{ $students->firstItem() ?? 0 }}-{{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} siswa
                </p>
                @if($students->hasPages())
                    <div>{{ $students->links() }}</div>
                @endif
            </div>
        </x-section-card>
        </form> {{-- /bulkForm --}}

        {{-- Hidden Delete Form (outside bulkForm to avoid nesting) --}}
        <form id="deleteStudentForm" method="POST" action="" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    @push('scripts')
    <script>
        function deleteStudent(url, name) {
            if (confirm('Yakin ingin menghapus siswa ' + name + '?')) {
                const form = document.getElementById('deleteStudentForm');
                form.action = url;
                form.submit();
            }
        }
    </script>
    @endpush

    {{-- Modal QR Viewer per Siswa --}}
    <div id="modalQrViewer" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div
            class="absolute inset-0 bg-black/60 backdrop-blur-sm"
            onclick="closeQrModal()"
        ></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-xs mx-4 p-6 z-10 text-center">
            {{-- Header --}}
            <div class="mb-4">
                <div id="qrStudentName" class="font-bold text-gray-900 dark:text-white text-lg"></div>
                <div id="qrStudentNis" class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">NIS: </div>
            </div>

            {{-- QR Image --}}
            <div class="flex items-center justify-center bg-white rounded-xl p-4 mb-5 border border-gray-100 shadow-inner">
                <img
                    id="qrImageEl"
                    src=""
                    alt="QR Code"
                    class="w-48 h-48 object-contain"
                >
            </div>

            {{-- Tombol --}}
            <div class="flex gap-2">
                @if(auth()->user()?->isAdmin())
                <a
                    id="qrDownloadLink"
                    href="#"
                    class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all"
                >
                    <i class="fas fa-download mr-1"></i> Download
                </a>
                @endif
                <button
                    type="button"
                    onclick="closeQrModal()"
                    class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Modal: Generate QR Massal --}}
    {{-- Dipanggil oleh tombol "Generate QR Massal" di header halaman --}}
    {{-- ============================================================ --}}
    <div id="modalBulkQR" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
             onclick="document.getElementById('modalBulkQR').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 z-10">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                        <i class="fas fa-qrcode text-teal-600 dark:text-teal-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Generate QR Massal</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Generate QR Code untuk semua siswa aktif</p>
                    </div>
                </div>
                <button onclick="document.getElementById('modalBulkQR').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            {{-- Form --}}
            <form action="{{ route('attendance.qr.bulk-generate') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    {{-- Opsi generate --}}
                    <div class="space-y-2">
                        <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all">
                            <input type="radio" name="only_missing" value="1" checked
                                   class="w-4 h-4 text-teal-600 focus:ring-teal-500">
                            <span class="ml-3 text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">Hanya yang belum punya QR</span>
                                <span class="text-gray-500 dark:text-gray-400 block text-xs mt-0.5">Lebih cepat — skip siswa yang sudah ada QR-nya</span>
                            </span>
                        </label>
                        <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all">
                            <input type="radio" name="only_missing" value="0"
                                   class="w-4 h-4 text-teal-600 focus:ring-teal-500">
                            <span class="ml-3 text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">Generate ulang semua</span>
                                <span class="text-gray-500 dark:text-gray-400 block text-xs mt-0.5">Regenerate QR seluruh siswa aktif (lebih lama)</span>
                            </span>
                        </label>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            <i class="fas fa-info-circle mr-1"></i>
                            Proses ini mungkin memerlukan beberapa detik tergantung jumlah siswa.
                        </p>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-2 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                            class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-teal-600 to-teal-700 hover:from-teal-700 hover:to-teal-800 transition-all shadow-md">
                        <i class="fas fa-qrcode mr-2"></i>Generate QR
                    </button>
                    <button type="button"
                            onclick="document.getElementById('modalBulkQR').classList.add('hidden')"
                            class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm text-gray-600 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- Modal: Cetak Kartu QR PDF --}}
    {{-- Konten ini sebelumnya tercecer tanpa wrapper modal --}}
    {{-- ============================================================ --}}
    <div id="modalQRCardsPDF" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
             onclick="document.getElementById('modalQRCardsPDF').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm mx-4 p-6 z-10">
            {{-- Header --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                        <i class="fas fa-id-card text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Cetak Kartu QR</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Download PDF kartu QR per kelas</p>
                    </div>
                </div>
                <button onclick="document.getElementById('modalQRCardsPDF').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form action="{{ route('attendance.students.card.generate') }}" method="POST">
                @csrf
                {{-- Select Kelas --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Kelas
                    </label>
                    <select
                        name="class_id"
                        class="w-full px-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 dark:bg-gray-700 dark:text-white text-sm transition-all"
                    >
                        <option value="">-- Semua Siswa Aktif --</option>
                        @foreach(\App\Models\AttendanceClass::orderBy('nama_kelas')->get() as $kelas)
                            <option value="{{ $kelas->id }}">
                                {{ $kelas->nama_kelas }} ({{ $kelas->students()->where('is_active', true)->count() }} siswa)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Select Layout --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Layout Kartu per Halaman
                    </label>
                    <div class="space-y-2">
                        <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all">
                            <input
                                type="radio"
                                name="layout"
                                value="3x3"
                                checked
                                class="w-4 h-4 text-red-600 focus:ring-red-500"
                            >
                            <span class="ml-3 text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">3x3 Layout</span>
                                <span class="text-gray-500 dark:text-gray-400 block text-xs mt-0.5">
                                    9 kartu per halaman (5cm x 6cm) - Ukuran besar, lebih jelas
                                </span>
                            </span>
                        </label>
                        <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all">
                            <input
                                type="radio"
                                name="layout"
                                value="4x4"
                                class="w-4 h-4 text-red-600 focus:ring-red-500"
                            >
                            <span class="ml-3 text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">4x4 Layout</span>
                                <span class="text-gray-500 dark:text-gray-400 block text-xs mt-0.5">
                                    16 kartu per halaman - Lebih kompak
                                </span>
                            </span>
                        </label>
                        <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all">
                            <input
                                type="radio"
                                name="layout"
                                value="6x6"
                                class="w-4 h-4 text-red-600 focus:ring-red-500"
                            >
                            <span class="ml-3 text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">6x6 Layout</span>
                                <span class="text-gray-500 dark:text-gray-400 block text-xs mt-0.5">
                                    36 kartu per halaman - Paling kompak, hemat kertas
                                </span>
                            </span>
                        </label>
                    </div>
                </div>

                {{-- Include Class Name --}}
                <div class="mb-6">
                    <label class="flex items-center p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all">
                        <input
                            type="checkbox"
                            name="include_class"
                            value="1"
                            class="w-4 h-4 text-red-600 rounded focus:ring-red-500"
                        >
                        <span class="ml-3 text-sm">
                            <span class="font-medium text-gray-900 dark:text-white">Tampilkan nama kelas di kartu</span>
                            <span class="text-gray-500 dark:text-gray-400 block text-xs mt-0.5">
                                Contoh: "Agus Setiawan / X-AKALBR"
                            </span>
                        </span>
                    </label>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-2 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button
                        type="submit"
                        class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 transition-all shadow-md"
                    >
                        <i class="fas fa-download mr-2"></i>
                        Download PDF
                    </button>
                    <button
                        type="button"
                        onclick="document.getElementById('modalQRCardsPDF').classList.add('hidden')"
                        class="flex-1 py-2.5 px-4 rounded-xl font-semibold text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                    >
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

{{-- ============================================================
     MODAL: Upload Foto Massal
     ============================================================ --}}
@if(auth()->user()?->isAdmin())
<div id="modalBulkFoto" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60" onclick="closeBulkFoto(event)">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-pink-700 flex items-center justify-center text-white">
                    <i class="fas fa-images"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Upload Foto Massal</h3>
                    <p class="text-xs text-gray-500">Nama file harus = NIS siswa (contoh: <code>12345.jpg</code>)</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalBulkFoto').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('attendance.students.bulk-foto') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf

            {{-- Info --}}
            <div class="p-3 bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-700 rounded-lg text-sm text-pink-800 dark:text-pink-200 space-y-1">
                <p>📌 <strong>Konvensi nama file:</strong> nama file = NIS siswa</p>
                <p>📎 Format: JPG, PNG, GIF &bull; Max 3MB per foto &bull; Max 200 foto</p>
                <p>🔄 Foto lama akan otomatis diganti</p>
            </div>

            {{-- Drop zone --}}
            <div
                id="bulkFotoDrop"
                class="border-2 border-dashed border-pink-300 dark:border-pink-600 rounded-xl p-8 text-center cursor-pointer hover:border-pink-500 hover:bg-pink-50 dark:hover:bg-pink-900/20 transition-all"
                onclick="document.getElementById('bulkFotoInput').click()"
                ondragover="event.preventDefault(); this.classList.add('border-pink-500','bg-pink-50')"
                ondragleave="this.classList.remove('border-pink-500','bg-pink-50')"
                ondrop="handleBulkFotoDrop(event)"
            >
                <i class="fas fa-cloud-upload-alt text-4xl text-pink-400 mb-3"></i>
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Klik atau seret foto ke sini</p>
                <p class="text-xs text-gray-400 mt-1" id="bulkFotoCount">Belum ada foto dipilih</p>
            </div>
            <input type="file" id="bulkFotoInput" name="fotos[]" multiple accept="image/*" class="hidden"
                onchange="updateBulkFotoCount(this.files)">

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <button type="button"
                    onclick="document.getElementById('modalBulkFoto').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" id="bulkFotoSubmit" disabled
                    class="px-4 py-2 text-sm font-semibold text-white bg-pink-600 hover:bg-pink-700 rounded-lg transition disabled:opacity-40 disabled:cursor-not-allowed">
                    <i class="fas fa-upload mr-1"></i> Upload Foto
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Hasil Bulk Foto --}}
@if(session('bulk_foto_result'))
    @php $bfr = session('bulk_foto_result'); @endphp
    <div id="bulkFotoResult" class="fixed bottom-4 right-4 z-50 w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 bg-gradient-to-r from-pink-500 to-pink-700 text-white">
            <span class="font-bold">📸 Hasil Upload Foto Massal</span>
            <button onclick="document.getElementById('bulkFotoResult').remove()" class="text-white/80 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 space-y-3 max-h-80 overflow-y-auto">
            <div class="flex gap-4 text-sm font-semibold">
                <span class="text-green-600">✅ Berhasil: {{ count($bfr['berhasil']) }}</span>
                <span class="text-red-500">❌ Gagal: {{ count($bfr['gagal']) }}</span>
                <span class="text-gray-500">Total: {{ $bfr['total'] }}</span>
            </div>
            @if(!empty($bfr['berhasil']))
                <div>
                    <p class="text-xs font-semibold text-green-700 dark:text-green-400 mb-1">Berhasil:</p>
                    @foreach($bfr['berhasil'] as $ok)
                        <p class="text-xs text-gray-600 dark:text-gray-300">✅ {{ $ok['nis'] }} — {{ $ok['nama'] }}</p>
                    @endforeach
                </div>
            @endif
            @if(!empty($bfr['gagal']))
                <div>
                    <p class="text-xs font-semibold text-red-600 dark:text-red-400 mb-1">Gagal:</p>
                    @foreach($bfr['gagal'] as $err)
                        <p class="text-xs text-red-500">❌ {{ $err['file'] }} — {{ $err['reason'] }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
@endif

@push('scripts')
<script>
function closeBulkFoto(e) {
    if (e.target === document.getElementById('modalBulkFoto')) {
        document.getElementById('modalBulkFoto').classList.add('hidden');
    }
}
function updateBulkFotoCount(files) {
    const count = files.length;
    document.getElementById('bulkFotoCount').textContent = count > 0 ? count + ' foto dipilih' : 'Belum ada foto dipilih';
    document.getElementById('bulkFotoSubmit').disabled = count === 0;
    // Sync input
    const dt = new DataTransfer();
    Array.from(files).forEach(f => dt.items.add(f));
    document.getElementById('bulkFotoInput').files = dt.files;
}
function handleBulkFotoDrop(e) {
    e.preventDefault();
    const drop = document.getElementById('bulkFotoDrop');
    drop.classList.remove('border-pink-500', 'bg-pink-50');
    const files = e.dataTransfer.files;
    updateBulkFotoCount(files);
}
</script>
@endpush
{{-- ============================================================
     MODAL: Download Kartu QR Massal (PHP GD + ZIP)
     ============================================================ --}}
@if(auth()->user()?->isAdmin())
<div id="modalBulkKartuQR" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/60" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md" onclick="event.stopPropagation()">
        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center text-white">
                    <i class="fas fa-id-card"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Download Kartu QR Massal</h3>
                    <p class="text-xs text-gray-500">Semua kartu dikemas dalam 1 file ZIP</p>
                </div>
            </div>
            <button onclick="document.getElementById('modalBulkKartuQR').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('attendance.students.bulk-qr-cards') }}" method="GET" class="p-5 space-y-4">
            {{-- Info --}}
            <div class="p-3 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg text-sm text-orange-800 dark:text-orange-200 space-y-1">
                <p>🖼️ Setiap kartu berisi: <strong>QR Code + Nama + NIS + Kelas + Foto</strong></p>
                <p>📦 Output: file <strong>.ZIP</strong> berisi PNG per siswa</p>
                <p>⏱️ Proses mungkin memakan waktu untuk kelas besar</p>
            </div>

            {{-- Filter Kelas --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Kelas</label>
                <select name="class_id" class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-orange-500">
                    <option value="">— Semua Kelas —</option>
                    @foreach(\App\Models\AttendanceClass::where('is_active', true)->orderBy('tingkat')->orderBy('nama_kelas')->get() as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-3">
                <button type="button"
                    onclick="document.getElementById('modalBulkKartuQR').classList.add('hidden')"
                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 text-sm font-semibold text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition">
                    <i class="fas fa-download mr-1"></i> Download ZIP
                </button>
            </div>
        </form>
    </div>
</div>
@endif

</x-app-layout>

