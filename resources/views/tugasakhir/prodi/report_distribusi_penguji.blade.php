@extends('tugasakhir.index')
@section('isi')
<style>
    .distribution-examiner-table > thead > tr > th {
        background-color: #245c63 !important;
        border-color: #19474d !important;
        color: #fff !important;
    }

    .distribution-examiner-table > thead > tr:nth-child(2) > th {
        background-color: #347780 !important;
    }

    .distribution-examiner-table > thead > tr:nth-child(3) > th {
        background-color: #4b8b93 !important;
    }

    .distribution-examiner-table > tbody > tr > td {
        background-color: #fff;
    }

    .distribution-examiner-table > tbody > tr:nth-child(even) > td {
        background-color: #f4f7f7;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">{{ $reportPageTitle }} <small>{{ $reportContext['label'] }}</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ $reportDashboardUrl }}">Report</a></li>
            <li class="active">{{ $reportPageTitle }}</li>
        </ol>

        @if (!empty($reportWarnings))
            <div class="alert alert-warning">
                Sebagian laporan belum dapat dimuat. Silakan coba lagi atau periksa konfigurasi database.
            </div>
        @endif

        <div class="the-box">
            <div class="row">
                <div class="col-sm-8">
                    <h3 class="small-title" style="margin-top: 0;">
                        {{ $pengujiReport['is_detailed'] ? 'Distribusi Tim Ujian per Peran' : 'Distribusi Penugasan Tim Ujian' }}
                    </h3>
                    <p class="text-muted" style="margin-bottom: 0;">
                        @if ($pengujiReport['is_detailed'])
                            Penugasan dipisahkan sebagai Ketua Sidang, Penguji I, Penguji II, dan Penguji III
                            berdasarkan tanggal jadwal ujian.
                        @else
                            Jumlah penugasan dosen dalam tim ujian berdasarkan tanggal jadwal ujian.
                            Proposal dan Ujian Meja dihitung sebagai penugasan yang berbeda.
                        @endif
                    </p>
                    <div class="btn-group" style="margin-top: 12px;" role="group" aria-label="Mode laporan distribusi penguji">
                        <a href="{{ $reportActionUrl . '?' . http_build_query(['tahun_ajaran' => $pengujiReport['selected_year'], 'mode' => 'ringkas']) }}"
                           class="btn {{ $pengujiReport['mode'] === 'ringkas' ? 'btn-primary' : 'btn-default' }}">
                            <i class="fa fa-users"></i> Ringkasan Tim Ujian
                        </a>
                        <a href="{{ $reportActionUrl . '?' . http_build_query(['tahun_ajaran' => $pengujiReport['selected_year'], 'mode' => 'lengkap']) }}"
                           class="btn {{ $pengujiReport['mode'] === 'lengkap' ? 'btn-primary' : 'btn-default' }}">
                            <i class="fa fa-list-alt"></i> Rinci Peran
                        </a>
                    </div>
                </div>
                <div class="col-sm-4">
                    <form method="get" action="{{ $reportActionUrl }}" class="form-inline text-right">
                        <input type="hidden" name="mode" value="{{ $pengujiReport['mode'] }}">
                        <label for="tahun-ajaran" class="sr-only">Tahun Ajaran</label>
                        <select id="tahun-ajaran" name="tahun_ajaran" class="form-control" onchange="this.form.submit()">
                            @forelse ($pengujiReport['period_options'] as $period)
                                <option value="{{ $period }}" {{ $period === $pengujiReport['selected_year'] ? 'selected' : '' }}>
                                    Tahun Ajaran {{ $period }}
                                </option>
                            @empty
                                <option value="{{ $pengujiReport['selected_year'] }}" selected>
                                    Tahun Ajaran {{ $pengujiReport['selected_year'] }}
                                </option>
                            @endforelse
                        </select>
                        <a href="{{ $reportExcelUrl . '?' . http_build_query(['tahun_ajaran' => $pengujiReport['selected_year'], 'mode' => $pengujiReport['mode']]) }}"
                           class="btn btn-success" title="Download Excel">
                            <i class="fa fa-download"></i> Excel
                        </a>
                    </form>
                </div>
            </div>

            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-3">
                    <div class="well well-sm text-center" style="margin-bottom: 10px;">
                        <strong>{{ number_format($pengujiReport['total_dosen']) }}</strong><br>
                        <small>Dosen dalam Tim Ujian</small>
                    </div>
                </div>
                @foreach ($pengujiReport['programs'] as $program)
                    <div class="col-sm-3">
                        <div class="well well-sm text-center" style="margin-bottom: 10px;">
                            <strong>{{ number_format($pengujiReport['total_penugasan_by_program'][$program['key']]['total'] ?? 0) }}</strong><br>
                            <small>
                                Total Penugasan - {{ $program['label'] }}
                                @if ($pengujiReport['is_detailed'])
                                    <span style="display: block;">
                                        @foreach ($pengujiReport['roles'] as $roleKey => $role)
                                            {{ $roleKey }} {{ number_format($pengujiReport['total_penugasan_by_program'][$program['key']][$roleKey] ?? 0) }}{{ !$loop->last ? ' ·' : '' }}
                                        @endforeach
                                    </span>
                                @endif
                            </small>
                        </div>
                    </div>
                @endforeach
                <div class="col-sm-3">
                    <div class="well well-sm text-center" style="margin-bottom: 10px;">
                        <strong>{{ number_format($pengujiReport['total_penugasan']) }}</strong><br>
                        <small>Total Penugasan - Semua Prodi</small>
                    </div>
                </div>
            </div>

            <p class="text-muted" style="margin: 5px 0 0;">
                {{ $pengujiReport['selected_year'] }}: {{ $pengujiReport['awal_label'] }} / {{ $pengujiReport['akhir_label'] }}.
            </p>

            @php($roleCount = count($pengujiReport['roles']))
            @php($detailColumnCount = 3 + (count($pengujiReport['programs']) * $roleCount * 2) + $roleCount)
            @php($summaryColumnCount = 3 + (count($pengujiReport['programs']) * 2))
            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-bordered table-hover table-condensed distribution-examiner-table" style="min-width: {{ $pengujiReport['is_detailed'] ? '1750px' : '720px' }};">
                    <thead>
                        @if ($pengujiReport['is_detailed'])
                            <tr>
                                <th rowspan="3" class="text-center" style="vertical-align: middle;">No</th>
                                <th rowspan="3" style="vertical-align: middle;">Nama Dosen</th>
                                @foreach ($pengujiReport['programs'] as $program)
                                    <th colspan="{{ $roleCount * 2 }}" class="text-center">{{ $program['label'] }}</th>
                                @endforeach
                                <th colspan="{{ $roleCount + 1 }}" class="text-center">Total</th>
                            </tr>
                            <tr>
                                @foreach ($pengujiReport['programs'] as $program)
                                    <th colspan="{{ $roleCount }}" class="text-center">Awal</th>
                                    <th colspan="{{ $roleCount }}" class="text-center">Akhir</th>
                                @endforeach
                                @foreach ($pengujiReport['roles'] as $roleKey => $role)
                                    <th rowspan="2" class="text-center" style="vertical-align: middle;">{{ $roleKey }}</th>
                                @endforeach
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">Semua</th>
                            </tr>
                            <tr>
                                @foreach ($pengujiReport['programs'] as $program)
                                    @foreach (['Ganjil', 'Genap'] as $semester)
                                        @foreach ($pengujiReport['roles'] as $roleKey => $role)
                                            <th class="text-center">{{ $roleKey }}</th>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">No</th>
                                <th rowspan="2" style="vertical-align: middle;">Nama Dosen</th>
                                @foreach ($pengujiReport['programs'] as $program)
                                    <th colspan="2" class="text-center">{{ $program['label'] }}</th>
                                @endforeach
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">Total</th>
                            </tr>
                            <tr>
                                @foreach ($pengujiReport['programs'] as $program)
                                    <th class="text-center">Awal</th>
                                    <th class="text-center">Akhir</th>
                                @endforeach
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @forelse ($pengujiReport['rows'] as $row)
                            <tr>
                                <td class="text-center">{{ $row['no'] }}</td>
                                <td>
                                    <strong>{{ $row['nama_dosen'] }}</strong>
                                    <small class="text-muted" style="display: block;">{{ $row['kode_dosen'] }}</small>
                                </td>
                                @if ($pengujiReport['is_detailed'])
                                    @foreach ($pengujiReport['programs'] as $program)
                                        @foreach (['Ganjil', 'Genap'] as $semester)
                                            @foreach ($pengujiReport['roles'] as $roleKey => $role)
                                                <td class="text-center">{{ $row['role_counts'][$program['key']][$semester][$roleKey] }}</td>
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                    @foreach ($pengujiReport['roles'] as $roleKey => $role)
                                        <td class="text-center"><strong>{{ $row['role_totals'][$roleKey] }}</strong></td>
                                    @endforeach
                                    <td class="text-center"><strong>{{ $row['grand_total'] }}</strong></td>
                                @else
                                    @foreach ($pengujiReport['programs'] as $program)
                                        <td class="text-center">{{ $row['counts'][$program['key']]['Ganjil'] }}</td>
                                        <td class="text-center">{{ $row['counts'][$program['key']]['Genap'] }}</td>
                                    @endforeach
                                    <td class="text-center"><strong>{{ $row['total'] }}</strong></td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $pengujiReport['is_detailed'] ? $detailColumnCount : $summaryColumnCount }}" class="text-center">
                                    Belum ada penugasan tim ujian pada tahun ajaran ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-muted" style="margin: 15px 0 0;">
                Distribusi dihitung dari jadwal ujian mahasiswa yang valid, kemudian dipetakan ke Ketua Sidang dan Penguji I-III.
                Awal: September-Februari. Akhir: Maret-Agustus.
            </p>
        </div>
    </div>
</div>
@endsection
