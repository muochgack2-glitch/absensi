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
        }

        body {
            background: white;
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 5mm;
            page-break-after: always;
            background: white;
        }

        .cards-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .cards-table tr {
            page-break-inside: avoid;
        }

        .cards-table td {
            width: 50mm;
            height: 50mm;
            border: 1px solid #000;
            padding: 0.5mm;
            text-align: center;
            vertical-align: top;
            background: white;
        }

        .card-inner {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 0.3mm;
        }

        .card-qr {
            width: 40mm;
            height: 40mm;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.2mm;
            background: white;
        }

        .card-qr img {
            width: 40mm;
            height: 40mm;
            object-fit: contain;
        }

        .card-text {
            font-size: 8px;
            font-weight: bold;
            font-family: monospace;
            margin-bottom: 0.1mm;
            line-height: 1;
        }

        .card-nama {
            font-size: 7px;
            margin-bottom: 0.1mm;
            line-height: 1;
            text-transform: uppercase;
        }

        .card-kelas {
            font-size: 7px;
            line-height: 1;
            text-transform: uppercase;
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
            .page {
                margin: 0;
                padding: 5mm;
                box-shadow: none;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

@foreach($pages as $pageIdx => $cards)
<div class="page">
    <table class="cards-table" cellpadding="0" cellspacing="0" border="0">
        @for($i = 0; $i < 3; $i++)
        <tr>
            @for($j = 0; $j < 3; $j++)
                @php $idx = ($i * 3) + $j; $student = $cards[$idx] ?? null; @endphp
                <td>
                    @if($student)
                    <div class="card-inner">
                        <div class="card-qr">
                            @if(!empty($student['qr_code_base64']))
                            <img src="data:{{ $student['qr_code_mime'] ?? 'image/png' }};base64,{{ $student['qr_code_base64'] }}" alt="QR" />
                            @else
                            <span style="color: #ccc; font-size: 8px;">-</span>
                            @endif
                        </div>
                        <div class="card-text">{{ $student['nis'] }}</div>
                        <div class="card-nama">{{ $student['nama'] }}</div>
                        @if($includeClass && $student['kelas'])
                        <div class="card-kelas">{{ $student['kelas']['nama_kelas'] }}</div>
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
