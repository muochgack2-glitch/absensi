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
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            width: 100%;
        }

        body {
            background: #eee;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
            font-size: 24px;
        }

        .page-info {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 5mm;
            page-break-after: always;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            background: white;
        }

        /* Table untuk grid 3x3 */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .grid-table tr {
            height: 60mm;
            page-break-inside: avoid;
        }

        .grid-table td {
            width: 66.666mm;
            height: 60mm;
            border: 1px solid #000;
            padding: 1.5mm;
            vertical-align: top;
            text-align: center;
            background: #fff;
        }

        /* Card content */
        .card {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .card-qr {
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

        .card-qr img {
            max-width: 47mm;
            max-height: 47mm;
            width: auto;
            height: auto;
        }

        .card-qr span {
            color: #ccc;
            font-size: 10px;
        }

        .card-text {
            width: 100%;
            font-size: 10px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            margin-bottom: 0.3mm;
            color: #000;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card-nama {
            width: 100%;
            font-size: 9px;
            margin-bottom: 0.3mm;
            color: #000;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .card-kelas {
            width: 100%;
            font-size: 9px;
            color: #000;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .page-number {
            text-align: center;
            margin-top: 10px;
            color: #999;
            font-size: 12px;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            body {
                background: white;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }
            h1, .page-info, .page-number {
                display: none !important;
            }
            .page {
                box-shadow: none;
                margin: 0;
                padding: 5mm;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Kartu QR Code Siswa</h1>
    <p class="page-info">Layout: 3x3 (9 kartu per halaman A4)</p>

    @foreach($pages as $pageIdx => $cards)
    <div class="page">
        <table class="grid-table" cellspacing="0" cellpadding="0">
            @for($row = 0; $row < 3; $row++)
            <tr>
                @for($col = 0; $col < 3; $col++)
                    @php $cardIdx = ($row * 3) + $col; $student = $cards[$cardIdx] ?? null; @endphp
                    <td>
                        @if($student)
                        <div class="card">
                            <div class="card-qr">
                                @if(!empty($student['qr_code_base64']))
                                <img src="data:image/png;base64,{{ $student['qr_code_base64'] }}" alt="QR" />
                                @else
                                <span>-</span>
                                @endif
                            </div>
                            <div class="card-text">{{ $student['nis'] ?? '' }}</div>
                            <div class="card-nama" style="text-transform: uppercase;">{{ $student['nama'] ?? '' }}</div>
                            @if($includeClass && !empty($student['kelas']['nama_kelas']))
                            <div class="card-kelas" style="text-transform: uppercase;">{{ $student['kelas']['nama_kelas'] ?? '' }}</div>
                            @endif
                        </div>
                        @endif
                    </td>
                @endfor
            </tr>
            @endfor
        </table>
    </div>
    <div class="page-number">Halaman {{ $pageIdx + 1 }}</div>
    @endforeach

</div>

</body>
</html>
