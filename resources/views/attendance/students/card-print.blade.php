<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Pelajar</title>
    <style>
        /* F4 Paper: 215mm x 330mm */
        @page {
            size: 215mm 330mm;
            margin: 8mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #1a1a1a;
        }

        .page {
            page-break-after: always;
            width: 100%;
            height: 314mm; /* F4 height - margins */
        }

        .page:last-child {
            page-break-after: avoid;
        }

        .cards-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .cards-grid td {
            width: 50%;
            padding: 2mm;
            vertical-align: top;
        }

        /* ============== CARD DESIGN ============== */
        .card {
            width: 100%;
            height: 58mm; /* ~58mm per card for 2x5 on F4 */
            border: 1px solid #ccc;
            border-radius: 4mm;
            overflow: hidden;
            position: relative;
            background: #ffffff;
        }

        .card-2x4 {
            height: 72mm;
        }

        .card-2x3 {
            height: 98mm;
        }

        /* Header Bar */
        .card-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: white;
            padding: 2mm 3mm;
            display: flex;
            align-items: center;
            height: 14mm;
        }

        .card-header-logo {
            width: 10mm;
            height: 10mm;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            margin-right: 2mm;
            overflow: hidden;
        }

        .card-header-logo img {
            width: 10mm;
            height: 10mm;
            object-fit: contain;
        }

        .card-header-text {
            flex: 1;
        }

        .card-header-text .school-name {
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 0.3pt;
            line-height: 1.2;
        }

        .card-header-text .card-title {
            font-size: 6pt;
            opacity: 0.9;
            letter-spacing: 0.5pt;
            margin-top: 0.5mm;
        }

        /* Card Body - menggunakan table layout karena DomPDF tidak support flexbox */
        .card-body {
            padding: 2mm 3mm;
            width: 100%;
        }

        .card-body-table {
            width: 100%;
            border-collapse: collapse;
        }

        .card-body-table td {
            vertical-align: middle;
            padding: 0;
        }

        /* Photo Area */
        .card-photo {
            width: 18mm;
            height: 24mm;
            border: 1px solid #ddd;
            border-radius: 2mm;
            overflow: hidden;
            background: #f3f4f6;
            text-align: center;
        }

        .card-photo img {
            width: 18mm;
            height: 24mm;
            object-fit: cover;
        }

        .card-photo .initials {
            font-size: 14pt;
            font-weight: bold;
            color: #6b7280;
            line-height: 24mm;
            display: block;
        }

        /* Info Area */
        .card-info {
            padding-left: 2mm;
            padding-right: 1mm;
        }

        /* QR Code Area */
        .card-qr-wrap {
            width: 24mm;
            text-align: center;
        }

        .card-qr {
            width: 22mm;
            height: 22mm;
            border: 1px solid #e5e7eb;
            border-radius: 2mm;
            padding: 1mm;
            background: white;
            margin: 0 auto;
        }

        .card-qr img {
            width: 20mm;
            height: 20mm;
            object-fit: contain;
        }

        .card-qr-label {
            text-align: center;
            font-size: 5pt;
            color: #9ca3af;
            margin-top: 0.5mm;
        }

        /* Footer */
        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1mm 3mm;
            background: #f8fafc;
            border-top: 0.5px solid #e5e7eb;
            font-size: 5pt;
            color: #9ca3af;
            text-align: center;
        }

        /* Empty card placeholder */
        .card-empty {
            border: 1px dashed #e5e7eb;
            border-radius: 4mm;
            height: 58mm;
        }

        .card-empty-2x4 {
            height: 72mm;
        }

        .card-empty-2x3 {
            height: 98mm;
        }

        /* ============== TABLE LAYOUT ============== */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 0.5mm 0;
            font-size: 7pt;
            vertical-align: top;
        }

        .info-table .lbl {
            width: 14mm;
            color: #6b7280;
            font-weight: 600;
        }

        .info-table .sep {
            width: 3mm;
            text-align: center;
            color: #6b7280;
        }

        .info-table .val {
            font-weight: 700;
            color: #111827;
        }
    </style>
</head>
<body>
    @foreach($pages as $pageIndex => $page)
    <div class="page">
        <table class="cards-grid">
            @for($row = 0; $row < $config['rows']; $row++)
            <tr>
                @for($col = 0; $col < $config['cols']; $col++)
                    @php
                        $idx = ($row * $config['cols']) + $col;
                        $item = $page[$idx] ?? null;
                        $cardClass = 'card';
                        $emptyClass = 'card-empty';
                        if ($layout === '2x4') {
                            $cardClass .= ' card-2x4';
                            $emptyClass .= ' card-empty-2x4';
                        } elseif ($layout === '2x3') {
                            $cardClass .= ' card-2x3';
                            $emptyClass .= ' card-empty-2x3';
                        }
                    @endphp
                    <td>
                        @if($item)
                            @php $s = $item['student']; @endphp
                            <div class="{{ $cardClass }}">
                                {{-- Header --}}
                                <div class="card-header">
                                    <div class="card-header-logo">
                                        @if($logoBase64)
                                            <img src="{{ $logoBase64 }}" alt="Logo">
                                        @endif
                                    </div>
                                    <div class="card-header-text">
                                        <div class="school-name">{{ strtoupper($schoolName) }}</div>
                                        <div class="card-title">KARTU PELAJAR {{ $tahunAjaran }}</div>
                                    </div>
                                </div>

                                {{-- Body menggunakan HTML table (DomPDF tidak support flexbox) --}}
                                <div class="card-body">
                                    <table class="card-body-table">
                                        <tr>
                                            {{-- Kolom Foto --}}
                                            <td style="width:18mm;">
                                                <div class="card-photo">
                                                    @if($item['foto_base64'])
                                                        <img src="{{ $item['foto_base64'] }}" alt="Foto">
                                                    @else
                                                        <span class="initials">{{ strtoupper(substr($s->nama, 0, 1)) }}</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Kolom Info --}}
                                            <td class="card-info">
                                                <table class="info-table">
                                                    <tr>
                                                        <td class="lbl">Nama</td>
                                                        <td class="sep">:</td>
                                                        <td class="val">{{ $s->nama }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="lbl">NIS</td>
                                                        <td class="sep">:</td>
                                                        <td class="val">{{ $s->nis }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="lbl">Kelas</td>
                                                        <td class="sep">:</td>
                                                        <td class="val">{{ $s->kelas->nama_kelas ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="lbl">T.A.</td>
                                                        <td class="sep">:</td>
                                                        <td class="val">{{ $tahunAjaran }}</td>
                                                    </tr>
                                                </table>
                                            </td>

                                            {{-- Kolom QR Code --}}
                                            <td class="card-qr-wrap" style="width:24mm;">
                                                <div class="card-qr">
                                                    @if($item['qr_base64'])
                                                        <img src="{{ $item['qr_base64'] }}" alt="QR">
                                                    @else
                                                        <span style="font-size:5pt;color:#999;">No QR</span>
                                                    @endif
                                                </div>
                                                <div class="card-qr-label">Scan Absensi</div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                {{-- Footer --}}
                                @if($schoolAddress)
                                <div class="card-footer">
                                    {{ $schoolAddress }}
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="{{ $emptyClass }}"></div>
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
