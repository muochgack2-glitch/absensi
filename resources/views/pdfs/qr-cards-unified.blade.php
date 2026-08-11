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
        }

        .page-info {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 12px;
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

        /* Simple table for 3x3 grid */
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

        .page-number {
            text-align: center;
            margin-top: 5px;
            color: #999;
            font-size: 11px;
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
                margin: 0;
                padding: 0;
            }
            h1, .page-info, .page-number {
                display: none;
            }
            .page {
                margin: 0;
                padding: 5mm;
                box-shadow: none;
                height: auto;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Kartu QR Code Siswa</h1>
    <p class="page-info">3x3 Layout - 9 kartu per halaman</p>

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
                                <img src="data:image/png;base64,{{ $student['qr_code_base64'] }}" />
                                @else
                                <span style="color: #ccc;">-</span>
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
    <div class="page-number">Halaman {{ $pageIdx + 1 }}</div>
    @endforeach

</div>

</body>
</html>
