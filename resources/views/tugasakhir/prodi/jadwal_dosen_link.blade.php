<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal {{ $namaTipeUjian }} - {{ $namaDosen }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 24px;
            color: #1f2933;
        }
        h2 {
            margin-bottom: 6px;
        }
        .toolbar {
            margin: 18px 0;
        }
        .btn {
            display: inline-block;
            padding: 9px 12px;
            margin-right: 8px;
            color: #ffffff;
            background: #2f80ed;
            text-decoration: none;
            border-radius: 4px;
            border: 0;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-print {
            background: #27ae60;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #111111;
            padding: 7px;
            text-align: center;
            vertical-align: middle;
        }
        th {
            background: #ffff00;
        }
        @media print {
            .toolbar {
                display: none;
            }
            body {
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <h2>Jadwal {{ $namaTipeUjian }}</h2>
    <div>{{ $namaDosen }}</div>
    <div class="toolbar">
        <a class="btn" href="{{ url('jadwal-dosen/'.$token) }}?download=excel">Download Excel</a>
        <button class="btn btn-print" type="button" onclick="window.print()">Buka PDF / Print</button>
    </div>

    <table>
        <thead>
        <tr>
            <th>Ruangan Ujian</th>
            <th>JAM</th>
            <th>Nim</th>
            <th>Nama Mahasiswa</th>
            <th>Pembimbing Utama</th>
            <th>Pembimbing Pendamping</th>
            <th>Penguji I</th>
            <th>Penguji II</th>
            <th>Penguji III</th>
            <th>Ketua Sidang</th>
            <th>Jenis Ujian</th>
        </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr>
                <td>{{ $row->nama_ruangan ?: '-' }}</td>
                <td>{{ $row->jam_ujian_rekap }}</td>
                <td>{{ $row->C_NPM }}</td>
                <td>{{ $row->NAMA_MAHASISWA }}</td>
                <td>{{ $row->pembimbing_utama }}</td>
                <td>{{ $row->pembimbing_pendamping }}</td>
                <td>{{ $row->penguji_1 }}</td>
                <td>{{ $row->penguji_2 }}</td>
                <td>{{ $row->penguji_3 }}</td>
                <td>{{ $row->ketua_sidang }}</td>
                <td>{{ $row->kode_jenis_tugas_akhir }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11">Tidak ada jadwal untuk dosen ini.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
