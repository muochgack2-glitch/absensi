<x-app-layout>
    <x-slot name="title">Manajemen Izin Online</x-slot>
    <x-slot name="pageTitle">Izin Online Siswa</x-slot>

    <div class="space-y-5">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="flex items-center gap-3 px-4 py-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">
            <i class="fas fa-check-circle text-lg"></i><span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- Header filter --}}
        <x-card>
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white text-xl">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">Pengajuan Izin Masuk</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            <span class="font-semibold text-yellow-600">{{ $countPending }}</span> pengajuan menunggu persetujuan
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    {{-- Filter status --}}
                    <form method="GET" action="{{ route('attendance.izin.index') }}" class="flex items-center gap-2">
                        <select name="status" onchange="this.form.submit()"
                                class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300">
                            <option value="pending"   {{ $status === 'pending'   ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>✅ Disetujui</option>
                            <option value="ditolak"   {{ $status === 'ditolak'   ? 'selected' : '' }}>❌ Ditolak</option>
                            <option value="all"       {{ $status === 'all'       ? 'selected' : '' }}>📋 Semua</option>
                        </select>
                        <input type="hidden" name="class_id" value="{{ $classId }}">
                    </form>

                    <a href="{{ route('izin.form') }}" target="_blank"
                       class="inline-flex items-center px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all">
                        <i class="fas fa-external-link-alt mr-1.5"></i>Form Publik
                    </a>
                </div>
            </div>
        </x-card>

        {{-- Tabel --}}
        <x-card>
            @if($izinList->isEmpty())
            <div class="text-center py-14 text-gray-400 dark:text-gray-500">
                <i class="fas fa-file-medical text-4xl mb-3 block opacity-30"></i>
                <p>Tidak ada pengajuan dengan status <strong>{{ $status }}</strong></p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 rounded-l-lg">Siswa</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400">Jenis</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400">Alasan</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400">Pelapor</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($izinList as $izin)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-900 dark:text-white text-sm">{{ $izin->student->nama }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $izin->student->nis }} • {{ $izin->student->kelas->nama_kelas ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @if($izin->jenis === 'sakit')
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 text-xs font-semibold rounded-full">
                                        <i class="fas fa-briefcase-medical mr-1"></i>Sakit
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-semibold rounded-full">
                                        <i class="fas fa-calendar-times mr-1"></i>Izin
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                <div>{{ $izin->tanggal_mulai->format('d M Y') }}</div>
                                @if($izin->tanggal_mulai != $izin->tanggal_selesai)
                                    <div class="text-gray-400">s/d {{ $izin->tanggal_selesai->format('d M Y') }}</div>
                                @endif
                                <div class="text-indigo-500 font-medium">{{ $izin->durasi }} hari</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400 max-w-xs">
                                <p class="line-clamp-2">{{ $izin->alasan }}</p>
                                @if($izin->lampiran)
                                    <a href="{{ Storage::url($izin->lampiran) }}" target="_blank"
                                       class="text-indigo-500 hover:text-indigo-700 mt-1 inline-flex items-center gap-1">
                                        <i class="fas fa-paperclip text-xs"></i>Lampiran
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-400">
                                <p class="font-medium">{{ $izin->nama_pelapor }}</p>
                                <p>{{ $izin->no_hp_pelapor }}</p>
                                <p class="text-gray-400">{{ $izin->created_at->format('d M H:i') }}</p>
                            </td>
                            <td class="px-4 py-3">
                                @php $c = $izin->status_color; @endphp
                                <span class="px-2 py-1 text-xs font-bold rounded-full
                                    bg-{{ $c }}-100 dark:bg-{{ $c }}-900/30 text-{{ $c }}-700 dark:text-{{ $c }}-400">
                                    {{ $izin->status_label }}
                                </span>
                                @if($izin->catatan_admin)
                                    <p class="text-xs text-gray-400 mt-1 italic">{{ $izin->catatan_admin }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($izin->status === 'pending')
                                <div class="flex flex-col gap-1.5 min-w-max">
                                    {{-- Setujui --}}
                                    <form action="{{ route('attendance.izin.approve', $izin) }}" method="POST"
                                          onsubmit="return confirm('Setujui izin {{ $izin->student->nama }}?')">
                                        @csrf
                                        <button type="submit"
                                                class="w-full px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition-all">
                                            <i class="fas fa-check mr-1"></i>Setujui
                                        </button>
                                    </form>

                                    {{-- Tolak --}}
                                    <button type="button"
                                            onclick="openRejectModal({{ $izin->id }}, '{{ addslashes($izin->student->nama) }}')"
                                            class="w-full px-3 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-xs font-semibold rounded-lg transition-all">
                                        <i class="fas fa-times mr-1"></i>Tolak
                                    </button>
                                </div>
                                @else
                                <span class="text-xs text-gray-400">
                                    {{ $izin->approvedBy?->name ?? '-' }}<br>
                                    {{ $izin->approved_at?->format('d M H:i') }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $izinList->withQueryString()->links() }}</div>
            @endif
        </x-card>
    </div>

    {{-- Modal Tolak --}}
    <div id="rejectModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Tolak Pengajuan Izin</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="rejectStudentName">-</p>

            <form id="rejectForm" method="POST">
                @csrf
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Alasan Penolakan <span class="text-red-500">*</span>
                </label>
                <textarea name="catatan_admin" rows="3" required placeholder="Tulis alasan penolakan..."
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-red-400 outline-none mb-4"></textarea>
                <div class="flex gap-3">
                    <button type="button" onclick="closeRejectModal()"
                            class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition-all">
                        <i class="fas fa-times mr-1"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openRejectModal(izinId, studentName) {
            document.getElementById('rejectStudentName').textContent = 'Siswa: ' + studentName;
            document.getElementById('rejectForm').action = '/attendance/izin/' + izinId + '/reject';
            document.getElementById('rejectModal').classList.remove('hidden');
            document.getElementById('rejectModal').classList.add('flex');
        }
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectModal').classList.remove('flex');
        }
    </script>
    @endpush
</x-app-layout>
