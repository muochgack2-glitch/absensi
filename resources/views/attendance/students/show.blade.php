@php
    $pageTitle = 'Detail Siswa - ' . $student->nama;
    $breadcrumbs = [
        ['label' => 'Data Siswa', 'url' => route('attendance.students.index')],
        ['label' => $student->nama]
    ];
@endphp

<x-app-layout>
    <div class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Student Info --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Profile Card --}}
                <x-card>
                    <div class="text-center">
                        @if($student->foto_profil)
                        <img src="{{ Storage::url($student->foto_profil) }}" 
                             alt="{{ $student->nama }}"
                             class="w-32 h-32 rounded-full object-cover mx-auto mb-4 border-4 border-primary-500 shadow-lg">
                        @else
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-4xl mx-auto mb-4 shadow-lg">
                            {{ substr($student->nama, 0, 1) }}
                        </div>
                        @endif

                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $student->nama }}</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-3">NIS: {{ $student->nis }}</p>
                        
                        @if($student->is_active)
                        <x-badge variant="success" class="text-sm">
                            <i class="fas fa-check-circle mr-1"></i> Aktif
                        </x-badge>
                        @else
                        <x-badge variant="danger" class="text-sm">
                            <i class="fas fa-times-circle mr-1"></i> Tidak Aktif
                        </x-badge>
                        @endif
                    </div>

                    <div class="mt-6 space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white mr-3">
                                <i class="fas fa-school"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Kelas</div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ $student->kelas->nama_kelas }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white mr-3">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-500 dark:text-gray-400">Jurusan</div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ $student->kelas->jurusan ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center text-white mr-3">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-500 dark:text-gray-400">HP Orang Tua</div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ $student->no_hp_ortu ?? '-' }}
                                </div>
                            </div>
                        </div>
                        @if($student->no_hp_ortu2)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-500 dark:text-purple-400">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm text-gray-500 dark:text-gray-400">HP Wali / Alternatif</div>
                                <div class="font-semibold text-gray-900 dark:text-white">
                                    {{ $student->no_hp_ortu2 }}
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 space-y-2">
                        <a href="{{ route('attendance.students.edit', $student->id) }}" 
                           class="block w-full text-center px-4 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-lg transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                            <i class="fas fa-edit mr-2"></i> Edit Data
                        </a>
                        @if($student->qr_code_path)
                        <a href="{{ route('attendance.qr.show', $student->nis) }}" 
                           class="block w-full text-center px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 shadow-md hover:shadow-lg font-medium"
                           target="_blank">
                            <i class="fas fa-qrcode mr-2"></i> Lihat QR Code
                        </a>
                        @endif
                    </div>
                </x-card>

                {{-- QR Code Card --}}
                @if($student->qr_code_path)
                <x-card>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-qrcode mr-2 text-primary-500"></i>
                        QR Code Absensi
                    </h3>
                    <div class="text-center space-y-3">
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-900 p-4 rounded-lg inline-block">
                            <img src="{{ Storage::url($student->qr_code_path) }}"
                                 alt="QR Code {{ $student->nis }}"
                                 id="qrCodeImg"
                                 crossorigin="anonymous"
                                 class="w-48 h-48 mx-auto border-2 border-primary-500 rounded">
                        </div>

                        {{-- Hidden canvas untuk generate kartu --}}
                        <canvas id="qrCardCanvas" style="display:none;"></canvas>

                        <div class="flex flex-col gap-2">
                            {{-- Download QR saja --}}
                            <a href="{{ route('attendance.qr.download', $student->nis) }}"
                               class="inline-flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm transition-all duration-200 shadow">
                                <i class="fas fa-download mr-2"></i> Download QR (PNG)
                            </a>

                            {{-- Download Kartu QR dengan identitas --}}
                            <button onclick="downloadQRCard('png')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm transition-all duration-200 shadow">
                                <i class="fas fa-id-card mr-2"></i> Download Kartu QR (PNG)
                            </button>
                            <button onclick="downloadQRCard('jpg')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm transition-all duration-200 shadow">
                                <i class="fas fa-image mr-2"></i> Download Kartu QR (JPG)
                            </button>
                        </div>
                    </div>
                </x-card>
                @endif
            </div>

            {{-- Right Column: Attendance History --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Stats Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <x-stat-card
                        title="Hadir"
                        :value="$student->attendanceRecords->where('status', 'hadir')->count()"
                        icon="fa-check-circle"
                        color="green"
                    />
                    <x-stat-card
                        title="Terlambat"
                        :value="$student->attendanceRecords->where('status', 'terlambat')->count()"
                        icon="fa-clock"
                        color="yellow"
                    />
                    <x-stat-card
                        title="Sakit/Izin"
                        :value="$student->attendanceRecords->whereIn('status', ['sakit', 'izin'])->count()"
                        icon="fa-notes-medical"
                        color="blue"
                    />
                    <x-stat-card
                        title="Alpha"
                        :value="$student->attendanceRecords->where('status', 'alpha')->count()"
                        icon="fa-times-circle"
                        color="red"
                    />
                </div>

                {{-- Calendar Heatmap --}}
                @php
                    $startDate = now()->subMonths(3)->startOfMonth();
                    $endDate = now();
                    $records = $student->attendanceRecords
                        ->where('date', '>=', $startDate)
                        ->keyBy(fn($r) => $r->date->format('Y-m-d'));
                    
                    $statusColors = [
                        'hadir' => '#22c55e',
                        'terlambat' => '#eab308',
                        'izin' => '#3b82f6',
                        'sakit' => '#a855f7',
                        'alpha' => '#ef4444',
                    ];
                    $statusLabels = [
                        'hadir' => 'Hadir',
                        'terlambat' => 'Terlambat',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpha' => 'Alpha',
                    ];
                @endphp
                <x-card>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-primary-500"></i>
                        Peta Kehadiran (3 Bulan Terakhir)
                    </h3>
                    
                    {{-- Legend --}}
                    <div class="flex flex-wrap gap-3 mb-4 text-xs">
                        @foreach($statusColors as $status => $color)
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-sm" style="background: {{ $color }}"></div>
                            <span class="text-gray-600 dark:text-gray-400">{{ $statusLabels[$status] }}</span>
                        </div>
                        @endforeach
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-sm bg-gray-200 dark:bg-gray-700"></div>
                            <span class="text-gray-600 dark:text-gray-400">Tidak Ada Data</span>
                        </div>
                    </div>

                    {{-- Heatmap Grid --}}
                    <div class="overflow-x-auto">
                        <div style="display: flex; gap: 3px; min-width: 600px;">
                            @php
                                $current = $startDate->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
                                $endWeek = $endDate->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
                                $dayLabels = ['S', 'S', 'R', 'K', 'J', 'S', 'M'];
                            @endphp
                            
                            {{-- Day labels --}}
                            <div style="display: flex; flex-direction: column; gap: 3px; margin-right: 4px;">
                                @foreach($dayLabels as $i => $label)
                                <div style="width: 14px; height: 14px; font-size: 9px; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                    @if($i % 2 == 0){{ $label }}@endif
                                </div>
                                @endforeach
                            </div>

                            @while($current->lte($endWeek))
                            <div style="display: flex; flex-direction: column; gap: 3px;">
                                @for($day = 0; $day < 7; $day++)
                                    @php
                                        $dateKey = $current->format('Y-m-d');
                                        $record = $records->get($dateKey);
                                        $isInRange = $current->gte($startDate) && $current->lte($endDate);
                                        $isWeekend = $current->isWeekend();
                                        
                                        if (!$isInRange) {
                                            $bgColor = 'transparent';
                                            $tooltip = '';
                                        } elseif ($record) {
                                            $bgColor = $statusColors[$record->status] ?? '#d1d5db';
                                            $tooltip = $current->format('d M Y') . ' — ' . ucfirst($record->status);
                                        } else {
                                            $bgColor = $isWeekend ? '#f3f4f6' : '#e5e7eb';
                                            $tooltip = $current->format('d M Y') . ($isWeekend ? ' (Libur)' : ' — Tidak ada data');
                                        }
                                    @endphp
                                    <div 
                                        style="width: 14px; height: 14px; border-radius: 3px; background: {{ $bgColor }}; cursor: {{ $tooltip ? 'pointer' : 'default' }};"
                                        @if($tooltip) title="{{ $tooltip }}" @endif
                                        class="{{ !$isInRange ? '' : 'dark:opacity-90 hover:ring-2 hover:ring-gray-400 transition-all' }}"
                                    ></div>
                                    @php $current->addDay(); @endphp
                                @endfor
                            </div>
                            @endwhile
                        </div>
                    </div>

                    {{-- Month labels --}}
                    <div class="flex mt-2 text-xs text-gray-400" style="padding-left: 18px; gap: 0;">
                        @php
                            $monthCurrent = $startDate->copy()->startOfMonth();
                        @endphp
                        @while($monthCurrent->lte($endDate))
                            @php
                                $daysInMonth = $monthCurrent->daysInMonth;
                                $weeksSpan = ceil($daysInMonth / 7);
                            @endphp
                            <div style="width: {{ $weeksSpan * 17 }}px;">{{ $monthCurrent->translatedFormat('M') }}</div>
                            @php $monthCurrent->addMonth(); @endphp
                        @endwhile
                    </div>
                </x-card>

                {{-- Attendance History --}}
                <x-card>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center">
                        <i class="fas fa-history mr-2 text-primary-500"></i>
                        Riwayat Absensi Terakhir
                    </h3>
                    
                    @if($student->attendanceRecords->count() > 0)
                    <div class="overflow-x-auto">
                        <x-table>
                            <x-table.header>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                                <th>Foto</th>
                            </x-table.header>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($student->attendanceRecords as $record)
                                <x-table.row>
                                    <x-table.cell>
                                        <div class="font-medium">{{ $record->date->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $record->date->format('l') }}</div>
                                    </x-table.cell>
                                    <x-table.cell>
                                        {{ $record->check_in_time ? substr($record->check_in_time, 0, 5) : '-' }}
                                    </x-table.cell>
                                    <x-table.cell>
                                        {{ $record->check_out_time ? substr($record->check_out_time, 0, 5) : '-' }}
                                    </x-table.cell>
                                    <x-table.cell>
                                        @php
                                            $statusVariants = [
                                                'hadir' => 'success',
                                                'terlambat' => 'warning',
                                                'sakit' => 'info',
                                                'izin' => 'info',
                                                'alpha' => 'danger',
                                            ];
                                            $variant = $statusVariants[$record->status] ?? 'secondary';
                                        @endphp
                                        <x-badge :variant="$variant">
                                            {{ ucfirst($record->status) }}
                                        </x-badge>
                                    </x-table.cell>
                                    <x-table.cell>
                                        <div class="flex gap-2">
                                            @if($record->check_in_photo)
                                            <img src="{{ Storage::url($record->check_in_photo) }}" 
                                                 alt="Check In"
                                                 class="w-12 h-12 rounded-lg object-cover cursor-pointer border-2 border-green-300 dark:border-green-700 hover:scale-110 transition-transform"
                                                 data-photo-url="{{ Storage::url($record->check_in_photo) }}"
                                                 data-photo-title="Check In - {{ $record->date->format('d M Y') }}"
                                                 onclick="viewPhoto(this.dataset.photoUrl, this.dataset.photoTitle)">
                                            @endif
                                            @if($record->check_out_photo)
                                            <img src="{{ Storage::url($record->check_out_photo) }}" 
                                                 alt="Check Out"
                                                 class="w-12 h-12 rounded-lg object-cover cursor-pointer border-2 border-blue-300 dark:border-blue-700 hover:scale-110 transition-transform"
                                                 data-photo-url="{{ Storage::url($record->check_out_photo) }}"
                                                 data-photo-title="Check Out - {{ $record->date->format('d M Y') }}"
                                                 onclick="viewPhoto(this.dataset.photoUrl, this.dataset.photoTitle)">
                                            @endif
                                        </div>
                                    </x-table.cell>
                                </x-table.row>
                                @endforeach
                            </tbody>
                        </x-table>
                    </div>
                    @else
                    <x-empty-state
                        icon="fa-calendar-times"
                        title="Belum Ada Riwayat"
                        message="Siswa ini belum memiliki riwayat absensi"
                    />
                    @endif
                </x-card>
            </div>
        </div>
    </div>

    {{-- Photo Modal --}}
    <x-modal id="photoModal">
        <div class="p-6">
            <h3 id="photoTitle" class="text-lg font-bold text-gray-900 dark:text-white mb-4"></h3>
            <img id="photoImage" src="" alt="Photo" class="w-full rounded-lg">
        </div>
    </x-modal>

    @push('scripts')
    <script>
        function viewPhoto(url, title) {
            document.getElementById('photoImage').src = url;
            document.getElementById('photoTitle').textContent = title;
            document.getElementById('photoModal').classList.remove('hidden');
        }

        // Close modal on click outside
        document.getElementById('photoModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('photoModal')?.classList.add('hidden');
            }
        });

        // ───────────────────────────────────────────────────
        // Download Kartu QR: QR + Identitas via Canvas API
        // ───────────────────────────────────────────────────
        async function downloadQRCard(format) {
            const canvas  = document.getElementById('qrCardCanvas');
            const qrImg   = document.getElementById('qrCodeImg');
            const W = 480, H = 640;
            canvas.width  = W;
            canvas.height = H;
            const ctx = canvas.getContext('2d');

            // ── Background putih ──
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, W, H);

            // ── Header gradient ──
            const grad = ctx.createLinearGradient(0, 0, W, 80);
            grad.addColorStop(0, '#4F46E5');
            grad.addColorStop(1, '#7C3AED');
            ctx.fillStyle = grad;
            ctx.fillRect(0, 0, W, 90);

            // ── Nama sekolah ──
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 18px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('{{ $schoolName ?? "SMK PGRI Blora" }}', W / 2, 38);
            ctx.font = '13px sans-serif';
            ctx.fillStyle = 'rgba(255,255,255,0.85)';
            ctx.fillText('Kartu Absensi Siswa', W / 2, 62);
            ctx.fillText('{{ now()->format("Y") }}', W / 2, 80);

            // ── Foto profil siswa (jika ada) ──
            @if($student->foto_profil)
            try {
                const foto = await loadImg('{{ Storage::url($student->foto_profil) }}');
                const fSize = 100;
                const fx = W / 2 - fSize / 2;
                const fy = 100;
                // Lingkaran clip
                ctx.save();
                ctx.beginPath();
                ctx.arc(W / 2, fy + fSize / 2, fSize / 2, 0, Math.PI * 2);
                ctx.closePath();
                ctx.clip();
                ctx.drawImage(foto, fx, fy, fSize, fSize);
                ctx.restore();
                // Border lingkaran
                ctx.beginPath();
                ctx.arc(W / 2, fy + fSize / 2, fSize / 2 + 2, 0, Math.PI * 2);
                ctx.strokeStyle = '#4F46E5';
                ctx.lineWidth = 3;
                ctx.stroke();
            } catch(e) {}
            @endif

            // ── QR Code ──
            const qrSize = 220;
            const qrX    = W / 2 - qrSize / 2;
            const qrY    = {{ $student->foto_profil ? 220 : 110 }};
            try {
                await new Promise((res) => {
                    if (qrImg.complete) { res(); return; }
                    qrImg.onload = res;
                });
                // Border kotak QR
                ctx.fillStyle = '#F3F4F6';
                roundRect(ctx, qrX - 10, qrY - 10, qrSize + 20, qrSize + 20, 12);
                ctx.fill();
                ctx.drawImage(qrImg, qrX, qrY, qrSize, qrSize);
            } catch(e) {}

            // ── Identitas siswa ──
            const textY = qrY + qrSize + 30;
            ctx.textAlign = 'center';

            // Nama
            ctx.fillStyle = '#111827';
            ctx.font = 'bold 22px sans-serif';
            ctx.fillText('{{ $student->nama }}', W / 2, textY);

            // NIS
            ctx.font = '15px sans-serif';
            ctx.fillStyle = '#6B7280';
            ctx.fillText('NIS: {{ $student->nis }}', W / 2, textY + 28);

            // Kelas
            ctx.fillStyle = '#4F46E5';
            ctx.font = 'bold 16px sans-serif';
            ctx.fillText('{{ $student->kelas->nama_kelas ?? "-" }}', W / 2, textY + 54);

            // ── Garis bawah / footer ──
            ctx.fillStyle = '#F9FAFB';
            ctx.fillRect(0, H - 44, W, 44);
            ctx.fillStyle = '#9CA3AF';
            ctx.font = '11px sans-serif';
            ctx.fillText('Scan QR Code ini untuk absensi harian', W / 2, H - 22);
            ctx.fillText('{{ config("app.url") }}', W / 2, H - 8);

            // ── Border kartu keseluruhan ──
            ctx.strokeStyle = '#E5E7EB';
            ctx.lineWidth = 2;
            ctx.strokeRect(1, 1, W - 2, H - 2);

            // ── Download ──
            const link = document.createElement('a');
            if (format === 'jpg') {
                link.href     = canvas.toDataURL('image/jpeg', 0.95);
                link.download = 'kartu_qr_{{ $student->nis }}.jpg';
            } else {
                link.href     = canvas.toDataURL('image/png');
                link.download = 'kartu_qr_{{ $student->nis }}.png';
            }
            link.click();
        }

        function loadImg(src) {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload  = () => resolve(img);
                img.onerror = reject;
                img.src = src;
            });
        }

        function roundRect(ctx, x, y, w, h, r) {
            ctx.beginPath();
            ctx.moveTo(x + r, y);
            ctx.lineTo(x + w - r, y);
            ctx.arcTo(x + w, y, x + w, y + r, r);
            ctx.lineTo(x + w, y + h - r);
            ctx.arcTo(x + w, y + h, x + w - r, y + h, r);
            ctx.lineTo(x + r, y + h);
            ctx.arcTo(x, y + h, x, y + h - r, r);
            ctx.lineTo(x, y + r);
            ctx.arcTo(x, y, x + r, y, r);
            ctx.closePath();
        }
    </script>
    @endpush
</x-app-layout>

