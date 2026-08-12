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

        html, body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 5mm;
            page-break-after: always;
            margin: 0;
            page-break-inside: avoid;
            position: relative;
        }

        /* DomPDF quirk: Use fixed dimensions for table */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        tr {
            page-break-inside: avoid;
            height: 60mm;
        }

        td {
            border: 1px solid #000;
            width: 66.666mm;
            height: 60mm;
            padding: 1.5mm;
            text-align: center;
            vertical-align: top;
            page-break-inside: avoid;
            background: #fff;
        }

        .card-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100%;
            width: 100%;
        }

        .qr-img {
            width: 47mm;
            height: 47mm;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5mm;
            background: #fff;
            flex-shrink: 0;
        }

        .qr-img img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .text-nis {
            font-weight: bold;
            font-size: 10px;
            font-family: 'Courier New', monospace;
            margin-bottom: 0.3mm;
            color: #000;
            line-height: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 100%;
        }

        .text-nama {
            font-size: 9px;
            margin-bottom: 0.3mm;
            color: #000;
            line-height: 1;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .text-kelas {
            font-size: 9px;
            color: #000;
            line-height: 1;
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @page {
            size: A4 portrait;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>

@foreach($pages as $pageIdx => $cards)
<div class="page">
    <table cellpadding="0" cellspacing="0">
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
                            <img src="data:{{ $student['qr_code_mime'] ?? 'image/png' }};base64,{{ $student['qr_code_base64'] }}" alt="QR" />
                            @else
                            <span style="color: #ccc; font-size: 10px;">-</span>
                            @endif
                        </div>
                        <div class="text-nis">{{ $student['nis'] ?? '' }}</div>
                        <div class="text-nama" style="text-transform: uppercase;">{{ $student['nama'] ?? '' }}</div>
                        @if($includeClass && !empty($student['kelas']['nama_kelas']))
                        <div class="text-kelas" style="text-transform: uppercase;">{{ $student['kelas']['nama_kelas'] ?? '' }}</div>
                        @endif
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
