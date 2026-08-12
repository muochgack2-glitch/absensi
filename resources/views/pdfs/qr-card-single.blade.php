<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu QR - {{ $student->nama }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .card {
            width: 50mm;
            height: 50mm;
            border: 2px solid #000;
            padding: 2mm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .qr-container {
            width: 40mm;
            height: 40mm;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5mm;
            background: #fff;
        }

        .qr-container img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .info {
            width: 100%;
            text-align: center;
            font-size: 7px;
            line-height: 1;
        }

        .nis {
            font-weight: bold;
            font-family: monospace;
            margin-bottom: 0.2mm;
        }

        .nama {
            text-transform: uppercase;
            margin-bottom: 0.2mm;
        }

        .kelas {
            text-transform: uppercase;
        }

        @page {
            size: A4;
            margin: 10mm;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .card {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="qr-container">
        @if(!empty($qr_code_base64))
        <img src="data:{{ $qr_code_mime ?? 'image/png' }};base64,{{ $qr_code_base64 }}" alt="QR Code" />
        @else
        <span style="color: #ccc;">No QR</span>
        @endif
    </div>
    <div class="info">
        <div class="nis">{{ $student->nis }}</div>
        <div class="nama">{{ $student->nama }}</div>
        <div class="kelas">{{ $student->kelas?->nama_kelas ?? '' }}</div>
    </div>
</div>

</body>
</html>
