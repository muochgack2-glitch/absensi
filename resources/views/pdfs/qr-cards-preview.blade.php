<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview - Kartu QR Code Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #eee;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .page {
            width: 210mm;
            height: 297mm;
            background: white;
            padding: 5mm;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            page-break-after: always;
        }

        table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }

        td {
            border: 2px solid #333;
            width: 33.333%;
            height: 79mm;
            padding: 3mm;
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
        }

        .qr-img {
            width: 35mm;
            height: 35mm;
            border: 2px dashed #666;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3mm;
            background: #f9f9f9;
        }

        .qr-img img {
            max-width: 100%;
            max-height: 100%;
        }

        .text-nis {
            font-weight: bold;
            font-size: 11px;
            font-family: monospace;
            margin-bottom: 2mm;
            color: #000;
        }

        .text-nama {
            font-size: 10px;
            margin-bottom: 2mm;
            color: #000;
            line-height: 1.3;
        }

        .text-sekolah {
            font-size: 9px;
            color: #333;
            line-height: 1.3;
        }

        .page-number {
            text-align: center;
            margin-top: 10px;
            color: #999;
            font-size: 12px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Preview: Kartu QR Code Siswa</h1>

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
                                <span style="color: #ccc; font-size: 12px;">No QR</span>
                                @endif
                            </div>
                            <div class="text-nis">{{ $student['nis'] ?? '' }}</div>
                            <div class="text-nama">
                                @if($includeClass && !empty($student['kelas']['nama_kelas']))
                                {{ $student['nama'] ?? '' }} / {{ $student['kelas']['nama_kelas'] ?? '' }}
                                @else
                                {{ $student['nama'] ?? '' }}
                                @endif
                            </div>
                            <div class="text-sekolah">{{ $schoolName }}</div>
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
