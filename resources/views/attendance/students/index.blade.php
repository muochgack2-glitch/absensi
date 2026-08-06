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
                    <a
                        href="{{ route('attendance.students.export.template') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-green-500 to-green-600 text-white hover:from-green-600 hover:to-green-700 shadow-md hover:shadow-lg hover:-translate-y-0.5"
                    >
                        <i class="fas fa-file-excel mr-2"></i>
                        Download Template
                    </a>

                    <a
                        href="{{ route('attendance.students.import.form') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-blue-500 to-blue-600 text-white hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg hover:-translate-y-0.5"
                    >
                        <i class="fas fa-file-import mr-2"></i>
                        Import Excel
                    </a>

                    <a
                        href="{{ route('attendance.students.card') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-purple-500 to-purple-600 text-white hover:from-purple-600 hover:to-purple-700 shadow-md hover:shadow-lg hover:-translate-y-0.5"
                    >
                        <i class="fas fa-id-card mr-2"></i>
                        Cetak Kartu
                    </a>
                </div>

                {{-- Baris 2: Generate QR Massal + Tambah Siswa --}}
                <div class="flex flex-wrap gap-2">
                    <button
                        type="button"
                        onclick="document.getElementById('modalBulkQR').classList.remove('hidden')"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-green-600 to-green-700 text-white hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg hover:-translate-y-0.5"
                    >
                        <i class="fas fa-qrcode mr-2"></i>
                        Generate QR Massal
                    </button>

                    <a
                        href="{{ route('attendance.students.create') }}"
                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 shadow-md hover:shadow-lg hover:-translate-y-0.5"
                    >
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Siswa
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                title="QR Code Dibuat"
                :value="\App\Models\AttendanceStudent::whereNotNull('qr_code_path')->count()"
                icon="fas fa-qrcode"
                color="purple"
            />
        </div>

        {{-- Search & Filter Card --}}
        <x-card>
            <form method="GET" action="{{ route('attendance.students.index') }}" class="space-y-4" id="filterForm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                </div>
                
                <div class="flex justify-end gap-2">
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
            // Debounce function untuk search input
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const filterForm = document.getElementById('filterForm');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        filterForm.submit();
                    }, 500); // Submit setelah 500ms user berhenti mengetik
                });
            }
        </script>
        @endpush

        {{-- Students Table --}}
        <x-section-card title="Daftar Siswa">
            <x-table>
                <x-slot name="header">
                    <x-table.header>Foto</x-table.header>
                    <x-table.header>NIS</x-table.header>
                    <x-table.header>Nama</x-table.header>
                    <x-table.header>Kelas</x-table.header>
                    <x-table.header>No HP Ortu</x-table.header>
                    <x-table.header>QR Code</x-table.header>
                    <x-table.header>Status</x-table.header>
                    <x-table.header>Aksi</x-table.header>
                </x-slot>

                @forelse($students as $student)
                    <x-table.row>
                        {{-- Photo --}}
                        <x-table.cell>
                            @if($student->foto_profil)
                                <img 
                                    src="{{ asset('storage/' . $student->foto_profil) }}" 
                                    alt="{{ $student->nama }}"
                                    class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-600"
                                >
                            @else
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white text-sm font-semibold">
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
                        
                        {{-- Parent Phone --}}
                        <x-table.cell>
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $student->no_hp_ortu ?? '-' }}
                            </span>
                        </x-table.cell>
                        
                        {{-- QR Code --}}
                        <x-table.cell>
                            @if($student->qr_code_path)
                                <a 
                                    href="{{ route('attendance.qr.show', $student->nis) }}" 
                                    class="inline-flex items-center text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium"
                                    target="_blank"
                                >
                                    <i class="fas fa-qrcode mr-1"></i>
                                    Lihat
                                </a>
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
                                {{-- Print QR Kartu --}}
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
                                
                                {{-- Delete --}}
                                <form 
                                    method="POST" 
                                    action="{{ route('attendance.students.destroy', $student->id) }}" 
                                    onsubmit="return confirm('Yakin ingin menghapus siswa {{ $student->nama }}?')"
                                    class="inline"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit" 
                                        class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300"
                                        title="Hapus"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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
                                    <a
                                        href="{{ route('attendance.students.create') }}"
                                        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 shadow-md hover:shadow-lg mt-4"
                                    >
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah Siswa Pertama
                                    </a>
                                </x-slot>
                            </x-empty-state>
                        </x-table.cell>
                    </x-table.row>
                @endforelse
            </x-table>
            
            {{-- Pagination --}}
            @if($students->hasPages())
                <div class="mt-6">
                    {{ $students->links() }}
                </div>
            @endif
        </x-section-card>
    </div>

    {{-- Modal Bulk Generate QR --}}
    <div id="modalBulkQR" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        {{-- Backdrop --}}
        <div
            class="absolute inset-0 bg-black/50 backdrop-blur-sm"
            onclick="document.getElementById('modalBulkQR').classList.add('hidden')"
        ></div>

        {{-- Modal Box --}}
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 z-10">
            {{-- Icon --}}
            <div class="flex items-center justify-center w-14 h-14 rounded-full bg-green-100 dark:bg-green-900/40 mx-auto mb-4">
                <i class="fas fa-qrcode text-2xl text-green-600 dark:text-green-400"></i>
            </div>

            <h3 class="text-lg font-bold text-center text-gray-900 dark:text-white mb-1">Generate QR Code Massal</h3>
            <p class="text-sm text-center text-gray-500 dark:text-gray-400 mb-6">
                Pilih mode generate untuk semua siswa aktif.
            </p>

            {{-- Statistik --}}
            <div class="grid grid-cols-2 gap-3 mb-6">
                <div class="text-center p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ \App\Models\AttendanceStudent::where('is_active', true)->count() }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total Siswa Aktif</div>
                </div>
                <div class="text-center p-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/20">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ \App\Models\AttendanceStudent::where('is_active', true)->whereNull('qr_code_path')->count() }}
                    </div>
                    <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-0.5">Belum Ada QR</div>
                </div>
            </div>

            {{-- Form: hanya yang belum ada --}}
            <form method="POST" action="{{ route('attendance.qr.bulk-generate') }}" id="formBulkMissing">
                @csrf
                <input type="hidden" name="only_missing" value="1">
            </form>

            {{-- Form: generate ulang semua --}}
            <form method="POST" action="{{ route('attendance.qr.bulk-generate') }}" id="formBulkAll">
                @csrf
                <input type="hidden" name="only_missing" value="0">
            </form>

            {{-- Tombol Aksi --}}
            <div class="space-y-2">
                <button
                    type="submit"
                    form="formBulkMissing"
                    class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition-all shadow-md"
                >
                    <i class="fas fa-plus-circle mr-2"></i>
                    Generate Yang Belum Ada
                </button>
                <button
                    type="submit"
                    form="formBulkAll"
                    onclick="return confirm('Generate ulang QR untuk SEMUA siswa aktif? File QR lama akan ditimpa.')"
                    class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/40 border border-green-200 dark:border-green-700 transition-all"
                >
                    <i class="fas fa-redo mr-2"></i>
                    Generate Ulang Semua
                </button>
                <button
                    type="button"
                    onclick="document.getElementById('modalBulkQR').classList.add('hidden')"
                    class="w-full py-2.5 px-4 rounded-xl font-semibold text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all"
                >
                    Batal
                </button>
            </div>
        </div>
    </div>

</x-app-layout>
