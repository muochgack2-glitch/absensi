<x-public-layout>
    @push('styles')
    <style>
        .izin-wrap { max-width: 640px; margin: 0 auto; padding: 24px 16px 60px; }

        .form-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            border: 1px solid #f0f0f0;
        }
        .dark .form-card { background: #1e2433; border-color: #374151; }

        .form-header {
            background: linear-gradient(135deg, #059669, #0d9488);
            padding: 24px;
            color: white;
        }

        .form-body { padding: 24px; }

        .field-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .dark .field-label { color: #d1d5db; }

        .field-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            background: #f9fafb;
            color: #111827;
            outline: none;
            transition: all 0.2s;
        }
        .dark .field-input { background: #111827; border-color: #374151; color: white; }
        .field-input:focus { border-color: #059669; background: white; box-shadow: 0 0 0 3px rgba(5,150,105,0.12); }
        .dark .field-input:focus { background: #1f2937; }

        .field-textarea { min-height: 90px; resize: vertical; }

        .search-box { position: relative; }
        .search-results {
            position: absolute; top: 100%; left: 0; right: 0; z-index: 50;
            background: white; border: 1.5px solid #e5e7eb; border-top: none;
            border-radius: 0 0 10px 10px; max-height: 220px; overflow-y: auto;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .dark .search-results { background: #1e2433; border-color: #374151; }
        .search-result-item { padding: 10px 14px; cursor: pointer; border-bottom: 1px solid #f5f5f5; display: flex; align-items: center; gap: 10px; }
        .dark .search-result-item { border-color: #2d3748; }
        .search-result-item:hover { background: #ecfdf5; }
        .dark .search-result-item:hover { background: #064e3b; }
        .search-result-item:last-child { border-bottom: none; }

        .student-selected {
            display: none;
            align-items: center; gap: 10px;
            padding: 10px 14px;
            background: #ecfdf5; border: 1.5px solid #6ee7b7; border-radius: 10px; margin-top: 6px;
        }
        .dark .student-selected { background: #064e3b; border-color: #059669; }
        .student-selected.show { display: flex; }

        .jenis-radio { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .radio-item { position: relative; }
        .radio-item input { position: absolute; opacity: 0; width: 0; height: 0; }
        .radio-label {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px;
            cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.2s;
            color: #6b7280;
        }
        .dark .radio-label { border-color: #374151; color: #9ca3af; }
        .radio-item input:checked + .radio-label {
            border-color: #059669; background: #ecfdf5; color: #065f46;
        }
        .dark .radio-item input:checked + .radio-label { background: #064e3b; color: #6ee7b7; }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #059669, #0d9488);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 14px rgba(5,150,105,0.3);
            margin-top: 6px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(5,150,105,0.4); }
    </style>
    @endpush

    <div class="izin-wrap">

        @if(session('success'))
        <div class="flex items-center gap-3 px-5 py-4 mb-5 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-800 dark:text-green-300">
            <i class="fas fa-check-circle text-xl text-green-500"></i>
            <div>
                <p class="font-semibold text-sm">Pengajuan Terkirim!</p>
                <p class="text-xs mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div class="form-card">
            {{-- Header --}}
            <div class="form-header">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-white/20 rounded-xl flex items-center justify-center text-xl">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Form Izin / Sakit Online</h1>
                        <p class="text-green-100 text-sm mt-0.5">{{ $schoolName }}</p>
                    </div>
                </div>
                <p class="text-green-100 text-xs mt-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pengajuan akan diproses admin sekolah. Mohon isi dengan jujur dan lengkap.
                </p>
            </div>

            <div class="form-body">
                <form method="POST" action="{{ route('izin.submit') }}" enctype="multipart/form-data" id="izinForm">
                    @csrf

                    {{-- Cari Siswa --}}
                    <div class="mb-5">
                        <label class="field-label">Nama / NIS Siswa <span class="text-red-500">*</span></label>
                        <div class="search-box">
                            <input type="text"
                                   id="studentSearch"
                                   placeholder="Ketik nama atau NIS siswa..."
                                   autocomplete="off"
                                   class="field-input">
                            <div class="search-results hidden" id="searchResults"></div>
                        </div>
                        <input type="hidden" name="student_id" id="studentId">

                        {{-- Preview siswa terpilih --}}
                        <div class="student-selected" id="studentSelected">
                            <div class="w-9 h-9 rounded-lg bg-green-500 flex items-center justify-center text-white font-bold text-sm" id="studentAvatar">?</div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white text-sm" id="studentName">-</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400" id="studentInfo">-</p>
                            </div>
                            <button type="button" onclick="clearStudent()" class="ml-auto text-gray-400 hover:text-red-500">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        @error('student_id')
                            <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenis Izin --}}
                    <div class="mb-5">
                        <label class="field-label">Jenis <span class="text-red-500">*</span></label>
                        <div class="jenis-radio">
                            <div class="radio-item">
                                <input type="radio" name="jenis" id="jenisIzin" value="izin" checked>
                                <label for="jenisIzin" class="radio-label">
                                    <i class="fas fa-calendar-times text-blue-500"></i> Izin
                                </label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" name="jenis" id="jenisSakit" value="sakit">
                                <label for="jenisSakit" class="radio-label">
                                    <i class="fas fa-briefcase-medical text-red-400"></i> Sakit
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="field-label" for="tanggalMulai">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_mulai" id="tanggalMulai"
                                   min="{{ date('Y-m-d') }}"
                                   value="{{ old('tanggal_mulai', date('Y-m-d')) }}"
                                   class="field-input" onchange="updateMinEnd()">
                            @error('tanggal_mulai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="field-label" for="tanggalSelesai">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_selesai" id="tanggalSelesai"
                                   min="{{ date('Y-m-d') }}"
                                   value="{{ old('tanggal_selesai', date('Y-m-d')) }}"
                                   class="field-input">
                            @error('tanggal_selesai')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Alasan --}}
                    <div class="mb-5">
                        <label class="field-label" for="alasan">Alasan <span class="text-red-500">*</span></label>
                        <textarea name="alasan" id="alasan" rows="3"
                                  placeholder="Jelaskan alasan izin/sakit secara singkat dan jelas..."
                                  class="field-input field-textarea">{{ old('alasan') }}</textarea>
                        @error('alasan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Data Pelapor --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="field-label" for="namaPelapor">Nama Orang Tua/Wali <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_pelapor" id="namaPelapor"
                                   value="{{ old('nama_pelapor') }}"
                                   placeholder="Nama ortu/wali"
                                   class="field-input">
                            @error('nama_pelapor')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="field-label" for="noHp">Nomor HP/WA <span class="text-red-500">*</span></label>
                            <input type="tel" name="no_hp_pelapor" id="noHp"
                                   value="{{ old('no_hp_pelapor') }}"
                                   placeholder="08XXXXXXXXXX"
                                   class="field-input">
                            @error('no_hp_pelapor')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Lampiran --}}
                    <div class="mb-6">
                        <label class="field-label" for="lampiran">
                            Lampiran Surat / Foto (opsional)
                        </label>
                        <input type="file" name="lampiran" id="lampiran"
                               accept=".jpg,.jpeg,.png,.pdf"
                               class="block w-full text-sm text-gray-600 dark:text-gray-400
                                      file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold file:bg-green-100 file:text-green-700
                                      hover:file:bg-green-200 dark:file:bg-green-900/30 dark:file:text-green-400
                                      cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, atau PDF — maks 5MB</p>
                        @error('lampiran')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn" disabled>
                        <i class="fas fa-paper-plane"></i>
                        Kirim Pengajuan Izin
                    </button>

                    <p class="text-center text-xs text-gray-400 dark:text-gray-500 mt-3">
                        <i class="fas fa-lock mr-1"></i>
                        Data Anda aman dan hanya dilihat pihak sekolah
                    </p>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let searchTimeout;
        const input = document.getElementById('studentSearch');
        const results = document.getElementById('searchResults');
        const submitBtn = document.getElementById('submitBtn');

        input.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            const q = input.value.trim();
            if (q.length < 2) { results.classList.add('hidden'); return; }

            searchTimeout = setTimeout(async () => {
                try {
                    const resp = await fetch(`{{ route('izin.search') }}?query=${encodeURIComponent(q)}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await resp.json();
                    renderResults(data);
                } catch(e) { console.error(e); }
            }, 300);
        });

        function renderResults(data) {
            results.innerHTML = '';
            if (!data.length) {
                results.innerHTML = '<div class="search-result-item text-gray-400 text-sm">Tidak ditemukan</div>';
                results.classList.remove('hidden');
                return;
            }
            data.forEach(s => {
                const div = document.createElement('div');
                div.className = 'search-result-item';
                div.innerHTML = `
                    <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#059669,#0d9488);display:flex;align-items:center;justify-content:center;color:white;font-weight:800;font-size:13px;flex-shrink:0">
                        ${s.nama.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:inherit">${s.nama}</p>
                        <p style="font-size:11px;color:#9ca3af">NIS: ${s.nis} &bull; ${s.kelas}</p>
                    </div>
                `;
                div.addEventListener('click', () => selectStudent(s));
                results.appendChild(div);
            });
            results.classList.remove('hidden');
        }

        function selectStudent(s) {
            document.getElementById('studentId').value = s.id;
            document.getElementById('studentName').textContent = s.nama;
            document.getElementById('studentInfo').textContent = `NIS: ${s.nis} • ${s.kelas}`;
            document.getElementById('studentAvatar').textContent = s.nama.charAt(0).toUpperCase();
            document.getElementById('studentSelected').classList.add('show');
            results.classList.add('hidden');
            input.value = s.nama;
            input.classList.add('opacity-50', 'pointer-events-none');
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
        }

        function clearStudent() {
            document.getElementById('studentId').value = '';
            document.getElementById('studentSelected').classList.remove('show');
            input.value = '';
            input.classList.remove('opacity-50', 'pointer-events-none');
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
        }

        function updateMinEnd() {
            document.getElementById('tanggalSelesai').min = document.getElementById('tanggalMulai').value;
        }

        // Disable submit saat awal (belum pilih siswa)
        submitBtn.style.opacity = '0.6';

        // Tutup hasil saat klik di luar
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-box')) results.classList.add('hidden');
        });
    </script>
    @endpush
</x-public-layout>
