<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>
        {{ $report['is_detailed'] ? 'Distribusi Bimbingan PU dan PP' : 'Distribusi Jumlah Bimbingan Utama' }}
        {{ $report['selected_year'] }}
    </title>
</head>
<body>
    @php($columnCount = $report['is_detailed'] ? 5 + (count($report['programs']) * 4) : 3 + (count($report['programs']) * 2))
    <table border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th colspan="{{ $columnCount }}">
                {{ $report['is_detailed'] ? 'Data Distribusi Bimbingan Utama (PU) dan Pendamping (PP)' : 'Data Jumlah Bimbingan Utama' }}
            </th>
        </tr>
        <tr>
            <td colspan="{{ $columnCount }}">Tahun Ajaran {{ $report['selected_year'] }}</td>
        </tr>
        @foreach ($report['programs'] as $program)
            <tr>
                <td colspan="2">Total Penugasan - {{ $program['label'] }}</td>
                @if ($report['is_detailed'])
                    <td colspan="{{ $columnCount - 2 }}">
                        PU: {{ $report['total_penugasan_by_program'][$program['key']]['PU'] }} |
                        PP: {{ $report['total_penugasan_by_program'][$program['key']]['PP'] }} |
                        Total: {{ $report['total_penugasan_by_program'][$program['key']]['total'] }}
                    </td>
                @else
                    <td colspan="{{ $columnCount - 2 }}">{{ $report['total_mahasiswa_by_program'][$program['key']] }}</td>
                @endif
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Total Penugasan - Semua Prodi</td>
            <td colspan="{{ $columnCount - 2 }}">
                {{ $report['is_detailed'] ? $report['total_penugasan'] : $report['total_mahasiswa'] }}
            </td>
        </tr>

        @if ($report['is_detailed'])
            <tr>
                <th rowspan="3">No</th>
                <th rowspan="3">Nama Dosen</th>
                @foreach ($report['programs'] as $program)
                    <th colspan="4">{{ $program['label'] }}</th>
                @endforeach
                <th colspan="3">Total</th>
            </tr>
            <tr>
                @foreach ($report['programs'] as $program)
                    <th colspan="2">Awal</th>
                    <th colspan="2">Akhir</th>
                @endforeach
                <th rowspan="2">PU</th>
                <th rowspan="2">PP</th>
                <th rowspan="2">Semua</th>
            </tr>
            <tr>
                @foreach ($report['programs'] as $program)
                    <th>PU</th>
                    <th>PP</th>
                    <th>PU</th>
                    <th>PP</th>
                @endforeach
            </tr>
        @else
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
        @endif

        @forelse ($report['rows'] as $row)
            <tr>
                <td>{{ $row['no'] }}</td>
                <td>{{ $row['nama_dosen'] }} ({{ $row['kode_dosen'] }})</td>
                @if ($report['is_detailed'])
                    @foreach ($report['programs'] as $program)
                        <td>{{ $row['role_counts'][$program['key']]['Ganjil']['PU'] }}</td>
                        <td>{{ $row['role_counts'][$program['key']]['Ganjil']['PP'] }}</td>
                        <td>{{ $row['role_counts'][$program['key']]['Genap']['PU'] }}</td>
                        <td>{{ $row['role_counts'][$program['key']]['Genap']['PP'] }}</td>
                    @endforeach
                    <td>{{ $row['role_totals']['PU'] }}</td>
                    <td>{{ $row['role_totals']['PP'] }}</td>
                    <td>{{ $row['grand_total'] }}</td>
                @else
                    @foreach ($report['programs'] as $program)
                        <td>{{ $row['counts'][$program['key']]['Ganjil'] }}</td>
                        <td>{{ $row['counts'][$program['key']]['Genap'] }}</td>
                    @endforeach
                    <td>{{ $row['total'] }}</td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="{{ $columnCount }}">
                    Belum ada data penugasan {{ $report['is_detailed'] ? 'PU/PP' : 'Pembimbing Utama' }} pada tahun ajaran ini.
                </td>
            </tr>
        @endforelse
    </table>
    <p>Awal: September-Februari. Akhir: Maret-Agustus.</p>
</body>
</html>
