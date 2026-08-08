<x-app-layout>
    <x-slot name="title">Manajemen Pengguna</x-slot>
    <x-slot name="pageTitle">Manajemen Pengguna & Wali Kelas</x-slot>

    <div class="space-y-6">

        @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">
            <i class="fas fa-check-circle text-lg"></i><span>{{ session('success') }}</span>
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400">
            <i class="fas fa-exclamation-circle text-lg"></i><span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- Grid: Tabel users (kiri) + Form tambah (kanan) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Daftar Users --}}
            <div class="lg:col-span-2">
                <x-card>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-users text-indigo-500"></i> Daftar Pengguna
                    </h3>

                    {{-- Search & Filter --}}
                    <div class="flex flex-col sm:flex-row gap-3 mb-4">
                        <div class="flex-1 relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="userSearch" placeholder="Cari nama atau email..."
                                   class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                        </div>
                        <select id="roleFilter" 
                                class="px-3 py-2 text-sm border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Semua Role</option>
                            <option value="admin">Admin</option>
                            <option value="wali_kelas">Wali Kelas</option>
                        </select>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 rounded-l-lg">Nama</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 hidden sm:table-cell">Email</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Role</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Kelas</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 rounded-r-lg">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @foreach($users as $u)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 user-row" data-name="{{ strtolower($u->name) }}" data-email="{{ strtolower($u->email) }}" data-role="{{ $u->role }}">
                                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $u->name }}</td>
                                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs hidden sm:table-cell">{{ $u->email }}</td>
                                    <td class="px-4 py-3">
                                        @if($u->role === 'admin')
                                            <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs font-bold rounded-full">Admin</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 text-xs font-bold rounded-full">Wali Kelas</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                        {{ $u->kelas?->nama_kelas ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <button onclick="openEditModal({{ $u->id }}, '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->role }}', {{ $u->kelas_id ?? 'null' }})"
                                                    class="text-indigo-500 hover:text-indigo-700 text-xs font-medium">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if($u->id !== auth()->id())
                                            <form action="{{ route('attendance.users.destroy', $u) }}" method="POST"
                                                  onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-400 hover:text-red-600 text-xs">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
            </div>

            {{-- Form Tambah --}}
            <div>
                <x-card>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-user-plus text-teal-500"></i> Tambah Pengguna
                    </h3>
                    <form action="{{ route('attendance.users.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap</label>
                            <input type="text" name="name" required placeholder="Nama pengguna"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 outline-none">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input type="email" name="email" required placeholder="email@sekolah.sch.id"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 outline-none">
                            @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Password</label>
                            <input type="password" name="password" required placeholder="Min 6 karakter"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 outline-none">
                            @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Role</label>
                            <select name="role" id="addRole" onchange="toggleKelas('addKelas', this.value)"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 outline-none">
                                <option value="admin">Admin</option>
                                <option value="wali_kelas">Wali Kelas</option>
                            </select>
                        </div>

                        <div id="addKelas" class="hidden">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kelas yang Diampu</label>
                            <select name="kelas_id"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400 outline-none">
                                <option value="">Pilih Kelas</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition-all shadow">
                            <i class="fas fa-plus mr-2"></i>Tambah Pengguna
                        </button>
                    </form>
                </x-card>

                {{-- Info box --}}
                <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 text-xs text-blue-700 dark:text-blue-300">
                    <p class="font-semibold mb-1"><i class="fas fa-info-circle mr-1"></i>Info Role:</p>
                    <p class="mb-1"><strong>Admin:</strong> Akses penuh ke semua fitur</p>
                    <p><strong>Wali Kelas:</strong> Hanya bisa lihat data kelas yang diampu</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Edit Pengguna</h3>
            <form id="editForm" method="POST" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Nama</label>
                    <input type="text" name="name" id="editName" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Email</label>
                    <input type="email" name="email" id="editEmail" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Password Baru (kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Role</label>
                    <select name="role" id="editRole" onchange="toggleKelas('editKelas', this.value)" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="admin">Admin</option>
                        <option value="wali_kelas">Wali Kelas</option>
                    </select>
                </div>
                <div id="editKelas">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Kelas</label>
                    <select name="kelas_id" id="editKelasId" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-indigo-400">
                        <option value="">— Pilih Kelas —</option>
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}">{{ $cls->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleKelas(elId, role) {
            const el = document.getElementById(elId);
            el?.classList.toggle('hidden', role !== 'wali_kelas');
        }

        function openEditModal(id, name, email, role, kelasId) {
            document.getElementById('editForm').action = '/attendance/users/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editKelasId').value = kelasId || '';
            toggleKelas('editKelas', role);
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }
        // Search & Filter
        function filterUsers() {
            const q = document.getElementById('userSearch').value.toLowerCase();
            const role = document.getElementById('roleFilter').value;
            document.querySelectorAll('.user-row').forEach(row => {
                const name = row.dataset.name;
                const email = row.dataset.email;
                const r = row.dataset.role;
                const matchSearch = !q || name.includes(q) || email.includes(q);
                const matchRole = !role || r === role;
                row.style.display = (matchSearch && matchRole) ? '' : 'none';
            });
        }
        document.getElementById('userSearch').addEventListener('input', filterUsers);
        document.getElementById('roleFilter').addEventListener('change', filterUsers);
    </script>
    @endpush
</x-app-layout>
