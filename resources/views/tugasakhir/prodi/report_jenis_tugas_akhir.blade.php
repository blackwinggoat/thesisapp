@extends('tugasakhir.index')
@section('isi')
<style>
    .jenis-ta-toolbar { align-items: center; display: flex; flex-wrap: wrap; gap: 8px; justify-content: space-between; margin-bottom: 18px; }
    .jenis-ta-toolbar .btn-group { margin-bottom: 0; }
    .jenis-ta-filter { align-items: flex-end; display: flex; flex-wrap: wrap; gap: 10px; }
    .jenis-ta-filter .form-group { margin: 0; min-width: 190px; }
    .jenis-ta-filter label { display: block; font-size: 12px; margin-bottom: 5px; }
    .jenis-ta-summary { display: grid; gap: 12px; grid-template-columns: repeat(4, minmax(0, 1fr)); margin: 18px 0; }
    .jenis-ta-summary-item { background: #fff; border: 1px solid #d9e2ec; border-radius: 6px; min-height: 92px; padding: 15px; }
    .jenis-ta-summary-item .value { color: #102a43; display: block; font-size: 25px; font-weight: 700; line-height: 1.1; }
    .jenis-ta-summary-item .label-text { color: #52606d; display: block; font-size: 12px; margin-top: 7px; }
    .jenis-ta-section { border-top: 1px solid #d9e2ec; margin-top: 24px; padding-top: 20px; }
    .jenis-ta-section h3 { color: #243b53; font-size: 17px; margin: 0 0 6px; }
    .jenis-ta-section-note { color: #627d98; font-size: 12px; margin: 0 0 15px; }
    .jenis-ta-composition { background: #e9eff5; border-radius: 4px; display: flex; height: 18px; margin-bottom: 18px; overflow: hidden; width: 100%; }
    .jenis-ta-segment { min-width: 2px; }
    .jenis-ta-bar-track { background: #e8eef3; border-radius: 3px; height: 9px; margin-top: 5px; overflow: hidden; }
    .jenis-ta-bar { height: 100%; }
    .tone-1 { background: #16794a; }
    .tone-2 { background: #2563a8; }
    .tone-3 { background: #b7791f; }
    .tone-4 { background: #b8325a; }
    .tone-5 { background: #0f766e; }
    .tone-6 { background: #6b46a5; }
    .jenis-ta-code { border-radius: 3px; color: #fff; display: inline-block; font-size: 11px; font-weight: 700; min-width: 62px; padding: 4px 7px; text-align: center; }
    .jenis-ta-table th { background: #eef3f7; color: #243b53; vertical-align: middle !important; }
    .jenis-ta-table td { vertical-align: middle !important; }
    .jenis-ta-table .metric { font-size: 12px; white-space: nowrap; }
    .jenis-ta-table .metric small { color: #627d98; display: block; }
    .jenis-ta-data-note { background: #fff8e6; border-left: 4px solid #b7791f; color: #674d00; margin-top: 16px; padding: 11px 13px; }
    @media (max-width: 991px) {
        .jenis-ta-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    @media (max-width: 600px) {
        .jenis-ta-toolbar { align-items: stretch; display: block; }
        .jenis-ta-toolbar .btn-group, .jenis-ta-toolbar .btn { margin-bottom: 8px; width: 100%; }
        .jenis-ta-toolbar .btn-group .btn { margin-bottom: 0; width: 50%; }
        .jenis-ta-filter { display: block; }
        .jenis-ta-filter .form-group { margin-bottom: 10px; min-width: 0; width: 100%; }
        .jenis-ta-summary { grid-template-columns: 1fr; }
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Persebaran Jenis Tugas Akhir <small>{{ $scope['program_studi'] }}</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('prodi/report') }}">Report</a></li>
            <li class="active">Persebaran Jenis TA</li>
        </ol>

        @if (!empty($reportWarnings))
            <div class="alert alert-warning">Laporan belum dapat dimuat sempurna. Silakan periksa kembali konfigurasi database.</div>
        @endif

        <div class="the-box">
            <div class="jenis-ta-toolbar">
                <div class="btn-group" role="group" aria-label="Sudut pandang laporan">
                    <a class="btn {{ $report['mode'] === 'tahun_ajaran' ? 'btn-primary' : 'btn-default' }}"
                       href="{{ route('prodi.report_jenis_tugas_akhir', ['mode' => 'tahun_ajaran', 'program_studi' => $scope['nim_prefix']]) }}">
                        <i class="fa fa-calendar"></i> Tahun Ajaran
                    </a>
                    <a class="btn {{ $report['mode'] === 'angkatan' ? 'btn-primary' : 'btn-default' }}"
                       href="{{ route('prodi.report_jenis_tugas_akhir', ['mode' => 'angkatan', 'program_studi' => $scope['nim_prefix']]) }}">
                        <i class="fa fa-users"></i> Angkatan
                    </a>
                </div>

                <a class="btn btn-danger"
                   href="{{ route('prodi.report_jenis_tugas_akhir_pdf') . '?' . http_build_query(['mode' => $report['mode'], 'periode' => $report['selected_period'], 'program_studi' => $scope['nim_prefix']]) }}"
                   target="_blank" rel="noopener">
                    <i class="fa fa-file-pdf-o"></i> Generate PDF
                </a>
            </div>

            <form method="get" action="{{ route('prodi.report_jenis_tugas_akhir') }}" class="jenis-ta-filter">
                <input type="hidden" name="mode" value="{{ $report['mode'] }}">
                @if ($scope['can_select_program'])
                    <div class="form-group">
                        <label for="jenis-ta-prodi">Program Studi</label>
                        <select id="jenis-ta-prodi" name="program_studi" class="form-control">
                            <option value="130" {{ $scope['nim_prefix'] === '130' ? 'selected' : '' }}>Teknik Informatika</option>
                            <option value="131" {{ $scope['nim_prefix'] === '131' ? 'selected' : '' }}>Sistem Informasi</option>
                        </select>
                    </div>
                @else
                    <input type="hidden" name="program_studi" value="{{ $scope['nim_prefix'] }}">
                @endif

                <div class="form-group">
                    <label for="jenis-ta-periode">{{ $report['mode_label'] }}</label>
                    <select id="jenis-ta-periode" name="periode" class="form-control">
                        @forelse ($report['period_options'] as $period)
                            <option value="{{ $period }}" {{ $period === $report['selected_period'] ? 'selected' : '' }}>{{ $period }}</option>
                        @empty
                            <option value="">Belum ada data</option>
                        @endforelse
                    </select>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Terapkan</button>
            </form>

            <div class="jenis-ta-summary">
                <div class="jenis-ta-summary-item">
                    <span class="value">{{ number_format($report['summary']['total']) }}</span>
                    <span class="label-text">Mahasiswa lulus - {{ $report['selected_period'] ?: 'Belum ada periode' }}</span>
                </div>
                <div class="jenis-ta-summary-item">
                    <span class="value">{{ number_format($report['summary']['type_count']) }}</span>
                    <span class="label-text">Jenis tugas akhir digunakan</span>
                </div>
                <div class="jenis-ta-summary-item">
                    <span class="value">{{ $report['summary']['dominant_code'] }}</span>
                    <span class="label-text">Jenis terbanyak - {{ number_format($report['summary']['dominant_percentage'], 2, ',', '.') }}%</span>
                </div>
                <div class="jenis-ta-summary-item">
                    <span class="value">{{ number_format($report['summary']['total_all_periods']) }}</span>
                    <span class="label-text">Total lulusan seluruh periode</span>
                </div>
            </div>

            <section class="jenis-ta-section">
                <h3>Komposisi {{ $report['mode_label'] }} {{ $report['selected_period'] ?: '-' }}</h3>
                <p class="jenis-ta-section-note">Persentase dihitung terhadap seluruh mahasiswa berstatus lulus pada periode terpilih.</p>

                @if ($report['summary']['total'] > 0)
                    <div class="jenis-ta-composition" aria-label="Komposisi jenis tugas akhir">
                        @foreach ($report['distribution'] as $type)
                            <div class="jenis-ta-segment {{ $type['tone'] }}"
                                 style="width: {{ $type['percentage'] }}%;"
                                 title="{{ $type['code'] }}: {{ number_format($type['percentage'], 2, ',', '.') }}%"></div>
                        @endforeach
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered jenis-ta-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;" class="text-center">No</th>
                                <th style="width: 105px;">Kode</th>
                                <th>Jenis Tugas Akhir</th>
                                <th style="width: 110px;" class="text-center">Jumlah</th>
                                <th style="width: 260px;">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report['distribution'] as $index => $type)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><span class="jenis-ta-code {{ $type['tone'] }}">{{ $type['code'] }}</span></td>
                                    <td>{{ $type['description'] }}</td>
                                    <td class="text-center"><strong>{{ number_format($type['count']) }}</strong></td>
                                    <td>
                                        <strong>{{ number_format($type['percentage'], 2, ',', '.') }}%</strong>
                                        <div class="jenis-ta-bar-track"><div class="jenis-ta-bar {{ $type['tone'] }}" style="width: {{ $type['percentage'] }}%;"></div></div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center">Belum ada mahasiswa lulus pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="jenis-ta-section">
                <h3>{{ $report['cross_title'] }}</h3>
                <p class="jenis-ta-section-note">{{ $report['cross_note'] }} Nilai pada setiap sel menunjukkan jumlah mahasiswa dan persentasenya.</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover jenis-ta-table">
                        <thead>
                            <tr>
                                <th>{{ $report['cross_dimension_label'] }}</th>
                                @foreach ($report['type_columns'] as $type)
                                    <th class="text-center">{{ $type['code'] }}</th>
                                @endforeach
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report['cross_distribution'] as $period)
                                <tr>
                                    <td><strong>{{ $period['period'] }}</strong></td>
                                    @foreach ($report['type_columns'] as $type)
                                        <td class="text-center metric">
                                            <strong>{{ number_format($period['counts'][$type['code']]['count']) }}</strong>
                                            <small>{{ number_format($period['counts'][$type['code']]['percentage'], 2, ',', '.') }}%</small>
                                        </td>
                                    @endforeach
                                    <td class="text-center"><strong>{{ number_format($period['total']) }}</strong></td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ count($report['type_columns']) + 2 }}" class="text-center">Belum ada data persebaran silang.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if ($report['summary']['fallback_date_count'] > 0 || $report['summary']['default_type_count'] > 0)
                <div class="jenis-ta-data-note">
                    <strong>Catatan kualitas data:</strong>
                    @if ($report['summary']['fallback_date_count'] > 0)
                        {{ number_format($report['summary']['fallback_date_count']) }} mahasiswa belum memiliki tanggal kelulusan yang dapat ditelusuri.
                    @endif
                    @if ($report['summary']['default_type_count'] > 0)
                        {{ number_format($report['summary']['default_type_count']) }} mahasiswa belum memiliki jenis tugas akhir dan sementara diklasifikasikan sebagai TA-SM.
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
