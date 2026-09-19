@extends('tugasakhir.index')
@section('isi')
<style>
    .distribution-guidance-table > thead > tr > th {
        background-color: #245c63 !important;
        border-color: #19474d !important;
        color: #fff !important;
    }

    .distribution-guidance-table > thead > tr:nth-child(2) > th {
        background-color: #347780 !important;
    }

    .distribution-guidance-table > thead > tr:nth-child(3) > th {
        background-color: #4b8b93 !important;
    }

    .distribution-guidance-table > tbody > tr > td {
        background-color: #fff;
    }

    .distribution-guidance-table > tbody > tr:nth-child(even) > td {
        background-color: #f4f7f7;
    }
</style>
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">{{ $reportPageTitle }} <small>{{ $reportContext['label'] }}</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            @if ($reportDashboardUrl)
                <li><a href="{{ $reportDashboardUrl }}">Report</a></li>
            @else
                <li>Report</li>
            @endif
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
                        {{ $bimbinganReport['is_detailed'] ? 'Distribusi Bimbingan Utama dan Pendamping' : 'Distribusi Jumlah Bimbingan Utama' }}
                    </h3>
                    <p class="text-muted" style="margin-bottom: 0;">
                        @if ($bimbinganReport['is_detailed'])
                            Penugasan setiap dosen dipisahkan sebagai Pembimbing Utama (PU) dan Pembimbing Pendamping (PP)
                            berdasarkan tanggal SK pembimbing.
                        @else
                            Jumlah mahasiswa yang ditugaskan kepada dosen sebagai Pembimbing Utama berdasarkan tanggal SK pembimbing.
                            Mahasiswa dihitung satu kali pada setiap semester.
                        @endif
                    </p>
                    <div class="btn-group" style="margin-top: 12px;" role="group" aria-label="Mode laporan distribusi bimbingan">
                        <a href="{{ $reportActionUrl . '?' . http_build_query(['tahun_ajaran' => $bimbinganReport['selected_year'], 'mode' => 'utama']) }}"
                           class="btn {{ $bimbinganReport['mode'] === 'utama' ? 'btn-primary' : 'btn-default' }}">
                            <i class="fa fa-user"></i> Ringkasan PU
                        </a>
                        <a href="{{ $reportActionUrl . '?' . http_build_query(['tahun_ajaran' => $bimbinganReport['selected_year'], 'mode' => 'lengkap']) }}"
                           class="btn {{ $bimbinganReport['mode'] === 'lengkap' ? 'btn-primary' : 'btn-default' }}">
                            <i class="fa fa-users"></i> Rinci PU/PP
                        </a>
                    </div>
                </div>
                <div class="col-sm-4">
                    <form method="get" action="{{ $reportActionUrl }}" class="form-inline text-right">
                        <input type="hidden" name="mode" value="{{ $bimbinganReport['mode'] }}">
                        <label for="tahun-ajaran" class="sr-only">Tahun Ajaran</label>
                        <select id="tahun-ajaran" name="tahun_ajaran" class="form-control" onchange="this.form.submit()">
                            @forelse ($bimbinganReport['period_options'] as $period)
                                <option value="{{ $period }}" {{ $period === $bimbinganReport['selected_year'] ? 'selected' : '' }}>
                                    Tahun Ajaran {{ $period }}
                                </option>
                            @empty
                                <option value="{{ $bimbinganReport['selected_year'] }}" selected>
                                    Tahun Ajaran {{ $bimbinganReport['selected_year'] }}
                                </option>
                            @endforelse
                        </select>
                        <a href="{{ $reportExcelUrl . '?' . http_build_query(['tahun_ajaran' => $bimbinganReport['selected_year'], 'mode' => $bimbinganReport['mode']]) }}"
                           class="btn btn-success" title="Download Excel">
                            <i class="fa fa-download"></i> Excel
                        </a>
                    </form>
                </div>
            </div>

            <div class="row" style="margin-top: 20px;">
                <div class="col-sm-3">
                    <div class="well well-sm text-center" style="margin-bottom: 10px;">
                        <strong>{{ number_format($bimbinganReport['total_dosen']) }}</strong><br>
                        <small>{{ $bimbinganReport['is_detailed'] ? 'Dosen Pembimbing PU/PP' : 'Dosen Pembimbing Utama' }}</small>
                    </div>
                </div>
                @foreach ($bimbinganReport['programs'] as $program)
                    <div class="col-sm-3">
                        <div class="well well-sm text-center" style="margin-bottom: 10px;">
                            @if ($bimbinganReport['is_detailed'])
                                <strong>{{ number_format($bimbinganReport['total_penugasan_by_program'][$program['key']]['total'] ?? 0) }}</strong><br>
                                <small>
                                    {{ $program['label'] }} &middot;
                                    PU {{ number_format($bimbinganReport['total_penugasan_by_program'][$program['key']]['PU'] ?? 0) }} &middot;
                                    PP {{ number_format($bimbinganReport['total_penugasan_by_program'][$program['key']]['PP'] ?? 0) }}
                                </small>
                            @else
                                <strong>{{ number_format($bimbinganReport['total_mahasiswa_by_program'][$program['key']] ?? 0) }}</strong><br>
                                <small>Total Penugasan Mahasiswa - {{ $program['label'] }}</small>
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="col-sm-3">
                    <div class="well well-sm text-center" style="margin-bottom: 10px;">
                        <strong>{{ number_format($bimbinganReport['is_detailed'] ? $bimbinganReport['total_penugasan'] : $bimbinganReport['total_mahasiswa']) }}</strong><br>
                        <small>Total Penugasan - Semua Prodi</small>
                    </div>
                </div>
            </div>

            <p class="text-muted" style="margin: 5px 0 0;">
                {{ $bimbinganReport['selected_year'] }}: {{ $bimbinganReport['awal_label'] }} / {{ $bimbinganReport['akhir_label'] }}.
            </p>

            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-bordered table-hover table-condensed distribution-guidance-table" style="min-width: {{ $bimbinganReport['is_detailed'] ? '980px' : '720px' }};">
                    <thead>
                        @if ($bimbinganReport['is_detailed'])
                            <tr>
                                <th rowspan="3" class="text-center" style="vertical-align: middle;">No</th>
                                <th rowspan="3" style="vertical-align: middle;">Nama Dosen</th>
                                @foreach ($bimbinganReport['programs'] as $program)
                                    <th colspan="4" class="text-center">{{ $program['label'] }}</th>
                                @endforeach
                                <th colspan="3" class="text-center">Total</th>
                            </tr>
                            <tr>
                                @foreach ($bimbinganReport['programs'] as $program)
                                    <th colspan="2" class="text-center">Awal</th>
                                    <th colspan="2" class="text-center">Akhir</th>
                                @endforeach
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">PU</th>
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">PP</th>
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">Semua</th>
                            </tr>
                            <tr>
                                @foreach ($bimbinganReport['programs'] as $program)
                                    <th class="text-center">PU</th>
                                    <th class="text-center">PP</th>
                                    <th class="text-center">PU</th>
                                    <th class="text-center">PP</th>
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">No</th>
                                <th rowspan="2" style="vertical-align: middle;">Nama Dosen</th>
                                @foreach ($bimbinganReport['programs'] as $program)
                                    <th colspan="2" class="text-center">{{ $program['label'] }}</th>
                                @endforeach
                                <th rowspan="2" class="text-center" style="vertical-align: middle;">Total</th>
                            </tr>
                            <tr>
                                @foreach ($bimbinganReport['programs'] as $program)
                                    <th class="text-center">Awal</th>
                                    <th class="text-center">Akhir</th>
                                @endforeach
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @forelse ($bimbinganReport['rows'] as $row)
                            <tr>
                                <td class="text-center">{{ $row['no'] }}</td>
                                <td>
                                    <strong>{{ $row['nama_dosen'] }}</strong>
                                    <small class="text-muted" style="display: block;">{{ $row['kode_dosen'] }}</small>
                                </td>
                                @if ($bimbinganReport['is_detailed'])
                                    @foreach ($bimbinganReport['programs'] as $program)
                                        <td class="text-center">{{ $row['role_counts'][$program['key']]['Ganjil']['PU'] }}</td>
                                        <td class="text-center">{{ $row['role_counts'][$program['key']]['Ganjil']['PP'] }}</td>
                                        <td class="text-center">{{ $row['role_counts'][$program['key']]['Genap']['PU'] }}</td>
                                        <td class="text-center">{{ $row['role_counts'][$program['key']]['Genap']['PP'] }}</td>
                                    @endforeach
                                    <td class="text-center"><strong>{{ $row['role_totals']['PU'] }}</strong></td>
                                    <td class="text-center"><strong>{{ $row['role_totals']['PP'] }}</strong></td>
                                    <td class="text-center"><strong>{{ $row['grand_total'] }}</strong></td>
                                @else
                                    @foreach ($bimbinganReport['programs'] as $program)
                                        <td class="text-center">{{ $row['counts'][$program['key']]['Ganjil'] }}</td>
                                        <td class="text-center">{{ $row['counts'][$program['key']]['Genap'] }}</td>
                                    @endforeach
                                    <td class="text-center"><strong>{{ $row['total'] }}</strong></td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $bimbinganReport['is_detailed'] ? 5 + (count($bimbinganReport['programs']) * 4) : 3 + (count($bimbinganReport['programs']) * 2) }}" class="text-center">
                                    Belum ada data penugasan {{ $bimbinganReport['is_detailed'] ? 'PU/PP' : 'Pembimbing Utama' }} pada tahun ajaran ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-muted" style="margin: 15px 0 0;">
                Distribusi dihitung dari SK pembimbing: Awal September-Februari dan Akhir Maret-Agustus.
                Laporan menampilkan Teknik Informatika dan Sistem Informasi secara terpisah pada setiap kolom{{ $bimbinganReport['is_detailed'] ? ', termasuk peran PU dan PP' : '' }}.
            </p>
        </div>
    </div>
</div>
@endsection
