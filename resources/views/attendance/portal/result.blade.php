<x-public-layout>
    @push('styles')
    <style>
        .result-wrap { max-width: 680px; margin: 0 auto; padding: 24px 16px 48px; }

        .student-card {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 20px;
            padding: 24px;
            color: white;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }
        .student-card::after {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 150px; height: 150px;
            background: rgba(255,255,255,0.07);
            border-radius: 50%;
        }

        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-bottom: 16px; }
        @media (max-width: 500px) { .stats-grid { grid-template-columns: repeat(3, 1fr); } }

        .stat-box {
            background: white;
            border-radius: 14px;
            padding: 14px 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
        }
        .dark .stat-box { background: #1e2433; border-color: #374151; }
        .stat-box .val { font-size: 24px; font-weight: 800; line-height: 1; }
        .stat-box .lbl { font-size: 11px; color: #9ca3af; margin-top: 4px; font-weight: 500; }

        .progress-wrap {
            background: white;
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
        }
        .dark .progress-wrap { background: #1e2433; border-color: #374151; }
        .progress-bar { height: 10px; background: #e5e7eb; border-radius: 100px; overflow: hidden; }
        .dark .progress-bar { background: #374151; }
        .progress-fill { height: 100%; border-radius: 100px; transition: width 1s ease; }

        .month-filter {
            background: white;
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            display: flex; align-items: center; justify-content: space-between;
        }
        .dark .month-filter { background: #1e2433; border-color: #374151; }

        .month-select {
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 13px;
            background: #f9fafb;
            color: #374151;
            outline: none;
        }
        .dark .month-select { background: #111827; border-color: #4b5563; color: #d1d5db; }

        .records-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
        }
        .dark .records-card { background: #1e2433; border-color: #374151; }
        .rec-header { padding: 14px 16px; border-bottom: 1px solid #f0f0f0; background: #fafafa; display: flex; align-items: center; justify-content: space-between; }
        .dark .rec-header { border-color: #374151; background: #151c2c; }

        .rec-row { display: flex; align-items: center; padding: 12px 16px; border-bottom: 1px solid #f9fafb; gap: 12px; }
        .dark .rec-row { border-color: #1f2937; }
        .rec-row:last-child { border-bottom: none; }
        .rec-row:hover { background: #fafafe; }
        .dark .rec-row:hover { background: #1a2235; }

        .status-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .dot-hadir     { background: #22c55e; }
        .dot-terlambat { background: #f59e0b; }
        .dot-izin      { background: #3b82f6; }
        .dot-sakit     { background: #a855f7; }
        .dot-alpha     { background: #ef4444; }

        .badge-status { font-size: 11px; font-weight: 600; padding: 2px 10px; border-radius: 100px; }
        .bg-hadir     { background: #dcfce7; color: #166534; }
        .bg-terlambat { background: #fef9c3; color: #854d0e; }
        .bg-izin      { background: #dbeafe; color: #1e40af; }
        .bg-sakit     { background: #f3e8ff; color: #6b21a8; }
        .bg-alpha     { background: #fee2e2; color: #991b1b; }

        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 10px;
            font-size: 13px; font-weight: 600;
            color: #4f46e5; background: #ede9fe;
            text-decoration: none; margin-bottom: 16px; transition: all 0.2s;
        }
        .btn-back:hover { background: #ddd6fe; }
        .dark .btn-back { background: #2e1065; color: #c4b5fd; }
        .dark .btn-back:hover { background: #3b0764; }
    </style>
    @endpush

    <div class="result-wrap">
        {{-- Back --}}
        <a href="{{ route('portal.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Cari Siswa Lain
        </a>

        {{-- Student Card --}}
        <div class="student-card mb-4">
            <div style="position:relative;z-index:1">
                <p class="text-purple-200 text-xs font-semibold uppercase tracking-widest mb-1">Data Siswa</p>
                <h2 class="text-2xl font-bold text-white leading-tight">{{ $student->nama }}</h2>
                <div class="flex flex-wrap gap-4 mt-3 text-sm">
                    <span class="text-purple-200"><i class="fas fa-id-card mr-1"></i>NIS: {{ $student->nis }}</span>
                    <span class="text-purple-200"><i class="fas fa-chalkboard mr-1"></i>{{ $student->kelas->nama_kelas ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Summary Stats --}}
        <div class="stats-grid">
            <div class="stat-box">
                <div class="val" style="color:#22c55e">{{ $summary['hadir'] }}</div>
                <div class="lbl">Hadir</div>
            </div>
            <div class="stat-box">
                <div class="val" style="color:#f59e0b">{{ $summary['terlambat'] }}</div>
                <div class="lbl">Terlambat</div>
            </div>
            <div class="stat-box">
                <div class="val" style="color:#3b82f6">{{ $summary['izin'] }}</div>
                <div class="lbl">Izin</div>
            </div>
            <div class="stat-box">
                <div class="val" style="color:#a855f7">{{ $summary['sakit'] }}</div>
                <div class="lbl">Sakit</div>
            </div>
            <div class="stat-box">
                <div class="val" style="color:#ef4444">{{ $summary['alpha'] }}</div>
                <div class="lbl">Alpha</div>
            </div>
        </div>

        {{-- Persentase --}}
        <div class="progress-wrap">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                    <i class="fas fa-chart-line mr-1 text-indigo-500"></i>Tingkat Kehadiran
                </span>
                <span class="text-lg font-bold {{ $persen >= 75 ? 'text-green-600' : ($persen >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ $persen }}%
                </span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"
                     style="width:0%;background:{{ $persen >= 75 ? '#22c55e' : ($persen >= 50 ? '#f59e0b' : '#ef4444') }}">
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                {{ $summary['hadir'] + $summary['terlambat'] }} dari {{ $totalHari }} hari sekolah
                &nbsp;
                @if($persen >= 75)
                    <span class="text-green-600 font-semibold">✅ Kehadiran Baik</span>
                @elseif($persen >= 50)
                    <span class="text-yellow-600 font-semibold">⚠️ Perlu Ditingkatkan</span>
                @else
                    <span class="text-red-600 font-semibold">❗ Kehadiran Rendah</span>
                @endif
            </p>
        </div>

        {{-- Filter Bulan --}}
        <div class="month-filter">
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                <i class="fas fa-calendar mr-1 text-indigo-500"></i>Filter Bulan
            </span>
            <form method="GET" action="{{ route('portal.result') }}">
                <input type="hidden" name="nis" value="{{ $student->nis }}">
                <select name="bulan" class="month-select" onchange="this.form.submit()">
                    @foreach($bulanList as $bl)
                        <option value="{{ $bl['value'] }}" {{ $bulan === $bl['value'] ? 'selected' : '' }}>
                            {{ $bl['label'] }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Daftar Absensi --}}
        <div class="records-card">
            <div class="rec-header">
                <span class="text-sm font-bold text-gray-800 dark:text-white">
                    📋 Riwayat Absensi
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M') }} –
                    {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                </span>
            </div>

            @forelse($records as $rec)
            @php
                $st = $rec->status;
                $labels = ['hadir'=>'Hadir','terlambat'=>'Terlambat','izin'=>'Izin','sakit'=>'Sakit','alpha'=>'Alpha'];
            @endphp
            <div class="rec-row">
                <div class="status-dot dot-{{ $st }}"></div>
                <div class="flex-1">
                    <div class="text-sm font-semibold text-gray-800 dark:text-white">
                        {{ \Carbon\Carbon::parse($rec->date)->translatedFormat('l, d F Y') }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        @if($rec->check_in_time)
                            <i class="fas fa-sign-in-alt text-green-500 mr-1"></i>Masuk: {{ \Carbon\Carbon::parse($rec->check_in_time)->format('H:i') }}
                        @endif
                        @if($rec->check_out_time)
                            &nbsp;<i class="fas fa-sign-out-alt text-blue-500 ml-2 mr-1"></i>Keluar: {{ \Carbon\Carbon::parse($rec->check_out_time)->format('H:i') }}
                        @endif
                        @if(!$rec->check_in_time && !$rec->check_out_time)
                            <span class="text-gray-400">Tidak ada catatan waktu</span>
                        @endif
                    </div>
                </div>
                <span class="badge-status bg-{{ $st }}">{{ $labels[$st] ?? $st }}</span>
            </div>
            @empty
            <div class="text-center py-12 text-gray-400 dark:text-gray-500">
                <i class="fas fa-calendar-times text-3xl mb-3 block opacity-40"></i>
                <p class="text-sm">Tidak ada data absensi bulan ini</p>
            </div>
            @endforelse
        </div>

        <p class="text-center text-xs text-gray-400 dark:text-gray-500 mt-6">
            <i class="fas fa-info-circle mr-1"></i>
            Data diperbarui secara real-time. Hubungi sekolah jika ada ketidaksesuaian.
        </p>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const fill = document.getElementById('progressFill');
            if (fill) {
                setTimeout(() => { fill.style.width = '{{ $persen }}%'; }, 200);
            }
        });
    </script>
    @endpush
</x-public-layout>
