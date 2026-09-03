<x-app-layout>
    <x-slot name="title">Manajemen Pengguna</x-slot>
    <x-slot name="pageTitle">Manajemen Pengguna</x-slot>

    @php
        $roleStats = [
            'admin'          => ['label'=>'Admin',          'icon'=>'fa-shield-halved',       'bg'=>'bg-purple-100 dark:bg-purple-900/40', 'text'=>'text-purple-600 dark:text-purple-300', 'stat'=>'text-purple-700 dark:text-purple-300', 'count'=>$users->where('role','admin')->count()],
            'wali_kelas'     => ['label'=>'Wali Kelas',     'icon'=>'fa-chalkboard-teacher',  'bg'=>'bg-blue-100 dark:bg-blue-900/40',   'text'=>'text-blue-600 dark:text-blue-300',   'stat'=>'text-blue-700 dark:text-blue-300',   'count'=>$users->where('role','wali_kelas')->count()],
            'petugas'        => ['label'=>'Petugas',        'icon'=>'fa-id-card',             'bg'=>'bg-green-100 dark:bg-green-900/40', 'text'=>'text-green-600 dark:text-green-300', 'stat'=>'text-green-700 dark:text-green-300', 'count'=>$users->where('role','petugas')->count()],
            'kepala_sekolah' => ['label'=>'Kepala Sekolah', 'icon'=>'fa-user-tie',            'bg'=>'bg-yellow-100 dark:bg-yellow-900/40','text'=>'text-yellow-700 dark:text-yellow-300','stat'=>'text-yellow-700 dark:text-yellow-300','count'=>$users->where('role','kepala_sekolah')->count()],
            'waka_kesiswaan' => ['label'=>'Waka Kesiswaan', 'icon'=>'fa-briefcase',           'bg'=>'bg-orange-100 dark:bg-orange-900/40','text'=>'text-orange-600 dark:text-orange-300','stat'=>'text-orange-700 dark:text-orange-300','count'=>$users->where('role','waka_kesiswaan')->count()],
            'guru_bk'        => ['label'=>'Guru BK',        'icon'=>'fa-heart',               'bg'=>'bg-pink-100 dark:bg-pink-900/40',   'text'=>'text-pink-600 dark:text-pink-300',   'stat'=>'text-pink-700 dark:text-pink-300',   'count'=>$users->where('role','guru_bk')->count()],
        ];
        $roleColors = [
            'admin'          => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
            'wali_kelas'     => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
            'petugas'        => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
            'kepala_sekolah' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
            'waka_kesiswaan' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
            'guru_bk'        => 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300',
        ];
        $roleLabels = [
            'admin'=>'Admin','wali_kelas'=>'Wali Kelas','petugas'=>'Petugas',
            'kepala_sekolah'=>'Kepala Sekolah','waka_kesiswaan'=>'Waka Kesiswaan','guru_bk'=>'Guru BK',
        ];
        $avatarColors = [
            'admin'          => 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300',
            'wali_kelas'     => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
            'petugas'        => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300',
            'kepala_sekolah' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300',
            'waka_kesiswaan' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300',
            'guru_bk'        => 'bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300',
        ];
    @endphp

    {{-- Flash / Validation --}}
    @if(session('success'))
    <div id="toast-ok" class="flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl text-sm text-green-700 dark:text-green-400 shadow-sm animate-fade-in">
        <div class="w-7 h-7 rounded-full bg-green-100 dark:bg-green-800 flex items-center justify-center flex-shrink-0"><i class="fas fa-check text-green-600 dark:text-green-300 text-xs"></i></div>
        <span class="font-medium">{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto text-green-400 hover:text-green-600"><i class="fas fa-times text-xs"></i></button>
    </div>
    @endif
    @if(session('error'))
    <div class="flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-sm text-red-700 dark:text-red-400 shadow-sm">
        <div class="w-7 h-7 rounded-full bg-red-100 dark:bg-red-800 flex items-center justify-center flex-shrink-0"><i class="fas fa-exclamation text-red-600 dark:text-red-300 text-xs"></i></div>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif
    @if($errors->any())
    <div class="px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-sm text-red-700 dark:text-red-400 shadow-sm">
        <div class="flex items-center gap-2 font-semibold mb-1.5"><i class="fas fa-triangle-exclamation"></i> Gagal menyimpan:</div>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        {{-- Total --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center text-center col-span-3 lg:col-span-1">
            <div class="w-11 h-11 rounded-xl bg-indigo-100 dark:bg-indigo-800/60 flex items-center justify-center text-indigo-600 dark:text-indigo-200 mb-2 shadow-sm">
                <i class="fas fa-users text-sm"></i>
            </div>
            <p class="text-3xl font-black text-gray-900 dark:text-white leading-none">{{ $users->count() }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">Total User</p>
        </div>
        {{-- Per role --}}
        @foreach($roleStats as $roleKey => $rs)
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm flex flex-col items-center text-center cursor-pointer hover:border-indigo-300 dark:hover:border-indigo-600 transition-all"
             onclick="filterByRole('{{ $roleKey }}')">
            <div class="w-11 h-11 rounded-xl {{ $rs['bg'] }} {{ $rs['text'] }} flex items-center justify-center mb-2 shadow-sm">
                <i class="fas {{ $rs['icon'] }} text-sm"></i>
            </div>
            <p class="text-3xl font-black {{ $rs['stat'] }} leading-none">{{ $rs['count'] }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1 leading-tight">{{ $rs['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Main Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm overflow-hidden">

        {{-- Card Header --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <i class="fas fa-users text-indigo-500"></i> Daftar Pengguna
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Kelola akun dan hak akses seluruh pengguna sistem</p>
            </div>
            <button onclick="openModal('add')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl text-sm font-semibold shadow-sm transition-all duration-150 whitespace-nowrap flex-shrink-0">
                <i class="fas fa-user-plus"></i>
                Tambah Pengguna
            </button>
        </div>

        {{-- Search + Filter --}}
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            {{-- Search --}}
            <div class="relative flex-1 w-full sm:max-w-xs">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="userSearch" placeholder="Cari nama atau email..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
            </div>
            {{-- Role filter pills --}}
            <div class="flex flex-wrap gap-2" id="rolePills">
                <button onclick="filterByRole('')" data-role=""
                        class="role-pill px-3 py-1.5 rounded-full text-xs font-semibold transition-all border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-indigo-400 bg-white dark:bg-gray-800 active-pill">
                    Semua
                </button>
                @foreach($roleStats as $roleKey => $rs)
                <button onclick="filterByRole('{{ $roleKey }}')" data-role="{{ $roleKey }}"
                        class="role-pill px-3 py-1.5 rounded-full text-xs font-semibold transition-all border border-gray-200 dark:border-gray-700 {{ $rs['text'] }} hover:border-current bg-white dark:bg-gray-800">
                    {{ $rs['label'] }}
                    <span class="ml-1 opacity-60">({{ $rs['count'] }})</span>
                </button>
                @endforeach
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50/70 dark:bg-gray-900/30">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden md:table-cell">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden sm:table-cell">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider hidden lg:table-cell">Kontak</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50" id="userTableBody">
                    @forelse($users as $u)
                    @php
                        $initials = collect(explode(' ', $u->name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
                        $ac = $avatarColors[$u->role] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300';
                    @endphp
                    <tr class="user-row hover:bg-indigo-50/30 dark:hover:bg-indigo-900/10 transition-colors duration-150"
                        data-name="{{ strtolower($u->name) }}"
                        data-email="{{ strtolower($u->email) }}"
                        data-role="{{ $u->role }}">

                        {{-- Pengguna (avatar + nama) --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl {{ $ac }} flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white text-sm leading-tight">{{ $u->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 md:hidden mt-0.5">{{ $u->email }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 hidden md:table-cell">{{ $u->email }}</td>

                        {{-- Role badge --}}
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $roleColors[$u->role] ?? 'bg-gray-100 text-gray-600' }}">
                                <i class="fas {{ $roleStats[$u->role]['icon'] ?? 'fa-user' }} text-[10px]"></i>
                                {{ $roleLabels[$u->role] ?? $u->role }}
                            </span>
                        </td>

                        {{-- Kelas --}}
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 hidden sm:table-cell">
                            @if($u->kelas)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg text-xs font-medium">
                                <i class="fas fa-door-open text-[10px]"></i> {{ $u->kelas->nama_kelas }}
                            </span>
                            @else
                            <span class="text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Kontak / WA --}}
                        <td class="px-6 py-4 text-xs hidden lg:table-cell">
                            @if($u->role === 'wali_kelas')
                                @if($u->phone)
                                    <div class="flex items-center gap-1.5">
                                        <i class="fab fa-whatsapp text-green-500"></i>
                                        <span class="text-gray-700 dark:text-gray-300 font-mono">{{ $u->phone }}</span>
                                        <form action="{{ route('attendance.users.regenerate-code', $u) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Reset WA {{ $u->name }}? Wali kelas harus daftar ulang.')">
                                            @csrf
                                            <button type="submit" class="text-gray-400 hover:text-red-500 transition" title="Reset & buat kode baru">
                                                <i class="fas fa-rotate text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                @elseif($u->verification_code)
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 font-mono font-bold rounded-lg">{{ $u->verification_code }}</span>
                                        <form action="{{ route('attendance.users.regenerate-code', $u) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Buat kode baru untuk {{ $u->name }}?')">
                                            @csrf
                                            <button type="submit" class="text-indigo-400 hover:text-indigo-600 transition" title="Buat kode baru">
                                                <i class="fas fa-rotate text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <form action="{{ route('attendance.users.regenerate-code', $u) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Buat kode verifikasi untuk {{ $u->name }}?')">
                                        @csrf
                                        <button type="submit" class="text-indigo-500 hover:text-indigo-700 font-medium text-xs">
                                            <i class="fas fa-plus mr-1"></i>Buat kode
                                        </button>
                                    </form>
                                @endif
                            @else
                                @if($u->phone)
                                    <div class="flex items-center gap-1.5">
                                        <i class="fab fa-whatsapp text-green-500"></i>
                                        <span class="text-gray-700 dark:text-gray-300 font-mono">{{ $u->phone }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-1">
                                <button onclick="openModal('edit', {{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->role }}', {{ $u->kelas_id ?? 'null' }}, '{{ $u->phone ?? '' }}')"
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 hover:text-indigo-700 transition-all"
                                        title="Edit">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                                @if($u->id !== auth()->id())
                                <form action="{{ route('attendance.users.destroy', $u) }}" method="POST"
                                      onsubmit="return confirm('Hapus akun {{ addslashes($u->name) }}? Tindakan ini tidak dapat dibatalkan.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 transition-all"
                                            title="Hapus">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-400">
                                <i class="fas fa-users text-4xl opacity-30"></i>
                                <p class="text-sm font-medium">Belum ada pengguna</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Empty search state --}}
        <div id="emptySearch" class="hidden px-6 py-16 text-center">
            <div class="flex flex-col items-center gap-3 text-gray-400">
                <i class="fas fa-search text-4xl opacity-30"></i>
                <p class="text-sm font-medium">Tidak ada pengguna yang cocok</p>
                <button onclick="clearSearch()" class="text-indigo-500 hover:text-indigo-700 text-sm font-medium">Reset pencarian</button>
            </div>
        </div>

        {{-- Info footer --}}
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/20">
            <div class="flex flex-wrap gap-x-6 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                <span><i class="fas fa-shield-halved text-purple-500 mr-1"></i><b>Admin:</b> Akses penuh</span>
                <span><i class="fas fa-briefcase text-orange-500 mr-1"></i><b>Waka:</b> Operasional + Data Siswa</span>
                <span><i class="fas fa-id-card text-green-500 mr-1"></i><b>Petugas:</b> Operasional & Kamera</span>
                <span><i class="fas fa-user-tie text-yellow-500 mr-1"></i><b>Kepala:</b> Dashboard & Laporan (view)</span>
                <span><i class="fas fa-chalkboard-teacher text-blue-500 mr-1"></i><b>Wali Kelas:</b> Data kelas sendiri</span>
            </div>
        </div>
    </div>

    {{-- ===================== UNIFIED MODAL ===================== --}}
    <div id="userModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal()"></div>

        {{-- Panel --}}
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto animate-modal">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div id="modalIconBg" class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white shadow-sm">
                        <i id="modalIcon" class="fas fa-user-plus text-sm"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-gray-900 dark:text-white">Tambah Pengguna</h3>
                        <p id="modalSubtitle" class="text-xs text-gray-500 dark:text-gray-400">Buat akun baru</p>
                    </div>
                </div>
                <button onclick="closeModal()" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 transition-all">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            {{-- Form --}}
            <form id="userForm" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <span id="methodField"></span>

                {{-- Nama --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="modalName" required placeholder="Nama pengguna"
                           class="w-full px-3.5 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm bg-white dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition placeholder-gray-400">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="modalEmail" required placeholder="email@sekolah.sch.id"
                           class="w-full px-3.5 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm bg-white dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition placeholder-gray-400">
                </div>

                {{-- Phone + Password row --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            Nomor WA <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <input type="text" name="phone" id="modalPhone" placeholder="081234567890"
                               class="w-full px-3.5 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm bg-white dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition placeholder-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            <span id="passLabel">Password</span> <span class="text-red-500" id="passRequired">*</span>
                        </label>
                        <input type="password" name="password" id="modalPassword" placeholder="Min 6 karakter"
                               class="w-full px-3.5 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm bg-white dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition placeholder-gray-400">
                    </div>
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Role <span class="text-red-500">*</span></label>
                    <select name="role" id="modalRole" onchange="handleRoleChange()" required
                            class="w-full px-3.5 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm bg-white dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition">
                        <option value="admin">👑 Admin — Akses penuh</option>
                        <option value="waka_kesiswaan">💼 Waka Kesiswaan — Operasional + Siswa</option>
                        <option value="petugas">🪪 Petugas — Operasional & Kamera</option>
                        <option value="kepala_sekolah">👔 Kepala Sekolah — Dashboard & Laporan</option>
                        <option value="wali_kelas">📋 Wali Kelas — Data kelas sendiri</option>
                        <option value="guru_bk">🎓 Guru BK — Monitoring siswa bermasalah</option>
                    </select>
                </div>

                {{-- Kelas (hanya wali_kelas) --}}
                <div id="kelasField" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Kelas yang Diampu</label>
                    <select name="kelas_id" id="modalKelasId"
                            class="w-full px-3.5 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm bg-white dark:bg-gray-900/50 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 outline-none transition">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeModal()"
                            class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </button>
                    <button type="submit" id="modalSubmitBtn"
                            class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-xl text-sm font-semibold shadow-sm transition-all duration-150">
                        <i class="fas fa-check mr-1.5"></i><span id="modalSubmitText">Tambah</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to   { opacity: 1; transform: scale(1)   translateY(0); }
        }
        .animate-modal { animation: modalIn 0.2s ease-out; }
        .role-pill { transition: all 0.15s ease; }
        .role-pill.active-pill {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white !important;
            border-color: #6366f1 !important;
        }
    </style>

    @push('scripts')
    <script>
        // ===== MODAL =====
        function openModal(mode, id, name, email, role, kelasId, phone) {
            const modal = document.getElementById('userModal');
            const form  = document.getElementById('userForm');

            if (mode === 'add') {
                // Reset form
                form.action = '{{ route("attendance.users.store") }}';
                document.getElementById('methodField').innerHTML = '';
                document.getElementById('modalName').value    = '';
                document.getElementById('modalEmail').value   = '';
                document.getElementById('modalPhone').value   = '';
                document.getElementById('modalPassword').value= '';
                document.getElementById('modalRole').value    = 'admin';
                document.getElementById('modalKelasId').value = '';
                document.getElementById('kelasField').classList.add('hidden');
                // UI
                document.getElementById('modalTitle').textContent    = 'Tambah Pengguna';
                document.getElementById('modalSubtitle').textContent = 'Buat akun baru';
                document.getElementById('modalIcon').className        = 'fas fa-user-plus text-sm';
                document.getElementById('modalSubmitText').textContent= 'Tambah';
                document.getElementById('passRequired').style.display = '';
                document.getElementById('passLabel').textContent      = 'Password';
                document.getElementById('modalPassword').required     = true;
            } else {
                // Edit mode
                form.action = '/attendance/users/' + id;
                document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
                document.getElementById('modalName').value    = name;
                document.getElementById('modalEmail').value   = email;
                document.getElementById('modalPhone').value   = phone || '';
                document.getElementById('modalPassword').value= '';
                document.getElementById('modalRole').value    = role;
                document.getElementById('modalKelasId').value = kelasId || '';
                document.getElementById('kelasField').classList.toggle('hidden', role !== 'wali_kelas');
                // UI
                document.getElementById('modalTitle').textContent    = 'Edit Pengguna';
                document.getElementById('modalSubtitle').textContent = name;
                document.getElementById('modalIcon').className        = 'fas fa-pen text-sm';
                document.getElementById('modalSubmitText').textContent= 'Simpan';
                document.getElementById('passRequired').style.display = 'none';
                document.getElementById('passLabel').textContent      = 'Password Baru';
                document.getElementById('modalPassword').required     = false;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            const modal = document.getElementById('userModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        function handleRoleChange() {
            const role = document.getElementById('modalRole').value;
            document.getElementById('kelasField').classList.toggle('hidden', role !== 'wali_kelas');
        }

        // ESC to close
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        // ===== SEARCH & FILTER =====
        let activeRole = '';

        function filterByRole(role) {
            activeRole = role;
            // Update pills
            document.querySelectorAll('.role-pill').forEach(pill => {
                const isActive = pill.dataset.role === role;
                pill.classList.toggle('active-pill', isActive);
            });
            // Sync select (legacy)
            applyFilter();
        }

        function applyFilter() {
            const q    = document.getElementById('userSearch').value.toLowerCase().trim();
            const rows = document.querySelectorAll('.user-row');
            let visible = 0;
            rows.forEach(row => {
                const name  = row.dataset.name;
                const email = row.dataset.email;
                const role  = row.dataset.role;
                const matchSearch = !q || name.includes(q) || email.includes(q);
                const matchRole   = !activeRole || role === activeRole;
                const show = matchSearch && matchRole;
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            document.getElementById('emptySearch').classList.toggle('hidden', visible > 0 || rows.length === 0);
        }

        function clearSearch() {
            document.getElementById('userSearch').value = '';
            activeRole = '';
            document.querySelectorAll('.role-pill').forEach((p, i) => p.classList.toggle('active-pill', i === 0));
            applyFilter();
        }

        document.getElementById('userSearch').addEventListener('input', applyFilter);

        // Auto-dismiss success toast
        setTimeout(() => {
            const t = document.getElementById('toast-ok');
            if (t) t.style.transition='opacity 0.5s', t.style.opacity='0', setTimeout(()=>t.remove(), 500);
        }, 4000);
    </script>
    @endpush
</x-app-layout>
