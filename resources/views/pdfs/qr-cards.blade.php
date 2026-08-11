<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu QR Code Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: white;
            margin: 0;
            padding: 0;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 3mm;
            page-break-after: always;
            margin: 0;
        }

        .cards-table {
            width: 100%;
            border-collapse: collapse;
        }

        .card-cell {
            width: 33.333%;
            height: 85mm;
            border: 1px solid #ccc;
            padding: 3mm;
            text-align: center;
            vertical-align: top;
            page-break-inside: avoid;
        }

        .card-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
        }

        .qr-container {
            width: 40mm;
            height: 40mm;
            border: 1px dashed #999;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3mm;
            flex-shrink: 0;
        }

        .qr-container img {
            max-width: 100%;
            max-height: 100%;
        }

        .nis {
            font-weight: bold;
            font-family: 'Courier New', monospace;
            font-size: 10px;
            margin-bottom: 2mm;
            line-height: 1;
            color: #000;
        }

        .nama {
            font-size: 9px;
            margin-bottom: 2mm;
            word-wrap: break-word;
            line-height: 1.2;
            min-height: 12px;
            color: #000;
        }

        .sekolah {
            font-size: 9px;
            color: #333;
            word-wrap: break-word;
            line-height: 1.2;
            min-height: 12px;
        }

        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
@foreach($pages as $pageIndex => $cards)
    <div class="page">
        <table class="cards-table">
            @for($i = 0; $i < count($cards); $i += 3)
            <tr>
                @for($j = 0; $j < 3; $j++)
                    @php $idx = $i + $j; @endphp
                    <td class="card-cell">
                        @if(isset($cards[$idx]) && $cards[$idx])
                            @php $student = $cards[$idx]; @endphp
                            <div class="card-inner">
                                <div class="qr-container">
                                    @if(!empty($student['qr_code_base64']))
                                        <img src="data:image/png;base64,{{ $student['qr_code_base64'] }}" alt="QR" />
                                    @else
                                        <span style="color: #999; font-size: 8px;">No QR</span>
                                    @endif
                                </div>
                                <div class="nis">{{ $student['nis'] ?? '' }}</div>
                                <div class="nama">
                                    @if($includeClass && !empty($student['kelas']['nama_kelas']))
                                        {{ $student['nama'] ?? '' }} / {{ $student['kelas']['nama_kelas'] ?? '' }}
                                    @else
                                        {{ $student['nama'] ?? '' }}
                                    @endif
                                </div>
                                <div class="sekolah">{{ $schoolName }}</div>
                            </div>
                        @endif
                    </td>
                @endfor
            </tr>
            @endfor
        </table>
    </div>
@endforeach
</body>
</html>
