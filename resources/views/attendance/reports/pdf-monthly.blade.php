<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Bulanan - {{ $schoolName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; background: #fff; }
        .header { text-align: center; padding: 12px 0 8px; border-bottom: 2px solid #1e3a5f; margin-bottom: 12px; }
        .header h1 { font-size: 14px; font-weight: bold; color: #1e3a5f; }
        .header h2 { font-size: 11px; color: #555; margin-top: 3px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        thead th { background: #1e3a5f; color: #fff; padding: 6px 5px; text-align: center; border: 1px solid #1e3a5f; }
        tbody tr:nth-child(even) { background: #f0f4f8; }
        tbody td { padding: 5px 5px; border: 1px solid #ccc; text-align: center; }
        tbody td.name { text-align: left; }
        .badge-hadir    { color: #166534; font-weight: bold; }
        .badge-terlambat{ color: #92400e; font-weight: bold; }
        .badge-alpha    { color: #991b1b; font-weight: bold; }
        .badge-izin     { color: #1e40af; font-weight: bold; }
        .footer { margin-top: 16px; display: flex; justify-content: space-between; font-size: 9px; color: #666; }
        .ttd { text-align: center; }
        .ttd .box { margin-top: 40px; border-top: 1px solid #333; padding-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($schoolName) }}</h1>
        <h2>REKAP ABSENSI BULANAN — {{ strtoupper(\Carbon\Carbon::parse($month)->translatedFormat('F Y')) }} — {{ strtoupper($className) }}</h2>
    </div>

    <div class="meta">
        <span>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</span>
        <span>Total Siswa: {{ $summary->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th style="width:70px">NIS</th>
                <th style="text-align:left; width:160px">Nama Siswa</th>
                <th style="width:80px">Kelas</th>
                <th style="width:40px">Hadir</th>
                <th style="width:50px">Terlambat</th>
                <th style="width:40px">Sakit</th>
                <th style="width:40px">Izin</th>
                <th style="width:40px">Alpha</th>
                <th style="width:40px">Total</th>
                <th style="width:45px">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary as $i => $row)
                @php
                    $pct = $row['total'] > 0 ? round(($row['hadir'] + $row['terlambat']) / $row['total'] * 100) : 0;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['student']->nis }}</td>
                    <td class="name">{{ $row['student']->nama }}</td>
                    <td>{{ $row['student']->kelas->nama_kelas }}</td>
                    <td class="badge-hadir">{{ $row['hadir'] }}</td>
                    <td class="badge-terlambat">{{ $row['terlambat'] }}</td>
                    <td>{{ $row['sakit'] }}</td>
                    <td class="badge-izin">{{ $row['izin'] }}</td>
                    <td class="badge-alpha">{{ $row['alpha'] }}</td>
                    <td>{{ $row['total'] }}</td>
                    <td>{{ $pct }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div></div>
        <div class="ttd">
            <div>Blora, {{ now()->translatedFormat('d F Y') }}</div>
            <div>Kepala Sekolah</div>
            <div class="box">( _____________________ )</div>
        </div>
    </div>
</body>
</html>
