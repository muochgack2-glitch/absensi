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
            margin: 0;
            padding: 0;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 5mm;
            page-break-after: always;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        tr {
            page-break-inside: avoid;
        }

        td {
            border: 1px solid #000;
            width: 33.333%;
            height: 72mm;
            padding: 2mm;
            text-align: center;
            vertical-align: top;
            page-break-inside: avoid;
        }

        .card-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
        }

        .qr-img {
            width: 50mm;
            height: 50mm;
            border: 1px dashed #666;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1mm;
            background: #fff;
        }

        .qr-img img {
            max-width: 100%;
            max-height: 100%;
        }

        .text-nis {
            font-weight: bold;
            font-size: 10px;
            font-family: monospace;
            margin-bottom: 0.5mm;
            color: #000;
        }

        .text-nama {
            font-size: 9px;
            margin-bottom: 0.5mm;
            color: #000;
            line-height: 1.2;
        }

        .text-sekolah {
            font-size: 8px;
            color: #333;
            line-height: 1.2;
        }

        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>

@foreach($pages as $pageIdx => $cards)
<div class="page">
    <table>
        @for($row = 0; $row < 3; $row++)
        <tr>
            @for($col = 0; $col < 3; $col++)
                @php
                    $cardIdx = ($row * 3) + $col;
                    $student = $cards[$cardIdx] ?? null;
                @endphp
                <td>
                    @if($student)
                    <div class="card-container">
                        <div class="qr-img">
                            @if(!empty($student['qr_code_base64']))
                            <img src="data:image/png;base64,{{ $student['qr_code_base64'] }}" alt="QR" />
                            @else
                            <span style="color: #ccc;">-</span>
                            @endif
                        </div>
                        <div class="text-nis">{{ $student['nis'] ?? '' }}</div>
                        <div class="text-nama">{{ $student['nama'] ?? '' }}</div>
                        @if($includeClass && !empty($student['kelas']['nama_kelas']))
                        <div class="text-kelas">{{ $student['kelas']['nama_kelas'] ?? '' }}</div>
                        @endif
                        <div class="text-sekolah">{{ $schoolName }}</div>
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
