@php
    $retryAfter = $exception->getHeaders()['Retry-After'] ?? 60;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terlalu Banyak Permintaan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,600,700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fef2f2, #fff7ed, #fefce8);
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 20px;
            max-width: 420px;
            width: 100%;
            padding: 40px 32px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            border: 1px solid #fde8e8;
        }
        .icon {
            width: 72px;
            height: 72px;
            border-radius: 18px;
            background: linear-gradient(135deg, #fecaca, #fde68a);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        }
        h1 {
            font-size: 20px;
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 8px;
        }
        p {
            font-size: 14px;
            color: #78716c;
            line-height: 1.6;
        }
        .countdown {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 20px;
            padding: 10px 20px;
            background: #fef3c7;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            color: #92400e;
        }
        .countdown span {
            font-size: 18px;
            font-weight: 700;
            color: #b91c1c;
        }
        .btn-back {
            display: inline-block;
            margin-top: 24px;
            padding: 11px 28px;
            background: linear-gradient(135deg, #059669, #0d9488);
            color: white;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            box-shadow: 0 4px 12px rgba(5,150,105,0.25);
        }
        .btn-back:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(5,150,105,0.35);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">⏳</div>
        <h1>Terlalu Banyak Permintaan</h1>
        <p>
            Anda terlalu sering mengirim permintaan. 
            Mohon tunggu sebentar sebelum mencoba lagi.
        </p>
        <div class="countdown">
            ⏱ Coba lagi dalam <span id="timer">{{ $retryAfter }}</span> detik
        </div>
        <br>
        <a href="javascript:history.back()" class="btn-back">← Kembali</a>
    </div>

    <script>
        const timer = document.getElementById('timer');
        let seconds = parseInt(timer.textContent);
        const interval = setInterval(() => {
            seconds--;
            timer.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(interval);
                timer.parentElement.innerHTML = '✅ Silakan coba lagi sekarang!';
                timer.parentElement.style.background = '#d1fae5';
                timer.parentElement.style.color = '#065f46';
            }
        }, 1000);
    </script>
</body>
</html>
