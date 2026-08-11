@extends('tugasakhir.index')
@section('isi')
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Pusat Laporan <small>{{ $reportContext['label'] }}</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('prodi/report') }}">Report</a></li>
            <li class="active">Pusat Laporan</li>
        </ol>

        @if (!empty($reportWarnings))
            <div class="alert alert-warning">
                Sebagian laporan belum dapat dimuat. Silakan coba lagi atau periksa konfigurasi database.
            </div>
        @endif

        <div class="the-box">
            <div class="row">
                <div class="col-sm-8">
                    <h3 class="small-title" style="margin-top: 0;">Distribusi Jumlah Bimbingan Utama</h3>
                    <p class="text-muted" style="margin-bottom: 0;">
                        Jumlah mahasiswa yang ditugaskan kepada dosen sebagai Pembimbing Utama berdasarkan tanggal SK pembimbing.
                        Mahasiswa dihitung satu kali pada setiap semester.
                    </p>
                </div>
                <div class="col-sm-4">
                    <form method="get" action="{{ url('prodi/report/laporan') }}" class="form-inline text-right">
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
                        <a href="{{ url('prodi/report/laporan/excel') . '?tahun_ajaran=' . urlencode($bimbinganReport['selected_year']) }}"
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
                        <small>Dosen Pembimbing Utama</small>
                    </div>
                </div>
                @foreach ($bimbinganReport['programs'] as $program)
                    <div class="col-sm-3">
                        <div class="well well-sm text-center" style="margin-bottom: 10px;">
                            <strong>{{ number_format($bimbinganReport['total_mahasiswa_by_program'][$program['key']] ?? 0) }}</strong><br>
                            <small>Total Penugasan Mahasiswa - {{ $program['label'] }}</small>
                        </div>
                    </div>
                @endforeach
                <div class="col-sm-3">
                    <div class="well well-sm text-center" style="margin-bottom: 10px;">
                        <strong>{{ number_format($bimbinganReport['total_mahasiswa']) }}</strong><br>
                        <small>Total Penugasan Mahasiswa - Semua Prodi</small>
                    </div>
                </div>
            </div>

            <p class="text-muted" style="margin: 5px 0 0;">
                {{ $bimbinganReport['selected_year'] }}: {{ $bimbinganReport['awal_label'] }} / {{ $bimbinganReport['akhir_label'] }}.
            </p>

            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-bordered table-striped table-hover" style="min-width: 720px;">
                    <thead>
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
                    </thead>
                    <tbody>
                        @forelse ($bimbinganReport['rows'] as $row)
                            <tr>
                                <td class="text-center">{{ $row['no'] }}</td>
                                <td>
                                    <strong>{{ $row['nama_dosen'] }}</strong>
                                    <small class="text-muted" style="display: block;">{{ $row['kode_dosen'] }}</small>
                                </td>
                                @foreach ($bimbinganReport['programs'] as $program)
                                    <td class="text-center">{{ $row['counts'][$program['key']]['Ganjil'] }}</td>
                                    <td class="text-center">{{ $row['counts'][$program['key']]['Genap'] }}</td>
                                @endforeach
                                <td class="text-center"><strong>{{ $row['total'] }}</strong></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + (count($bimbinganReport['programs']) * 2) }}" class="text-center">
                                    Belum ada data penugasan Pembimbing Utama pada tahun ajaran ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <p class="text-muted" style="margin: 15px 0 0;">
                Distribusi dihitung dari SK pembimbing: Awal September-Februari dan Akhir Maret-Agustus.
                Laporan menampilkan seluruh program studi untuk akun Prodi.
            </p>
        </div>
    </div>
</div>
@endsection
