<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>
        {{ $report['is_detailed'] ? 'Distribusi Penguji Rinci Peran' : 'Distribusi Penugasan Penguji' }}
        {{ $report['selected_year'] }}
    </title>
</head>
<body>
    @php($roleCount = count($report['roles']))
    @php($columnCount = $report['is_detailed'] ? 3 + (count($report['programs']) * $roleCount * 2) + $roleCount : 3 + (count($report['programs']) * 2))
    <table border="1" cellpadding="4" cellspacing="0">
        <tr>
            <th colspan="{{ $columnCount }}">
                {{ $report['is_detailed'] ? 'Data Distribusi Tim Ujian per Peran' : 'Data Distribusi Penugasan Tim Ujian' }}
            </th>
        </tr>
        <tr>
            <td colspan="{{ $columnCount }}">Tahun Ajaran {{ $report['selected_year'] }}</td>
        </tr>
        @foreach ($report['programs'] as $program)
            <tr>
                <td colspan="2">Total Penugasan - {{ $program['label'] }}</td>
                <td colspan="{{ $columnCount - 2 }}">
                    @if ($report['is_detailed'])
                        @foreach ($report['roles'] as $roleKey => $role)
                            {{ $roleKey }}: {{ $report['total_penugasan_by_program'][$program['key']][$roleKey] }}{{ !$loop->last ? ' |' : '' }}
                        @endforeach
                        | Total: {{ $report['total_penugasan_by_program'][$program['key']]['total'] }}
                    @else
                        {{ $report['total_penugasan_by_program'][$program['key']]['total'] }}
                    @endif
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Total Penugasan - Semua Prodi</td>
            <td colspan="{{ $columnCount - 2 }}">{{ $report['total_penugasan'] }}</td>
        </tr>

        @if ($report['is_detailed'])
            <tr>
                <th rowspan="3">No</th>
                <th rowspan="3">Nama Dosen</th>
                @foreach ($report['programs'] as $program)
                    <th colspan="{{ $roleCount * 2 }}">{{ $program['label'] }}</th>
                @endforeach
                <th colspan="{{ $roleCount + 1 }}">Total</th>
            </tr>
            <tr>
                @foreach ($report['programs'] as $program)
                    <th colspan="{{ $roleCount }}">Awal</th>
                    <th colspan="{{ $roleCount }}">Akhir</th>
                @endforeach
                @foreach ($report['roles'] as $roleKey => $role)
                    <th rowspan="2">{{ $roleKey }}</th>
                @endforeach
                <th rowspan="2">Semua</th>
            </tr>
            <tr>
                @foreach ($report['programs'] as $program)
                    @foreach (['Ganjil', 'Genap'] as $semester)
                        @foreach ($report['roles'] as $roleKey => $role)
                            <th>{{ $roleKey }}</th>
                        @endforeach
                    @endforeach
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
                        @foreach (['Ganjil', 'Genap'] as $semester)
                            @foreach ($report['roles'] as $roleKey => $role)
                                <td>{{ $row['role_counts'][$program['key']][$semester][$roleKey] }}</td>
                            @endforeach
                        @endforeach
                    @endforeach
                    @foreach ($report['roles'] as $roleKey => $role)
                        <td>{{ $row['role_totals'][$roleKey] }}</td>
                    @endforeach
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
                <td colspan="{{ $columnCount }}">Belum ada penugasan tim ujian pada tahun ajaran ini.</td>
            </tr>
        @endforelse
    </table>
    <p>Awal: September-Februari. Akhir: Maret-Agustus.</p>
</body>
</html>
