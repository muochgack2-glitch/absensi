<x-public-layout>
    @push('styles')
    <style>
        .select-wrap { max-width: 560px; margin: 0 auto; padding: 24px 16px 48px; }

        .search-header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 20px;
            padding: 20px 24px;
            color: white;
            margin-bottom: 20px;
        }

        .student-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid #f1f5f9;
            text-decoration: none;
            transition: all 0.15s;
            gap: 12px;
        }
        .dark .student-item { border-color: #1e2a3a; }
        .student-item:last-child { border-bottom: none; }
        .student-item:hover { background: #f5f3ff; }
        .dark .student-item:hover { background: #1e1a35; }

        .avatar {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 800; font-size: 15px;
            flex-shrink: 0;
        }

        .student-card-wrap {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            border: 1px solid #f0f0f0;
        }
        .dark .student-card-wrap { background: #1e2433; border-color: #374151; }

        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 10px;
            font-size: 13px; font-weight: 600;
            color: #4f46e5; background: #ede9fe;
            text-decoration: none; margin-bottom: 16px; transition: all 0.2s;
        }
        .btn-back:hover { background: #ddd6fe; }
        .dark .btn-back { background: #2e1065; color: #c4b5fd; }

        .highlight { background: #fef9c3; color: #854d0e; border-radius: 3px; padding: 0 2px; }
    </style>
    @endpush

    <div class="select-wrap">
        <a href="{{ route('portal.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Cari Ulang
        </a>

        {{-- Header hasil pencarian --}}
        <div class="search-header mb-5">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                @if(isset($appLogoUrl) && $appLogoUrl)
                    <img src="{{ $appLogoUrl }}" alt="Logo" style="width:36px; height:36px; object-fit:contain; background:rgba(255,255,255,0.2); border-radius:8px; padding:3px;">
                @endif
                <p class="text-purple-200 text-xs uppercase tracking-widest">Hasil Pencarian</p>
            </div>
            <h2 class="text-xl font-bold text-white">{{ $students->count() }} siswa ditemukan</h2>
            <p class="text-purple-200 text-sm mt-1">
                <i class="fas fa-search mr-1"></i>Kata kunci: <strong>"{{ $query }}"</strong>
            </p>
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
            Pilih nama siswa yang sesuai untuk melihat data absensi:
        </p>

        <div class="student-card-wrap">
            @foreach($students as $student)
            <a href="{{ route('portal.result', ['nis' => $student->nis]) }}"
               class="student-item">
                {{-- Avatar inisial --}}
                <div class="avatar">{{ strtoupper(substr($student->nama, 0, 1)) }}</div>

                {{-- Info siswa --}}
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 dark:text-white text-sm truncate">
                        {{ $student->nama }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        <span class="mr-2"><i class="fas fa-id-card mr-1 text-indigo-400"></i>{{ $student->nis }}</span>
                        <span><i class="fas fa-chalkboard mr-1 text-purple-400"></i>{{ $student->kelas->nama_kelas ?? '-' }}</span>
                    </p>
                </div>

                {{-- Arrow --}}
                <i class="fas fa-chevron-right text-gray-300 dark:text-gray-600 text-sm flex-shrink-0"></i>
            </a>
            @endforeach
        </div>

        <p class="text-center text-xs text-gray-400 dark:text-gray-500 mt-5">
            <i class="fas fa-info-circle mr-1"></i>
            Tidak menemukan nama yang tepat? Coba masukkan nama lebih lengkap atau gunakan NIS.
        </p>
    </div>
</x-public-layout>
