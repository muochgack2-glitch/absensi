<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu QR Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
            line-height: 1;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 10mm;
            background: white;
            page-break-after: always;
            overflow: hidden;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat({{ $dimensions['cols'] }}, {{ $dimensions['card_width_mm'] }}mm);
            grid-template-rows: repeat({{ $dimensions['rows'] }}, {{ $dimensions['card_height_mm'] }}mm);
            gap: {{ $dimensions['grid_gap_mm'] }}mm;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .card {
            width: {{ $dimensions['card_width_mm'] }}mm;
            height: {{ $dimensions['card_height_mm'] }}mm;
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            background: white;
            padding: 1.5mm;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        .card-qr {
            width: {{ $dimensions['qr_size_mm'] }}mm;
            height: {{ $dimensions['qr_size_mm'] }}mm;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-bottom: 0.5mm;
            margin-top: 0;
        }

        .card-qr img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-nis {
            font-size: 10px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            margin-bottom: 0.5mm;
            letter-spacing: 0.5px;
            text-align: center;
            line-height: 1;
        }

        .card-nama {
            font-size: 9px;
            text-align: center;
            line-height: 1.1;
            max-width: calc({{ $dimensions['card_width_mm'] }}mm - 3mm);
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            margin-bottom: 0.5mm;
            word-break: break-word;
        }

        .card-sekolah {
            font-size: 9px;
            color: #666;
            text-align: center;
            line-height: 1;
            max-width: calc({{ $dimensions['card_width_mm'] }}mm - 3mm);
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
        }

        /* Print styles */
        @media print {
            body, html {
                margin: 0;
                padding: 0;
                background: white;
            }
            .page {
                margin: 0;
                padding: 10mm;
                box-shadow: none;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    @foreach($pages as $pageIndex => $pageStudents)
        <div class="page">
            <div class="cards-grid">
                @foreach($pageStudents as $student)
                    <div class="card">
                        <div class="card-qr">
                            @php
                                // Generate QR code as data URL
                                $qrService = app(\App\Services\QRCodeService::class);
                                $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                                    ->size(300)
                                    ->errorCorrection('H')
                                    ->generate($student['nis']);
                                $qrDataUrl = 'data:image/svg+xml;base64,' . base64_encode($qrSvg);
                            @endphp
                            <img src="{{ $qrDataUrl }}" alt="QR Code {{ $student['nis'] }}" />
                        </div>
                        <div class="card-nis">{{ $student['nis'] }}</div>
                        <div class="card-nama">
                            @if($includeClass && isset($student['kelas']))
                                {{ $student['nama'] }} / {{ $student['kelas']['nama_kelas'] ?? '' }}
                            @else
                                {{ $student['nama'] }}
                            @endif
                        </div>
                        <div class="card-sekolah">{{ $schoolName }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</body>
</html>
