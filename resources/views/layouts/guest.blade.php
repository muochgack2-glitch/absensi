<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — {{ config('app.name', 'Absensi QR') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }

        body { margin: 0; min-height: 100vh; display: flex; background: #0f0c29; }

        /* ── Left Panel ── */
        .left-panel {
            display: none;
            width: 55%;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
        }
        @media (min-width: 1024px) { .left-panel { display: flex; flex-direction: column; justify-content: center; align-items: center; } }

        .left-panel .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.35;
            animation: float 8s ease-in-out infinite;
        }
        .orb-1 { width: 500px; height: 500px; background: #6c63ff; top: -100px; left: -100px; animation-delay: 0s; }
        .orb-2 { width: 400px; height: 400px; background: #48cfad; bottom: -80px; right: -80px; animation-delay: 3s; }
        .orb-3 { width: 300px; height: 300px; background: #a855f7; top: 50%; left: 50%; transform: translate(-50%,-50%); animation-delay: 1.5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-30px) scale(1.05); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(-50%,-50%) scale(1); }
            50% { transform: translate(-50%,-55%) scale(1.07); }
        }
        .orb-3 { animation-name: float2; }

        /* Floating grid lines */
        .grid-bg {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        .left-content { position: relative; z-index: 2; text-align: center; padding: 48px; }
        .left-content .badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.1); backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.15);
            color: #a5b4fc; font-size: 12px; font-weight: 600;
            padding: 6px 16px; border-radius: 100px; margin-bottom: 32px;
            letter-spacing: .5px; text-transform: uppercase;
        }
        .left-content h1 { font-size: 48px; font-weight: 800; color: #fff; margin: 0 0 16px; line-height: 1.15; }
        .left-content h1 span { background: linear-gradient(135deg, #a78bfa, #60a5fa, #34d399); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .left-content p { font-size: 16px; color: rgba(255,255,255,.6); max-width: 380px; margin: 0 auto 40px; line-height: 1.7; }

        .feature-list { display: flex; flex-direction: column; gap: 14px; text-align: left; }
        .feature-item { display: flex; align-items: center; gap: 12px; color: rgba(255,255,255,.8); font-size: 14px; }
        .feature-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }

        /* ── Right Panel ── */
        .right-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            background: #0f172a;
            position: relative;
        }
        .right-panel::before {
            content: '';
            position: absolute; inset: 0;
            background: radial-gradient(ellipse at 70% 30%, rgba(99,102,241,.12) 0%, transparent 60%);
        }

        .login-card {
            position: relative; z-index: 1;
            width: 100%; max-width: 420px;
        }

        .card-header { text-align: center; margin-bottom: 36px; }
        .logo-wrap {
            width: 64px; height: 64px; margin: 0 auto 18px;
            background: linear-gradient(135deg, #6c63ff, #a855f7);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 32px rgba(108,99,255,.4);
        }
        .logo-wrap img { width: 44px; height: 44px; object-fit: contain; }
        .logo-wrap i { color: #fff; font-size: 26px; }

        .card-header h2 { font-size: 26px; font-weight: 800; color: #f1f5f9; margin: 0 0 6px; }
        .card-header p { font-size: 14px; color: #64748b; margin: 0; }

        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #94a3b8; margin-bottom: 8px; letter-spacing: .3px; }

        .input-wrap { position: relative; }
        .input-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #475569; font-size: 14px; pointer-events: none; }
        .form-input {
            width: 100%; padding: 12px 14px 12px 40px;
            background: #1e293b; border: 1px solid #334155;
            border-radius: 12px; color: #f1f5f9; font-size: 14px;
            outline: none; transition: all .2s; appearance: none;
        }
        .form-input:focus { border-color: #6c63ff; box-shadow: 0 0 0 3px rgba(108,99,255,.2); background: #1e2d40; }
        .form-input::placeholder { color: #475569; }

        .toggle-pw { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #475569; cursor: pointer; font-size: 14px; transition: color .2s; }
        .toggle-pw:hover { color: #94a3b8; }

        .error-msg { font-size: 12px; color: #f87171; margin-top: 6px; display: flex; align-items: center; gap: 4px; }

        .row-options { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .remember-label { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; cursor: pointer; user-select: none; }
        .remember-check { width: 16px; height: 16px; accent-color: #6c63ff; cursor: pointer; }
        .forgot-link { font-size: 13px; color: #6c63ff; text-decoration: none; font-weight: 500; transition: color .2s; }
        .forgot-link:hover { color: #a78bfa; }

        .btn-login {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #6c63ff, #a855f7);
            border: none; border-radius: 12px;
            color: #fff; font-size: 15px; font-weight: 700;
            cursor: pointer; position: relative; overflow: hidden;
            transition: transform .15s, box-shadow .15s;
            box-shadow: 0 4px 20px rgba(108,99,255,.4);
        }
        .btn-login:hover { transform: translateY(-1px); box-shadow: 0 8px 28px rgba(108,99,255,.5); }
        .btn-login:active { transform: translateY(0); }
        .btn-login .btn-text { position: relative; z-index: 1; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-login::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.15), transparent);
            opacity: 0; transition: opacity .2s;
        }
        .btn-login:hover::before { opacity: 1; }

        .back-link { display: flex; align-items: center; justify-content: center; gap: 6px; margin-top: 20px; font-size: 13px; color: #475569; text-decoration: none; transition: color .2s; }
        .back-link:hover { color: #94a3b8; }

        .alert-status { padding: 12px 16px; background: rgba(52,211,153,.1); border: 1px solid rgba(52,211,153,.2); border-radius: 10px; color: #34d399; font-size: 13px; margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }
    </style>
</head>
<body>

    {{-- Left decorative panel --}}
    <div class="left-panel">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="grid-bg"></div>

        <div class="left-content">
            <div class="badge"><i class="fas fa-shield-halved"></i> Admin Portal</div>
            <h1>Sistem Absensi<br><span>Digital Modern</span></h1>
            <p>Kelola kehadiran siswa secara real-time dengan teknologi QR Code dan notifikasi WhatsApp otomatis.</p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-icon" style="background: rgba(108,99,255,.2); color:#a78bfa;"><i class="fas fa-qrcode"></i></div>
                    <span>Scan QR Card otomatis & real-time</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon" style="background: rgba(52,211,153,.2); color:#34d399;"><i class="fas fa-camera"></i></div>
                    <span>Foto wajah siswa saat check-in/out</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon" style="background: rgba(96,165,250,.2); color:#60a5fa;"><i class="fab fa-whatsapp"></i></div>
                    <span>Notifikasi WhatsApp ke orang tua</span>
                </div>
                <div class="feature-item">
                    <div class="feature-icon" style="background: rgba(251,191,36,.2); color:#fbbf24;"><i class="fas fa-chart-line"></i></div>
                    <span>Laporan & rekap kehadiran lengkap</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right login panel --}}
    <div class="right-panel">
        <div class="login-card">
            <div class="card-header">
                <div class="logo-wrap">
                    @php $logoUrl = \App\Models\AttendanceSetting::get('app_logo_url', ''); @endphp
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo">
                    @else
                        <i class="fas fa-qrcode"></i>
                    @endif
                </div>
                <h2>{{ \App\Models\AttendanceSetting::get('school_name', 'Absensi QR') }}</h2>
                <p>Masuk ke panel administrasi</p>
            </div>

            {{ $slot }}

            <a href="{{ url('/') }}" class="back-link">
                <i class="fas fa-arrow-left"></i> Kembali ke Scanner
            </a>
        </div>
    </div>

</body>
</html>
