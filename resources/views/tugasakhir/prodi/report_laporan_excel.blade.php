<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Distribusi Jumlah Bimbingan Utama {{ $report['selected_year'] }}</title>
</head>
<body>
    @php($columnCount = 3 + (count($report['programs']) * 2))
    <table border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th colspan="{{ $columnCount }}">Data Jumlah Bimbingan Utama</th>
        </tr>
        <tr>
            <td colspan="{{ $columnCount }}">Tahun Ajaran {{ $report['selected_year'] }}</td>
        </tr>
        @foreach ($report['programs'] as $program)
            <tr>
                <td colspan="2">Total Penugasan Mahasiswa - {{ $program['label'] }}</td>
                <td colspan="{{ $columnCount - 2 }}">{{ $report['total_mahasiswa_by_program'][$program['key']] }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Total Penugasan Mahasiswa - Semua Prodi</td>
            <td colspan="{{ $columnCount - 2 }}">{{ $report['total_mahasiswa'] }}</td>
        </tr>
        <tr>
            <th rowspan="2">No</th>
            <th rowspan="2">Nama Dosen</th>
            @foreach ($report['programs'] as $program)
                <th colspan="2">{{ $program['label'] }}</th>
            @endforeach
            <th rowspan="2">Total</th>
        </tr>
        <tr>
            @foreach ($report['programs'] as $program)
                <th>Awal</th>
                <th>Akhir</th>
            @endforeach
        </tr>
        @forelse ($report['rows'] as $row)
            <tr>
                <td>{{ $row['no'] }}</td>
                <td>{{ $row['nama_dosen'] }} ({{ $row['kode_dosen'] }})</td>
                @foreach ($report['programs'] as $program)
                    <td>{{ $row['counts'][$program['key']]['Ganjil'] }}</td>
                    <td>{{ $row['counts'][$program['key']]['Genap'] }}</td>
                @endforeach
                <td>{{ $row['total'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ $columnCount }}">Belum ada data penugasan Pembimbing Utama pada tahun ajaran ini.</td>
            </tr>
        @endforelse
    </table>
    <p>Awal: September-Februari. Akhir: Maret-Agustus.</p>
</body>
</html>
