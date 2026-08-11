@extends('tugasakhir.index')
@section('isi')
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Pusat Laporan <small>Prodi</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('prodi/report') }}">Report</a></li>
            <li class="active">Pusat Laporan</li>
        </ol>

        <div class="the-box">
            <div class="text-center" style="padding: 36px 20px;">
                <i class="fa fa-files-o" style="font-size: 42px; color: #3BAFDA;"></i>
                <h3>Pusat Laporan</h3>
                <p class="text-muted" style="max-width: 620px; margin: 0 auto 20px;">
                    Laporan akademik dan rekapitulasi Prodi dalam format Excel atau PDF akan dikumpulkan di halaman ini.
                </p>
                <a href="{{ url('prodi/report') }}" class="btn btn-primary btn-perspective">
                    <i class="fa fa-bar-chart-o"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
