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

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: white;
            color: #000;
        }

        /* A4 Page Setup */
        .print-page {
            width: 210mm;
            height: 297mm;
            margin: 0;
            padding: 5mm;
            page-break-after: always;
            position: relative;
        }

        /* Grid 3x3 Layout */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            grid-auto-rows: 60mm;
            gap: 3mm;
            width: 100%;
        }

        /* Kartu Individual */
        .card {
            width: 100%;
            max-width: 50mm;
            height: 60mm;
            border: 1px solid #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            background: white;
            padding: 1mm;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        /* QR Code Container */
        .card-qr {
            width: 45mm;
            height: 45mm;
            background: #f5f5f5;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5mm;
            margin-top: 0;
            flex-shrink: 0;
            overflow: hidden;
        }

        .card-qr img,
        .card-qr svg {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* NIS */
        .card-nis {
            font-size: 9pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            margin-bottom: 0.3mm;
            letter-spacing: 0.3px;
            text-align: center;
            line-height: 1;
        }

        /* Nama / Kelas (1 baris) */
        .card-nama {
            font-size: 8pt;
            text-align: center;
            line-height: 1;
            max-width: 43mm;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            line-clamp: 1;
            margin-bottom: 0.2mm;
            font-weight: 500;
        }

        /* Sekolah */
        .card-sekolah {
            font-size: 8pt;
            color: #666;
            text-align: center;
            line-height: 1;
            max-width: 43mm;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            line-clamp: 1;
        }

        /* Empty card (placeholder) */
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

        /* Print optimization */
        @page {
            margin: 0;
            padding: 0;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .print-page {
                margin: 0;
                padding: 10mm;
                box-shadow: none;
            }
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
                                    <img src="data:image/png;base64,{{ $student['qr_code_base64'] }}" alt="QR Code" style="width: 100%; height: 100%;">
                                @else
                                    {{-- Fallback: placeholder jika QR belum ada --}}
                                    <div style="width: 100%; height: 100%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 8pt; color: #999;">
                                        No QR
                                    </div>
                                @endif
                            </div>
                            <div class="card-nis">{{ $student['nis'] ?? 'N/A' }}</div>
                            <div class="card-nama">
                                {{ $student['nama'] ?? 'N/A' }}
                                @if($includeClass && !empty($student['kelas']))
                                    / {{ $student['kelas']['nama_kelas'] ?? 'N/A' }}
                                @endif
                            </div>
                            <div class="card-sekolah">{{ $schoolName }}</div>
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
