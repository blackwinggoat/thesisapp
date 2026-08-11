<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rekap Jadwal {{ $namaTipeUjian }}</title>
    <style>
        table {
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 11pt;
        }
        th, td {
            border: 1px solid #000000;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
            white-space: normal;
        }
        th {
            background: #ffff00;
            font-weight: bold;
        }
        .highlight {
            background: #f4b6ad;
        }
    </style>
</head>
<body>
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
                <td class="highlight">{{ $row->penguji_2 }}</td>
                <td>{{ $row->penguji_3 }}</td>
                <td>{{ $row->ketua_sidang }}</td>
                <td>{{ $row->kode_jenis_tugas_akhir }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="11">Belum ada peserta pada jadwal yang dipilih.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
