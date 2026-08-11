<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu QR Code Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
            color: #000;
        }

        /* A4 Page Setup */
        .print-page {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 3mm;
            page-break-after: always;
        }

        /* Grid Container - 3 columns */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, auto);
            gap: 2mm;
            width: 100%;
            height: 100%;
        }

        /* Kartu Individual */
        .card {
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            background: white;
            padding: 1mm;
            aspect-ratio: 50/60;
            page-break-inside: avoid;
        }

        /* QR Code Container */
        .card-qr {
            width: 90%;
            aspect-ratio: 1;
            background: #f5f5f5;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5mm;
            flex-shrink: 0;
            overflow: hidden;
        }

        .card-qr img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* NIS */
        .card-nis {
            font-size: 8pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            margin-bottom: 0.2mm;
            text-align: center;
            line-height: 1;
            width: 100%;
        }

        /* Nama / Kelas */
        .card-nama {
            font-size: 7pt;
            text-align: center;
            line-height: 1;
            width: 95%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            margin-bottom: 0.2mm;
            font-weight: 500;
        }

        /* Sekolah */
        .card-sekolah {
            font-size: 7pt;
            color: #666;
            text-align: center;
            line-height: 1;
            width: 95%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Empty card */
        .card.empty {
            border: none;
            background: transparent;
        }

        .card.empty .card-qr,
        .card.empty .card-nis,
        .card.empty .card-nama,
        .card.empty .card-sekolah {
            display: none;
        }

        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    @foreach($pages as $pageIndex => $cards)
        <div class="print-page">
            <div class="cards-grid">
                @foreach($cards as $student)
                    @if($student)
                        <div class="card">
                            <div class="card-qr">
                                @if(!empty($student['qr_code_base64']))
                                    <img src="data:image/png;base64,{{ $student['qr_code_base64'] }}" alt="QR">
                                @else
                                    <div style="font-size: 6pt; color: #999;">No QR</div>
                                @endif
                            </div>
                            <div class="card-nis">{{ substr($student['nis'] ?? '', 0, 8) }}</div>
                            <div class="card-nama">
                                {{ substr($student['nama'] ?? '', 0, 20) }}
                                @if($includeClass && !empty($student['kelas']['nama_kelas']))
                                    / {{ substr($student['kelas']['nama_kelas'] ?? '', 0, 10) }}
                                @endif
                            </div>
                            <div class="card-sekolah">{{ substr($schoolName, 0, 15) }}</div>
                        </div>
                    @else
                        <div class="card empty"></div>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
</body>
</html>
