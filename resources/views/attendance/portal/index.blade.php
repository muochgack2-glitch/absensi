<x-public-layout>
    @push('styles')
    <style>
        .portal-hero {
            min-height: calc(100vh - 70px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px 40px;
            background: radial-gradient(ellipse at top left, rgba(79,70,229,0.08) 0%, transparent 60%),
                        radial-gradient(ellipse at bottom right, rgba(139,92,246,0.07) 0%, transparent 60%);
        }

        .portal-card {
            width: 100%;
            max-width: 460px;
            background: white;
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow: 0 20px 60px -10px rgba(79,70,229,0.18), 0 4px 20px rgba(0,0,0,0.07);
            position: relative;
            overflow: hidden;
        }

        .dark .portal-card {
            background: #1e2433;
            box-shadow: 0 20px 60px -10px rgba(0,0,0,0.5);
        }

        .portal-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #ec4899);
        }

        .icon-ring {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 24px rgba(79,70,229,0.35);
            font-size: 28px; color: white;
        }

        .nis-input {
            width: 100%;
            padding: 14px 18px;
            font-size: 16px;
            letter-spacing: 1.5px;
            font-weight: 600;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            outline: none;
            transition: all 0.2s;
            background: #f9fafb;
            color: #111827;
        }

        .dark .nis-input {
            background: #111827;
            border-color: #374151;
            color: white;
        }

        .nis-input:focus {
            border-color: #4f46e5;
            background: white;
            box-shadow: 0 0 0 4px rgba(79,70,229,0.12);
        }

        .dark .nis-input:focus { background: #1f2937; }

        .btn-cek {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 14px rgba(79,70,229,0.35);
        }

        .btn-cek:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(79,70,229,0.45);
        }

        .btn-cek:active { transform: translateY(0); }

        .info-chips { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 20px; justify-content: center; }
        .chip {
            padding: 4px 12px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 500;
            background: #ede9fe;
            color: #6d28d9;
        }
        .dark .chip { background: #2e1065; color: #c4b5fd; }
    </style>
    @endpush

    <div class="portal-hero">
        <div class="portal-card">
            <div class="icon-ring">
                <i class="fas fa-search"></i>
            </div>

            <h1 class="text-center text-2xl font-bold text-gray-900 dark:text-white mb-1">
                Cek Absensi Siswa
            </h1>
            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-7">
                Masukkan NIS siswa untuk melihat rekap kehadiran
            </p>

            @if($errors->has('query'))
                <div class="flex items-center gap-2 px-4 py-3 mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-400">
                    <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                    <span>{{ $errors->first('query') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('portal.check') }}">
                @csrf
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    NIS atau Nama Siswa
                </label>
                <input type="text"
                       name="query"
                       id="queryInput"
                       value="{{ old('query') }}"
                       placeholder="NIS: 1234567890 atau nama siswa"
                       autocomplete="off"
                       autofocus
                       class="nis-input mb-4">

                <button type="submit" class="btn-cek">
                    <i class="fas fa-search"></i>
                    Cek Absensi
                </button>
            </form>

            <div class="info-chips">
                <span class="chip"><i class="fas fa-lock mr-1"></i>Data Privat</span>
                <span class="chip"><i class="fas fa-shield-alt mr-1"></i>Tanpa Login</span>
                <span class="chip"><i class="fas fa-mobile-alt mr-1"></i>Mobile Friendly</span>
            </div>
        </div>
    </div>
</x-public-layout>
