<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian - {{ $schoolName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1a1a1a; }
        .header { text-align: center; padding: 10px 0 8px; border-bottom: 2px solid #1e3a5f; margin-bottom: 10px; }
        .header h1 { font-size: 13px; font-weight: bold; color: #1e3a5f; }
        .header h2 { font-size: 10px; color: #555; margin-top: 3px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        thead th { background: #1e3a5f; color: #fff; padding: 5px 4px; text-align: center; border: 1px solid #1e3a5f; }
        tbody tr:nth-child(even) { background: #f0f4f8; }
        tbody td { padding: 5px 4px; border: 1px solid #ccc; text-align: center; }
        tbody td.name { text-align: left; }
        .status-hadir    { background: #d1fae5; color: #065f46; font-weight: bold; border-radius: 3px; padding: 1px 4px; }
        .status-terlambat{ background: #fef3c7; color: #92400e; font-weight: bold; border-radius: 3px; padding: 1px 4px; }
        .status-alpha    { background: #fee2e2; color: #991b1b; font-weight: bold; border-radius: 3px; padding: 1px 4px; }
        .status-izin     { background: #dbeafe; color: #1e40af; font-weight: bold; border-radius: 3px; padding: 1px 4px; }
        .status-sakit    { background: #f3e8ff; color: #6b21a8; font-weight: bold; border-radius: 3px; padding: 1px 4px; }
        .footer { margin-top: 14px; display: flex; justify-content: flex-end; font-size: 9px; }
        .ttd { text-align: center; }
        .ttd .box { margin-top: 38px; border-top: 1px solid #333; padding-top: 3px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($schoolName) }}</h1>
        <h2>LAPORAN ABSENSI HARIAN — {{ strtoupper(\Carbon\Carbon::parse($date)->translatedFormat('l, d F Y')) }} — {{ strtoupper($className) }}</h2>
    </div>

    <div class="meta">
        <span>Dicetak: {{ now()->translatedFormat('d F Y, H:i') }}</span>
        <span>Total: {{ $records->count() }} record</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:28px">No</th>
                <th style="width:65px">NIS</th>
                <th style="text-align:left; width:160px">Nama Siswa</th>
                <th style="width:80px">Kelas</th>
                <th style="width:55px">Jam Masuk</th>
                <th style="width:55px">Jam Pulang</th>
                <th style="width:70px">Status</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $i => $rec)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $rec->student->nis }}</td>
                    <td class="name">{{ $rec->student->nama }}</td>
                    <td>{{ $rec->student->kelas->nama_kelas }}</td>
                    <td>{{ $rec->check_in_time ? \Carbon\Carbon::parse($rec->check_in_time)->format('H:i') : '-' }}</td>
                    <td>{{ $rec->check_out_time ? \Carbon\Carbon::parse($rec->check_out_time)->format('H:i') : '-' }}</td>
                    <td><span class="status-{{ $rec->status }}">{{ ucfirst($rec->status) }}</span></td>
                    <td class="name">{{ $rec->notes ?? '' }}</td>
                </tr>
            @endforeach
            @if($records->isEmpty())
                <tr><td colspan="8" style="text-align:center; padding:12px; color:#999;">Tidak ada data absensi.</td></tr>
            @endif
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd">
            <div>Blora, {{ now()->translatedFormat('d F Y') }}</div>
            <div>Penanggung Jawab</div>
            <div class="box">( _____________________ )</div>
        </div>
    </div>
</body>
</html>
