<x-app-layout>
    <x-slot name="title">Tahun Ajaran</x-slot>
    <x-slot name="pageTitle">Manajemen Tahun Ajaran</x-slot>

    <div class="space-y-6">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-800 dark:text-green-300">
            <i class="fas fa-check-circle text-xl text-green-500"></i>
            <div>
                <p class="font-semibold text-sm">Berhasil!</p>
                <p class="text-xs mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="flex items-center gap-3 px-5 py-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-800 dark:text-red-300">
            <i class="fas fa-exclamation-circle text-xl text-red-500"></i>
            <div>
                <p class="font-semibold text-sm">Gagal!</p>
                <p class="text-xs mt-0.5">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        {{-- TAHUN AJARAN AKTIF --}}
        <x-card>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-2xl shadow-lg">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Tahun Ajaran Aktif</p>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $activeTahun }}</h2>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="document.getElementById('modalNaikKelas').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl font-semibold text-sm transition-all shadow">
                        <i class="fas fa-level-up-alt"></i>
                        Naik Kelas
                    </button>
                    <button onclick="document.getElementById('modalBuatBaru').classList.remove('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-xl font-semibold text-sm transition-all shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus"></i>
                        Tahun Baru
                    </button>
                </div>
            </div>
        </x-card>

        {{-- DAFTAR TAHUN AJARAN --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($tahunList as $ta)
            <div class="rounded-2xl border {{ $ta->isActive() ? 'border-indigo-300 dark:border-indigo-700 bg-indigo-50/50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }} p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $ta->tahun }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            @if($ta->started_at) Mulai: {{ $ta->started_at->format('d M Y') }} @endif
                        </p>
                    </div>
                    @if($ta->isActive())
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 text-xs font-bold rounded-full">
                            <i class="fas fa-check-circle mr-1"></i>AKTIF
                        </span>
                    @else
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs font-bold rounded-full">
                            <i class="fas fa-archive mr-1"></i>ARSIP
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div class="bg-white dark:bg-gray-900/50 rounded-xl p-3 text-center border border-gray-100 dark:border-gray-700">
                        <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ $ta->stats['total_siswa'] ?? 0 }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Siswa</p>
                    </div>
                    <div class="bg-white dark:bg-gray-900/50 rounded-xl p-3 text-center border border-gray-100 dark:border-gray-700">
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($ta->stats['total_record'] ?? 0) }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Record</p>
                    </div>
                </div>

                @if(!$ta->isActive())
                <form method="POST" action="{{ route('attendance.tahun-ajaran.activate', $ta) }}"
                      onsubmit="return confirm('Aktifkan tahun ajaran {{ $ta->tahun }}?')">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-all">
                        <i class="fas fa-power-off mr-1"></i> Aktifkan
                    </button>
                </form>
                @endif
            </div>
            @endforeach
        </div>

        {{-- SISWA ALUMNI / LULUS --}}
        @if($alumni->isNotEmpty())
        <x-card>
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-graduation-cap mr-2 text-amber-500"></i>Alumni / Lulus
                </h3>
                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full text-xs font-bold">
                    {{ $alumni->count() }} siswa
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left">
                            <th class="py-2 px-2 text-gray-500 dark:text-gray-400 font-medium text-xs">NIS</th>
                            <th class="py-2 px-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Nama</th>
                            <th class="py-2 px-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Kelas Terakhir</th>
                            <th class="py-2 px-2 text-gray-500 dark:text-gray-400 font-medium text-xs">Tahun Ajaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alumni->take(20) as $alu)
                        <tr class="border-b border-gray-50 dark:border-gray-800">
                            <td class="py-2 px-2 text-gray-600 dark:text-gray-400 text-xs font-mono">{{ $alu->nis }}</td>
                            <td class="py-2 px-2 text-gray-900 dark:text-white text-xs font-medium">{{ $alu->nama }}</td>
                            <td class="py-2 px-2 text-gray-600 dark:text-gray-400 text-xs">{{ $alu->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="py-2 px-2">
                                <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded text-xs">
                                    {{ $alu->tahun_ajaran ?? '-' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($alumni->count() > 20)
                <p class="text-xs text-gray-400 text-center mt-2">Menampilkan 20 dari {{ $alumni->count() }} alumni</p>
                @endif
            </div>
        </x-card>
        @endif

        {{-- RIWAYAT NAIK KELAS (E-Kaldik Style) --}}
        @if($promotions->isNotEmpty())
        <x-card>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-history mr-2 text-indigo-500"></i>Riwayat Naik Kelas
            </h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left">
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Tanggal</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Dari → Ke</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium text-center">Naik</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium text-center">Lulus</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Oleh</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium">Status</th>
                            <th class="py-3 px-2 text-gray-500 dark:text-gray-400 font-medium text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($promotions as $promo)
                        <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-3 px-2 text-gray-700 dark:text-gray-300">
                                {{ $promo->processed_at?->format('d M Y H:i') }}
                            </td>
                            <td class="py-3 px-2">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $promo->from_tahun_ajaran }}</span>
                                <i class="fas fa-arrow-right text-xs text-gray-400 mx-1"></i>
                                <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ $promo->to_tahun_ajaran }}</span>
                            </td>
                            <td class="py-3 px-2 text-center">
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg font-bold text-xs">
                                    {{ $promo->total_promoted }}
                                </span>
                            </td>
                            <td class="py-3 px-2 text-center">
                                <span class="px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg font-bold text-xs">
                                    {{ $promo->total_graduated }}
                                </span>
                            </td>
                            <td class="py-3 px-2 text-gray-600 dark:text-gray-400">
                                {{ $promo->processedBy?->name ?? '-' }}
                            </td>
                            <td class="py-3 px-2">
                                @if($promo->is_rolled_back)
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg text-xs font-medium">
                                        <i class="fas fa-undo mr-1"></i>Di-undo
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg text-xs font-medium">
                                        <i class="fas fa-check mr-1"></i>Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-2 text-center">
                                @if($promo->canRollback())
                                <form method="POST" action="{{ route('attendance.tahun-ajaran.rollback') }}"
                                      onsubmit="return confirm('UNDO naik kelas ini? Siswa akan dikembalikan ke keadaan sebelumnya. Tindakan ini tidak bisa dibatalkan!')">
                                    @csrf
                                    <input type="hidden" name="promotion_id" value="{{ $promo->id }}">
                                    <button type="submit" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 rounded-lg text-xs font-medium transition-colors">
                                        <i class="fas fa-undo mr-1"></i> Undo
                                    </button>
                                </form>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
        @endif

    </div>

    {{-- ============================================ --}}
    {{-- MODAL: Buat Tahun Ajaran Baru --}}
    {{-- ============================================ --}}
    <div id="modalBuatBaru" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modalBuatBaru').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 z-10">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Tahun Ajaran Baru</h3>
                </div>
                <button onclick="document.getElementById('modalBuatBaru').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('attendance.tahun-ajaran.create') }}"
                  onsubmit="return confirm('Buat tahun ajaran baru? Tahun aktif saat ini ({{ $activeTahun }}) akan diarsipkan.')">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Ajaran Baru</label>
                    <input type="text" name="tahun" value="{{ $suggestNext }}" placeholder="Contoh: 2027/2028"
                           pattern="\d{4}/\d{4}" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-lg font-bold text-center focus:ring-2 focus:ring-indigo-400">
                    <p class="text-xs text-gray-400 mt-1.5">Format: YYYY/YYYY</p>
                </div>

                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl mb-5">
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Perhatian:</strong> Tahun {{ $activeTahun }} akan otomatis diarsipkan. Data lama tetap tersimpan.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="document.getElementById('modalBuatBaru').classList.add('hidden')"
                            class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                        Batal
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium text-sm hover:from-indigo-700 hover:to-purple-700 transition-all shadow">
                        <i class="fas fa-plus mr-1"></i> Buat
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- MODAL: Naik Kelas Wizard (E-Kaldik Style) --}}
    {{-- ============================================ --}}
    <div id="modalNaikKelas" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="document.getElementById('modalNaikKelas').classList.add('hidden')"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full z-10 flex flex-col" style="max-height: 85vh;">
            {{-- Header (sticky) --}}
            <div class="flex items-center justify-between p-5 pb-3 border-b border-gray-100 dark:border-gray-700 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-br from-amber-500 to-orange-500 rounded-lg flex items-center justify-center text-white text-sm">
                        <i class="fas fa-level-up-alt"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900 dark:text-white">Naik Kelas Massal</h3>
                    </div>
                </div>
                <button onclick="document.getElementById('modalNaikKelas').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            {{-- Step Indicator (sticky) --}}
            <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-50 dark:border-gray-700/50 shrink-0">
                <div id="stepIndicator1" class="flex items-center gap-1 px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-bold">
                    <span class="w-4 h-4 bg-indigo-600 text-white rounded-full flex items-center justify-center text-[9px]">1</span> Pilih
                </div>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <div id="stepIndicator2" class="flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-full text-xs font-bold">
                    <span class="w-4 h-4 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-[9px]">2</span> Preview
                </div>
                <i class="fas fa-chevron-right text-[10px] text-gray-300"></i>
                <div id="stepIndicator3" class="flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-full text-xs font-bold">
                    <span class="w-4 h-4 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-[9px]">3</span> Proses
                </div>
            </div>

            {{-- Scrollable Body --}}
            <div class="overflow-y-auto flex-1 px-5 py-4">

                {{-- STEP 1: Pilih Tahun & Mapping --}}
                <div id="wizardStep1">
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Dari Tahun</label>
                            <select id="nkTahunLama" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                                @foreach($tahunList as $ta)
                                <option value="{{ $ta->tahun }}" {{ !$ta->isActive() ? '' : 'selected' }}>{{ $ta->tahun }} {{ $ta->isActive() ? '(aktif)' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Ke Tahun</label>
                            <select id="nkTahunBaru" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-sm">
                                @foreach($tahunList as $ta)
                                <option value="{{ $ta->tahun }}" {{ $ta->isActive() ? 'selected' : '' }}>{{ $ta->tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                        <i class="fas fa-exchange-alt mr-1"></i> Mapping Kelas
                    </label>
                    <div class="space-y-1.5 max-h-40 overflow-y-auto mb-3 border border-gray-100 dark:border-gray-700 rounded-lg p-2">
                        @foreach($classes as $kelas)
                        @php
                            $nextTingkat = match($kelas->tingkat) {
                                '10' => '11', '11' => '12', '12' => null,
                                'X' => 'XI', 'XI' => 'XII', 'XII' => null,
                                default => null,
                            };
                            $nextKelas = $nextTingkat ? $classes->first(fn($k) => $k->tingkat === $nextTingkat && $k->jurusan === $kelas->jurusan) : null;
                        @endphp
                        <div class="flex items-center gap-2 px-2.5 py-1.5 bg-gray-50 dark:bg-gray-900/50 rounded text-xs">
                            <span class="font-medium text-gray-700 dark:text-gray-300 w-28 truncate">{{ $kelas->nama_kelas }}</span>
                            <i class="fas fa-arrow-right text-[10px] text-gray-400"></i>
                            @if($nextKelas)
                                <span class="font-medium text-green-600 dark:text-green-400">{{ $nextKelas->nama_kelas }}</span>
                                <input type="hidden" class="nk-mapping" data-from="{{ $kelas->id }}" data-to="{{ $nextKelas->id }}">
                            @else
                                <span class="font-medium text-red-500 dark:text-red-400"><i class="fas fa-graduation-cap mr-1"></i>Lulus</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- STEP 2: Preview --}}
                <div id="wizardStep2" class="hidden">
                    <div id="previewContent"></div>

                    <div class="mt-3">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Catatan (opsional)</label>
                        <textarea id="nkNotes" rows="2" placeholder="Contoh: Kenaikan kelas TP 2027/2028"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-xs"></textarea>
                    </div>
                </div>
            </div>

            {{-- Footer Buttons (sticky) --}}
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 shrink-0 bg-white dark:bg-gray-800 rounded-b-2xl">
                {{-- Step 1 buttons --}}
                <div id="footerStep1">
                    <button onclick="loadPreview()" id="btnPreview"
                            class="w-full px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium text-sm transition-all shadow hover:from-indigo-700 hover:to-purple-700">
                        <i class="fas fa-eye mr-1"></i> Lihat Preview
                    </button>
                </div>
                {{-- Step 2 buttons --}}
                <div id="footerStep2" class="hidden flex gap-3">
                    <button onclick="goToStep(1)" class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali
                    </button>
                    <button onclick="submitNaikKelas()" id="btnSubmitNK"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-xl font-medium text-sm hover:from-amber-600 hover:to-orange-600 transition-all shadow">
                        <i class="fas fa-check mr-1"></i> Proses Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden form untuk submit (di luar modal) --}}
    <form id="formNaikKelas" method="POST" action="{{ route('attendance.tahun-ajaran.naik-kelas') }}" class="hidden">
        @csrf
        <input type="hidden" name="tahun_lama" id="frmTahunLama">
        <input type="hidden" name="tahun_baru" id="frmTahunBaru">
        <input type="hidden" name="notes" id="frmNotes">
        <div id="frmMappingContainer"></div>
    </form>

    @push('scripts')
    <script>
        // ============================================
        // WIZARD NAIK KELAS — E-Kaldik Style
        // ============================================

        function goToStep(step) {
            document.getElementById('wizardStep1').classList.toggle('hidden', step !== 1);
            document.getElementById('wizardStep2').classList.toggle('hidden', step !== 2);
            document.getElementById('footerStep1').classList.toggle('hidden', step !== 1);
            document.getElementById('footerStep2').classList.toggle('hidden', step !== 2);
            if (step === 2) document.getElementById('footerStep2').classList.add('flex');

            // Update step indicators
            for (let i = 1; i <= 3; i++) {
                const el = document.getElementById('stepIndicator' + i);
                const dot = el.querySelector('span');
                if (i <= step) {
                    el.className = 'flex items-center gap-1 px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 rounded-full text-xs font-bold';
                    dot.className = 'w-4 h-4 bg-indigo-600 text-white rounded-full flex items-center justify-center text-[9px]';
                } else {
                    el.className = 'flex items-center gap-1 px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-400 rounded-full text-xs font-bold';
                    dot.className = 'w-4 h-4 bg-gray-300 dark:bg-gray-600 text-white rounded-full flex items-center justify-center text-[9px]';
                }
            }
        }

        async function loadPreview() {
            const btn = document.getElementById('btnPreview');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat preview...';

            const tahunLama = document.getElementById('nkTahunLama').value;
            const tahunBaru = document.getElementById('nkTahunBaru').value;

            // Collect mapping
            const mapping = {};
            document.querySelectorAll('.nk-mapping').forEach(el => {
                mapping[el.dataset.from] = el.dataset.to;
            });

            try {
                const res = await fetch('{{ route("attendance.tahun-ajaran.preview") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ tahun_lama: tahunLama, tahun_baru: tahunBaru, mapping })
                });

                const data = await res.json();

                if (data.success) {
                    renderPreview(data.preview);
                    goToStep(2);
                } else {
                    alert('Gagal memuat preview');
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-eye mr-1"></i> Lihat Preview';
        }

        function renderPreview(preview) {
            let html = `
                <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm">
                            Preview: ${preview.from_year} → ${preview.to_year}
                        </h4>
                        <span class="text-xs text-gray-500">${preview.total_students} siswa total</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">${preview.total_promoted}</p>
                            <p class="text-xs text-green-700 dark:text-green-300">Naik Kelas</p>
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400">${preview.total_graduated}</p>
                            <p class="text-xs text-amber-700 dark:text-amber-300">Lulus / Alumni</p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
            `;

            preview.items.forEach(item => {
                const isGraduate = item.action === 'graduate';
                const color = isGraduate ? 'amber' : 'green';
                html += `
                    <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 rounded-lg text-sm border border-gray-100 dark:border-gray-700">
                        <span class="font-medium text-gray-700 dark:text-gray-300 w-28 truncate">${item.source_class}</span>
                        <i class="fas fa-arrow-right text-xs text-gray-400"></i>
                        <span class="font-medium text-${color}-600 dark:text-${color}-400">
                            ${isGraduate ? '<i class="fas fa-graduation-cap mr-1"></i>' : ''}${item.target_class || item.target}
                        </span>
                        <span class="ml-auto text-xs text-gray-500 font-bold">${item.student_count} siswa</span>
                    </div>
                `;
            });

            html += '</div></div>';
            document.getElementById('previewContent').innerHTML = html;
        }

        function submitNaikKelas() {
            if (!confirm('Proses naik kelas massal? Siswa kelas XII akan ditandai lulus. Proses ini bisa di-UNDO nanti.')) return;

            const tahunLama = document.getElementById('nkTahunLama').value;
            const tahunBaru = document.getElementById('nkTahunBaru').value;
            const notes = document.getElementById('nkNotes').value;

            document.getElementById('frmTahunLama').value = tahunLama;
            document.getElementById('frmTahunBaru').value = tahunBaru;
            document.getElementById('frmNotes').value = notes;

            // Add mapping inputs
            const container = document.getElementById('frmMappingContainer');
            container.innerHTML = '';
            document.querySelectorAll('.nk-mapping').forEach(el => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `mapping[${el.dataset.from}]`;
                input.value = el.dataset.to;
                container.appendChild(input);
            });

            document.getElementById('formNaikKelas').submit();
        }
    </script>
    @endpush

</x-app-layout>
