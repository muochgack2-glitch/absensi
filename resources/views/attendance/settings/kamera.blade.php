<x-app-layout>
    <x-slot name="title">Setting Kamera</x-slot>
    <x-slot name="pageTitle">Setting Kamera</x-slot>

    <div class="space-y-6">
        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">📷 Setting Kamera</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Konfigurasi kamera scanner absensi dan dual camera</p>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                <span class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg">
                <ul class="text-sm text-red-700 dark:text-red-400 list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('attendance.kamera.update') }}" method="POST">
            @csrf
            @method('PUT')

            <x-card>
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center text-white text-sm">
                            <i class="fas fa-camera"></i>
                        </span>
                        Pengaturan Kamera Scanner
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Dual camera: Webcam 1 scan QR kartu, Webcam 2 foto wajah siswa.
                        Hanya aktif di PC dengan 2 USB webcam terhubung.
                    </p>

                    {{-- Toggle Dual Camera --}}
                    <div class="flex items-center gap-3">
                        <input
                            type="checkbox"
                            id="use_dual_camera"
                            name="settings[use_dual_camera]"
                            value="1"
                            {{ ($settings['camera']['use_dual_camera'] ?? '0') == '1' ? 'checked' : '' }}
                            class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        >
                        <label for="use_dual_camera" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Aktifkan Dual Camera (QR + Foto Wajah)
                        </label>
                    </div>

                    {{-- Scan FPS --}}
                    <div class="p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-200 dark:border-indigo-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🎯 Agresivitas Scan Kamera (FPS)
                        </label>
                        @php $currentFps = (int) ($settings['camera']['scan_fps'] ?? $settings['general']['scan_fps'] ?? 10); @endphp
                        <div class="flex items-center gap-4">
                            <input type="range"
                                   name="settings[scan_fps]"
                                   id="scanFpsRange"
                                   min="5" max="30" step="5"
                                   value="{{ old('settings.scan_fps', $currentFps) }}"
                                   oninput="document.getElementById('scanFpsVal').textContent = this.value"
                                   class="flex-1 accent-indigo-500">
                            <span class="text-sm font-bold text-indigo-700 dark:text-indigo-300 w-16 text-center">
                                <span id="scanFpsVal">{{ $currentFps }}</span> fps
                            </span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1 px-1">
                            <span>5 — Hemat baterai</span><span>15 — Seimbang</span><span>30 — Cepat</span>
                        </div>
                        <p class="text-xs text-indigo-700 dark:text-indigo-400 mt-2">
                            Semakin tinggi = scan lebih cepat & responsif, tapi lebih boros CPU. Default: <strong>10 fps</strong>
                        </p>
                    </div>

                    {{-- Resolusi Kamera --}}
                    @php
                        $resOptions = [
                            'sd'  => ['label'=>'SD',  'desc'=>'640×480',   'note'=>'Hemat bandwidth'],
                            'hd'  => ['label'=>'HD',  'desc'=>'1280×720',  'note'=>'✅ Recommended'],
                            'fhd' => ['label'=>'FHD', 'desc'=>'1920×1080', 'note'=>'Butuh webcam HD'],
                        ];
                        $currentResQr    = $settings['camera']['scan_resolution_qr']   ?? 'hd';
                        $currentResPhoto = $settings['camera']['scan_resolution_photo'] ?? 'hd';
                    @endphp
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-700 space-y-5">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">📹 Resolusi Kamera</p>

                        {{-- QR Camera Resolution --}}
                        <div>
                            <label class="block text-xs font-medium text-indigo-700 dark:text-indigo-400 mb-2">
                                <i class="fas fa-qrcode mr-1"></i> Kamera QR Scanner
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($resOptions as $val => $info)
                                <label class="cursor-pointer" onclick="selectResCard(this, 'res-qr', '#6366f1')">
                                    <input type="radio" name="settings[scan_resolution_qr]" value="{{ $val }}"
                                           {{ old('settings.scan_resolution_qr', $currentResQr) === $val ? 'checked' : '' }}
                                           class="sr-only">
                                    <div data-group="res-qr"
                                         style="{{ old('settings.scan_resolution_qr', $currentResQr) === $val ? 'border-color:#6366f1;background:#eef2ff;' : '' }}"
                                         class="p-2 rounded-lg border-2 border-gray-200 text-center transition-all hover:border-indigo-300">
                                        <div class="font-bold text-sm text-gray-900 dark:text-white">{{ $info['label'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $info['desc'] }}</div>
                                        <div class="text-xs text-indigo-500 mt-1">{{ $info['note'] }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Photo Camera Resolution --}}
                        <div>
                            <label class="block text-xs font-medium text-purple-700 dark:text-purple-400 mb-2">
                                <i class="fas fa-camera mr-1"></i> Kamera Foto Wajah (Dual Camera)
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($resOptions as $val => $info)
                                <label class="cursor-pointer" onclick="selectResCard(this, 'res-photo', '#a855f7')">
                                    <input type="radio" name="settings[scan_resolution_photo]" value="{{ $val }}"
                                           {{ old('settings.scan_resolution_photo', $currentResPhoto) === $val ? 'checked' : '' }}
                                           class="sr-only">
                                    <div data-group="res-photo"
                                         style="{{ old('settings.scan_resolution_photo', $currentResPhoto) === $val ? 'border-color:#a855f7;background:#faf5ff;' : '' }}"
                                         class="p-2 rounded-lg border-2 border-gray-200 text-center transition-all hover:border-purple-300">
                                        <div class="font-bold text-sm text-gray-900 dark:text-white">{{ $info['label'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $info['desc'] }}</div>
                                        <div class="text-xs text-purple-500 mt-1">{{ $info['note'] }}</div>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <p class="text-xs text-purple-600 dark:text-purple-400">
                            Resolusi lebih tinggi = gambar lebih jelas, tapi lebih berat.
                        </p>
                    </div>

                    {{-- Dropdown + Preview --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- QR Camera --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-qrcode mr-1 text-indigo-500"></i> Kamera QR Scanner
                            </label>
                            <select id="qr_camera_select" onchange="onCameraSelect('qr')"
                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                <option value="">— Mendeteksi kamera... —</option>
                            </select>
                            <input type="hidden" name="settings[qr_camera_index]" id="qr_camera_index_val"
                                   value="{{ old('settings.qr_camera_index', $settings['camera']['qr_camera_index'] ?? '0') }}">
                            <input type="hidden" id="qr_camera_deviceid_val" value="">
                            <div id="qr-preview-wrap" class="hidden">
                                <div class="relative bg-black rounded-lg overflow-hidden">
                                    <video id="qr-preview-video" autoplay muted playsinline
                                           class="w-full rounded-lg" style="object-fit:contain; max-height:180px;"></video>
                                    <button type="button" onclick="stopCamPreview('qr')"
                                        class="absolute top-1 right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs flex items-center justify-center">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1 text-center">
                                    <i class="fas fa-microchip mr-1"></i>
                                    <span id="qr-preview-specs">mendeteksi...</span>
                                </p>
                            </div>
                        </div>

                        {{-- Foto Wajah Camera --}}
                        <div class="space-y-2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                <i class="fas fa-portrait mr-1 text-indigo-500"></i> Kamera Foto Wajah
                            </label>
                            <select id="photo_camera_select" onchange="onCameraSelect('photo')"
                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500">
                                <option value="">— Mendeteksi kamera... —</option>
                            </select>
                            <input type="hidden" name="settings[photo_camera_index]" id="photo_camera_index_val"
                                   value="{{ old('settings.photo_camera_index', $settings['camera']['photo_camera_index'] ?? '1') }}">
                            <input type="hidden" id="photo_camera_deviceid_val" value="">
                            <div id="photo-preview-wrap" class="hidden">
                                <div class="relative bg-black rounded-lg overflow-hidden">
                                    <video id="photo-preview-video" autoplay muted playsinline
                                           class="w-full rounded-lg" style="object-fit:contain; max-height:180px;"></video>
                                    <button type="button" onclick="stopCamPreview('photo')"
                                        class="absolute top-1 right-1 w-6 h-6 bg-red-500 hover:bg-red-600 text-white rounded-full text-xs flex items-center justify-center">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 mt-1 text-center">
                                    <i class="fas fa-microchip mr-1"></i>
                                    <span id="photo-preview-specs">mendeteksi...</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        Daftar kamera otomatis terisi saat halaman dibuka. Pilih kamera → preview langsung muncul.
                    </p>

                    {{-- Action Button --}}
                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-all duration-200 shadow">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Setting Kamera
                        </button>
                    </div>
                </div>
            </x-card>
        </form>
    </div>

    @push('scripts')
    <script>
    const resMap = {
        'sd':  { width: { ideal: 640  }, height: { ideal: 480  } },
        'hd':  { width: { ideal: 1280 }, height: { ideal: 720  } },
        'fhd': { width: { ideal: 1920 }, height: { ideal: 1080 } },
    };
    function selectResCard(clickedLabel, group, color) {
        document.querySelectorAll('[data-group="' + group + '"]').forEach(function(card) {
            card.style.borderColor = '';
            card.style.background  = '';
        });
        var card = clickedLabel.querySelector('[data-group="' + group + '"]');
        if (card) {
            card.style.borderColor = color;
            card.style.background  = color + '22';
        }
        var role = group === 'res-qr' ? 'qr' : 'photo';
        var radio = clickedLabel.querySelector('input[type=radio]');
        var resKey = radio ? radio.value : 'hd';
        if (typeof camStreams !== 'undefined' && camStreams[role]) {
            var deviceId = camStreams[role].getVideoTracks()[0]?.getSettings()?.deviceId;
            if (deviceId) {
                stopCamPreview(role);
                openCamPreview(role, deviceId, resMap[resKey]);
            }
        }
    }

    const camStreams = { qr: null, photo: null };
    let allCameras  = [];

    async function initCameraDropdowns() {
        try {
            const tempStream = await navigator.mediaDevices.getUserMedia({ video: true });
            tempStream.getTracks().forEach(t => t.stop());
            const devices = await navigator.mediaDevices.enumerateDevices();
            allCameras = devices.filter(d => d.kind === 'videoinput');

            const localQrDeviceId    = localStorage.getItem('absensi_qr_camera_deviceid') || '';
            const localPhotoDeviceId = localStorage.getItem('absensi_photo_camera_deviceid') || '';
            const _qrRaw    = document.getElementById('qr_camera_index_val').value;
            const _photoRaw = document.getElementById('photo_camera_index_val').value;
            const savedQr    = isNaN(parseInt(_qrRaw))    ? 0 : parseInt(_qrRaw);
            const savedPhoto = isNaN(parseInt(_photoRaw)) ? 1 : parseInt(_photoRaw);

            function resolveIdx(localDeviceId, dbIdx) {
                if (localDeviceId) {
                    const found = allCameras.findIndex(c => c.deviceId === localDeviceId);
                    if (found >= 0) return found;
                }
                return dbIdx;
            }
            const resolvedQr    = resolveIdx(localQrDeviceId, savedQr);
            const resolvedPhoto = resolveIdx(localPhotoDeviceId, savedPhoto);

            ['qr_camera_select', 'photo_camera_select'].forEach((selectId, si) => {
                const sel = document.getElementById(selectId);
                sel.innerHTML = '';
                const resolvedIdx = si === 0 ? resolvedQr : resolvedPhoto;
                allCameras.forEach((cam, idx) => {
                    const lbl = cam.label || ('Kamera ' + idx);
                    const opt = document.createElement('option');
                    opt.value = idx;
                    opt.textContent = `[${idx}] ${lbl}`;
                    if (idx === resolvedIdx) opt.selected = true;
                    sel.appendChild(opt);
                });
                if (allCameras.length === 0) {
                    const opt = document.createElement('option');
                    opt.textContent = '— Tidak ada kamera terdeteksi —';
                    sel.appendChild(opt);
                }
            });

            if (allCameras[resolvedQr]) {
                document.getElementById('qr_camera_deviceid_val').value = allCameras[resolvedQr].deviceId;
                openCamPreview('qr', allCameras[resolvedQr].deviceId);
            }
            if (allCameras[resolvedPhoto]) {
                document.getElementById('photo_camera_deviceid_val').value = allCameras[resolvedPhoto].deviceId;
                openCamPreview('photo', allCameras[resolvedPhoto].deviceId);
            }
        } catch (err) {
            console.warn('Gagal deteksi kamera:', err.message);
        }
    }

    function onCameraSelect(role) {
        const sel    = document.getElementById(role + '_camera_select');
        const idx    = parseInt(sel.value);
        const cam    = allCameras[idx];
        const hidden = document.getElementById(role + '_camera_index_val');
        const hiddenDev = document.getElementById(role + '_camera_deviceid_val');
        if (hidden)    hidden.value    = idx;
        if (hiddenDev) hiddenDev.value = cam?.deviceId || '';
        if (cam?.deviceId) {
            localStorage.setItem('absensi_' + role + '_camera_deviceid', cam.deviceId);
        }
        stopCamPreview(role);
        if (cam) openCamPreview(role, cam.deviceId);
    }

    async function openCamPreview(role, deviceId, resConstraint) {
        try {
            const videoConstraint = resConstraint
                ? { deviceId: { exact: deviceId }, width: resConstraint.width, height: resConstraint.height }
                : { deviceId: { exact: deviceId } };
            const stream = await navigator.mediaDevices.getUserMedia({ video: videoConstraint });
            camStreams[role] = stream;
            const video = document.getElementById(role + '-preview-video');
            video.srcObject = stream;
            document.getElementById(role + '-preview-wrap').classList.remove('hidden');
            video.onloadedmetadata = () => {
                const track    = stream.getVideoTracks()[0];
                const settings = track.getSettings();
                const specs    = document.getElementById(role + '-preview-specs');
                if (specs) {
                    const w   = settings.width  || video.videoWidth;
                    const h   = settings.height || video.videoHeight;
                    const fps = (settings.frameRate || 0).toFixed(0);
                    specs.textContent = `${w} × ${h} px @ ${fps} fps`;
                }
            };
        } catch (err) {
            console.error('Gagal preview kamera', role, ':', err.message);
        }
    }

    function stopCamPreview(role) {
        if (camStreams[role]) {
            camStreams[role].getTracks().forEach(t => t.stop());
            camStreams[role] = null;
        }
        document.getElementById(role + '-preview-wrap').classList.add('hidden');
        const video = document.getElementById(role + '-preview-video');
        if (video) video.srcObject = null;
    }

    document.addEventListener('DOMContentLoaded', function() {
        initCameraDropdowns();
    });
    </script>
    @endpush
</x-app-layout>
