<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Semester {{ ucfirst($semester) }} — {{ $tahunAjaran }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9px; color: #1f2937; background: white; }

        .header { text-align: center; border-bottom: 3px double #4f46e5; padding-bottom: 8px; margin-bottom: 12px; }
        .header h1 { font-size: 14px; font-weight: bold; color: #1e1b4b; letter-spacing: 1px; }
        .header h2 { font-size: 11px; font-weight: bold; color: #4f46e5; margin-top: 3px; }
        .header p  { font-size: 8px; color: #6b7280; margin-top: 2px; }

        .meta-grid { display: flex; gap: 8px; margin-bottom: 10px; flex-wrap: wrap; }
        .meta-box  { flex: 1; min-width: 100px; border: 1px solid #e5e7eb; border-radius: 6px; padding: 6px 10px; background: #f9fafb; }
        .meta-box .label { font-size: 7px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-box .value { font-size: 10px; font-weight: bold; color: #111827; margin-top: 1px; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #4f46e5; color: white; }
        thead th { padding: 5px 6px; text-align: center; font-size: 8px; font-weight: bold; }
        thead th:nth-child(3) { text-align: left; }
        tbody tr:nth-child(even) { background: #f8faff; }
        tbody tr.danger { background: #fff5f5; }
        tbody td { padding: 4px 6px; border-bottom: 1px solid #f0f0f0; font-size: 8px; text-align: center; }
        tbody td.left { text-align: left; }
        tfoot tr { background: #e0e7ff; font-weight: bold; }
        tfoot td { padding: 5px 6px; font-size: 8px; text-align: center; }
        tfoot td.left { text-align: left; }

        .badge { display: inline-block; padding: 1px 5px; border-radius: 10px; font-size: 7px; font-weight: bold; }
        .badge-green  { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-red    { background: #fee2e2; color: #991b1b; }

        .col-no   { width: 24px; }
        .col-nis  { width: 60px; }
        .col-nama { width: auto; }
        .col-kelas{ width: 55px; }
        .col-stat { width: 38px; }
        .col-pct  { width: 45px; }
        .col-ket  { width: 38px; }

        .footer { margin-top: 14px; display: flex; justify-content: space-between; align-items: flex-end; }
        .footer .info { font-size: 7px; color: #9ca3af; }
        .sign-box { text-align: center; font-size: 8px; }
        .sign-box .name { margin-top: 30px; border-top: 1px solid #374151; padding-top: 3px; font-weight: bold; font-size: 8px; }
    </style>
</head>
<body>
    {{-- HEADER --}}
    <div class="header">
        <h1>{{ strtoupper($schoolName) }}</h1>
        <h2>REKAP KEHADIRAN SEMESTER {{ strtoupper($semester) }}</h2>
        <p>Tahun Ajaran {{ $tahunAjaran }} &bull; {{ $kelas }}</p>
    </div>

    {{-- META --}}
    <div class="meta-grid">
        <div class="meta-box">
            <div class="label">Periode</div>
            <div class="value">{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</div>
        </div>
        <div class="meta-box">
            <div class="label">Total Hari Sekolah</div>
            <div class="value">{{ $totalHari }} hari</div>
        </div>
        <div class="meta-box">
            <div class="label">Jumlah Siswa</div>
            <div class="value">{{ count($rekap) }} siswa</div>
        </div>
        <div class="meta-box">
            <div class="label">Dicetak</div>
            <div class="value">{{ now()->format('d M Y H:i') }}</div>
        </div>
    </div>

    {{-- TABEL --}}
    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-nis">NIS</th>
                <th class="col-nama" style="text-align:left">Nama Siswa</th>
                <th class="col-kelas">Kelas</th>
                <th class="col-stat">Hadir</th>
                <th class="col-stat">Terlambat</th>
                <th class="col-stat">Izin</th>
                <th class="col-stat">Sakit</th>
                <th class="col-stat">Alpha</th>
                <th class="col-stat">T.Hadir</th>
                <th class="col-pct">% Hadir</th>
                <th class="col-ket">Ket</th>
            </tr>
        </thead>
        <tbody>
            @php $totalH=0; $totalT=0; $totalI=0; $totalS=0; $totalA=0; @endphp
            @foreach($rekap as $i => $row)
            @php
                $persen = $totalHari > 0 ? round(($row['hadir'] / $totalHari) * 100, 1) : 0;
                $ket    = $persen >= 75 ? 'BAIK' : ($persen >= 50 ? 'CUKUP' : 'KURANG');
                $bClass = $persen >= 75 ? 'badge-green' : ($persen >= 50 ? 'badge-yellow' : 'badge-red');
                $totalH += $row['hadir']; $totalT += $row['terlambat'];
                $totalI += $row['izin'];  $totalS += $row['sakit']; $totalA += $row['alpha'];
            @endphp
            <tr class="{{ $persen < 75 ? 'danger' : '' }}">
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['nis'] }}</td>
                <td class="left">{{ $row['nama'] }}</td>
                <td>{{ $row['kelas'] }}</td>
                <td><strong>{{ $row['hadir'] }}</strong></td>
                <td>{{ $row['terlambat'] }}</td>
                <td>{{ $row['izin'] }}</td>
                <td>{{ $row['sakit'] }}</td>
                <td><strong>{{ $row['alpha'] }}</strong></td>
                <td>{{ $row['hadir'] + $row['terlambat'] }}</td>
                <td><span class="badge {{ $bClass }}">{{ $persen }}%</span></td>
                <td><span class="badge {{ $bClass }}">{{ $ket }}</span></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="left">JUMLAH TOTAL</td>
                <td>{{ $totalH }}</td>
                <td>{{ $totalT }}</td>
                <td>{{ $totalI }}</td>
                <td>{{ $totalS }}</td>
                <td>{{ $totalA }}</td>
                <td>{{ $totalH + $totalT }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="info">
            *) Keterangan: BAIK ≥75% | CUKUP 50–74% | KURANG &lt;50% &bull; Baris merah = kehadiran &lt;75%
        </div>
        <div class="sign-box">
            ............., {{ now()->format('d F Y') }}<br>
            Wali Kelas / Penanggung Jawab<br>
            <div class="name">( _________________________________ )</div>
        </div>
    </div>
</body>
</html>
