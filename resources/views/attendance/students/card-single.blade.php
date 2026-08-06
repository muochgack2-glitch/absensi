<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Pelajar - {{ $student->nama }}</title>
    <style>
        /* A5 Landscape: 210mm x 148mm */
        @page {
            size: 210mm 148mm landscape;
            margin: 8mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #1a1a1a;
            width: 190mm;
        }

        /* ===== CARD ===== */
        .card {
            width: 190mm;
            height: 130mm;
            border: 1.5px solid #d1d5db;
            border-radius: 5mm;
            overflow: hidden;
            position: relative;
            background: #ffffff;
        }

        /* Header */
        .card-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 3mm 5mm;
            display: flex;
            align-items: center;
            height: 18mm;
        }

        .card-header-logo {
            width: 13mm;
            height: 13mm;
            border-radius: 50%;
            background: rgba(255,255,255,0.15);
            margin-right: 3mm;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-header-logo img {
            width: 13mm;
            height: 13mm;
            object-fit: contain;
        }

        .card-header-text {
            flex: 1;
        }

        .school-name {
            font-size: 9pt;
            font-weight: bold;
            letter-spacing: 0.5pt;
            line-height: 1.2;
        }

        .card-title {
            font-size: 7pt;
            opacity: 0.85;
            letter-spacing: 1pt;
            margin-top: 1mm;
            text-transform: uppercase;
        }

        /* Body */
        .card-body {
            display: flex;
            height: calc(130mm - 18mm - 8mm);
            padding: 4mm 5mm;
            gap: 5mm;
        }

        /* Photo */
        .card-photo {
            width: 28mm;
            height: 36mm;
            border: 1px solid #d1d5db;
            border-radius: 2mm;
            overflow: hidden;
            flex-shrink: 0;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card-photo .initials {
            font-size: 22pt;
            font-weight: bold;
            color: #9ca3af;
        }

        /* Info */
        .card-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 1mm 0;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .info-table .lbl {
            width: 18mm;
            color: #6b7280;
            font-weight: 600;
        }

        .info-table .sep {
            width: 4mm;
            text-align: center;
            color: #6b7280;
        }

        .info-table .val {
            font-weight: 700;
            color: #111827;
        }

        .info-table .val-name {
            font-size: 10pt;
            font-weight: 800;
            color: #111827;
        }

        /* QR Code Area */
        .card-qr-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 40mm;
            flex-shrink: 0;
        }

        .card-qr-box {
            width: 36mm;
            height: 36mm;
            border: 1.5px solid #e5e7eb;
            border-radius: 2mm;
            padding: 1.5mm;
            background: white;
        }

        .card-qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .card-qr-nis {
            margin-top: 1.5mm;
            font-size: 7pt;
            font-weight: 700;
            color: #374151;
            text-align: center;
            letter-spacing: 0.5pt;
        }

        .card-qr-label {
            font-size: 6pt;
            color: #9ca3af;
            text-align: center;
            margin-top: 0.5mm;
        }

        .no-qr-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6pt;
            color: #9ca3af;
            text-align: center;
        }

        /* Footer */
        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1.5mm 5mm;
            background: #f8fafc;
            border-top: 0.5px solid #e5e7eb;
            font-size: 6pt;
            color: #9ca3af;
            text-align: center;
        }

        /* Divider antara foto+info dan QR */
        .divider {
            width: 0.5px;
            background: #e5e7eb;
            margin: 2mm 0;
        }
    </style>
</head>
<body>
    <div class="card">
        {{-- Header --}}
        <div class="card-header">
            <div class="card-header-logo">
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo">
                @endif
            </div>
            <div class="card-header-text">
                <div class="school-name">{{ strtoupper($schoolName) }}</div>
                <div class="card-title">Kartu Pelajar &bull; Tahun Ajaran {{ $tahunAjaran }}</div>
            </div>
        </div>

        {{-- Body --}}
        <div class="card-body">

            {{-- Foto --}}
            <div class="card-photo">
                @if($fotoBase64)
                    <img src="{{ $fotoBase64 }}" alt="Foto">
                @else
                    <span class="initials">{{ strtoupper(substr($student->nama, 0, 1)) }}</span>
                @endif
            </div>

            {{-- Info Siswa --}}
            <div class="card-info">
                <table class="info-table">
                    <tr>
                        <td class="lbl">Nama</td>
                        <td class="sep">:</td>
                        <td class="val val-name">{{ $student->nama }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">NIS</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $student->nis }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">Kelas</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $student->kelas->nama_kelas ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="lbl">T.A.</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $tahunAjaran }}</td>
                    </tr>
                    @if($student->no_hp_ortu)
                    <tr>
                        <td class="lbl">No. HP</td>
                        <td class="sep">:</td>
                        <td class="val">{{ $student->no_hp_ortu }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            {{-- Garis pembatas --}}
            <div class="divider"></div>

            {{-- QR Code --}}
            <div class="card-qr-section">
                <div class="card-qr-box">
                    @if($qrBase64)
                        <img src="{{ $qrBase64 }}" alt="QR Absensi" style="width:100%;height:100%;">
                    @else
                        <div class="no-qr-placeholder">QR Code<br>belum ada</div>
                    @endif
                </div>
                <div class="card-qr-nis">{{ $student->nis }}</div>
                <div class="card-qr-label">Scan untuk Absensi</div>
            </div>

        </div>

        {{-- Footer --}}
        @if($schoolAddress)
        <div class="card-footer">{{ $schoolAddress }}</div>
        @endif
    </div>
</body>
</html>
