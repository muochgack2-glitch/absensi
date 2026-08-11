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
        }

        .page {
            width: 210mm;
            height: 297mm;
            padding: 5mm;
            page-break-after: always;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            width: 33.33%;
            height: 99mm;
            border: 1px solid #ccc;
            padding: 2mm;
            text-align: center;
            vertical-align: top;
            page-break-inside: avoid;
        }

        .card-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            font-size: 10px;
        }

        .qr-img {
            width: 45mm;
            height: 45mm;
            margin-bottom: 2mm;
            border: 1px dashed #999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-img img {
            max-width: 100%;
            max-height: 100%;
        }

        .nis {
            font-weight: bold;
            font-family: monospace;
            font-size: 9px;
            margin-bottom: 1mm;
        }

        .nama {
            font-size: 8px;
            margin-bottom: 1mm;
            line-height: 1.2;
        }

        .sekolah {
            font-size: 8px;
            color: #666;
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
        <table>
            @php $row = 0; @endphp
            @foreach($cards as $idx => $student)
                @if($idx % 3 == 0)
                    @if($idx > 0)
                        </tr>
                    @endif
                    <tr>
                @endif
                <td>
                    @if($student)
                        <div class="card-content">
                            <div class="qr-img">
                                @if(!empty($student['qr_code_base64']))
                                    <img src="data:image/png;base64,{{ $student['qr_code_base64'] }}" alt="QR">
                                @else
                                    <span style="color: #999; font-size: 8px;">No QR</span>
                                @endif
                            </div>
                            <div class="nis">{{ $student['nis'] ?? 'N/A' }}</div>
                            <div class="nama">
                                {{ $student['nama'] ?? 'N/A' }}
                                @if($includeClass && !empty($student['kelas']['nama_kelas']))
                                    / {{ $student['kelas']['nama_kelas'] ?? 'N/A' }}
                                @endif
                            </div>
                            <div class="sekolah">{{ $schoolName }}</div>
                        </div>
                    @endif
                </td>
            @endforeach
            </tr>
        </table>
    </div>
@endforeach
</body>
</html>
