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
            padding: 10mm;
            page-break-after: always;
            position: relative;
        }

        /* Grid 3x3 Layout */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 50mm);
            grid-template-rows: repeat(3, 60mm);
            gap: 4mm;
            justify-content: center;
            height: 100%;
        }

        /* Kartu Individual */
        .card {
            width: 50mm;
            height: 60mm;
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

        /* QR Code Container */
        .card-qr {
            width: 47mm;
            height: 47mm;
            background: #f5f5f5;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1mm;
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
            font-size: 10pt;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            margin-bottom: 0.5mm;
            letter-spacing: 0.5px;
            text-align: center;
        }

        /* Nama / Kelas (1 baris) */
        .card-nama {
            font-size: 9pt;
            text-align: center;
            line-height: 1.2;
            max-width: 44mm;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            line-clamp: 1;
            margin-bottom: 0.5mm;
            font-weight: 500;
        }

        /* Sekolah */
        .card-sekolah {
            font-size: 9pt;
            color: #666;
            text-align: center;
            line-height: 1.2;
            max-width: 44mm;
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
                                            @if(!empty($student['qr_code_path']) && file_exists(storage_path('app/public/' . $student['qr_code_path'])))
                                    {{-- Jika file SVG, embed langsung --}}
                                    @if(str_ends_with($student['qr_code_path'], '.svg'))
                                        {!! file_get_contents(storage_path('app/public/' . $student['qr_code_path'])) !!}
                                    @else
                                        {{-- Jika PNG atau format lain --}}
                                        <img src="{{ asset('storage/' . $student['qr_code_path']) }}" alt="QR Code">
                                    @endif
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
