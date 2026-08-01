@if ($status == 0)
@section('tambahan', "- Mahasiswa Persiapan Ujian Proposal")
@elseif($status == 2)
@section('tambahan', "- Mahasiswa Persiapan Ujian TA")
@elseif($status == 3)
@section('tambahan', "- Lulusan Mahasiswa")
@elseif($status == 4)
@section('tambahan', "- Mahasiswa Non Aktif")
@endif

@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Peserta</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        @if ($status == 0)
        <h3 class="page-heading">Daftar Peserta Persiapan Ujian Proposal</h3>
        @elseif($status == 2)
        <h3 class="page-heading">Daftar Peserta Perisapan Ujian TA</h3>
        @elseif($status == 3)
        <h3 class="page-heading">Daftar Lulusan</h3>
        @else
        <h3 class="page-heading">Daftar Mahasiswa Non Aktif</h3>
        @endif
        <div class="the-box">
            <div class="text-left">
                <div class="row">
                    <form action="{{ $filterActionRoute ?? route('tampilDetailStatusBimbinganDenganFilterTanggal') }}" method="get">
                        @csrf
                        <input type="hidden" name="status" value="{{$status}}">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label" style="margin-top: 8px;">Dari</label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control datepicker bold-border"
                                                    data-date-format="yyyy-mm-dd" placeholder="TAHUN-BULAN-HARI"
                                                    name="tanggal_dari" value="{{ request('tanggal_dari') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="form-group">
                                            <label class="col-md-2 control-label"
                                                style="margin-top: 8px;">Sampai</label>
                                            <div class="col-md-10">
                                                <input type="text" class="form-control datepicker bold-border"
                                                    data-date-format="yyyy-mm-dd" placeholder="TAHUN-BULAN-HARI"
                                                    name="tanggal_sampai" value="{{ request('tanggal_sampai') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary btn-block"
                                        style="margin-top: 5px; margin-left: 25px;">Filter</button>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ $resetUrl ?? url('prodi/detail_status_bimbingan_mahasiswa/' . $status) }}" class="btn btn-danger btn-block"
                                        style="margin-top: 5px">Reset</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive" style="margin-top: 20px">
                <table class="table table-striped table-hover display" id="detail_bimbingan">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nim</th>
                            <th>Nama Mahasiswa</th>
                            <th>Pembimbing Utama</th>
                            <th>Pembimbing Pendamping</th>
                            <th>Judul</th>
                            <th>Tanggal Pembaharuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $i => $d)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$i}}</td>
                            <td>{{$d->C_NPM}}</td>
                            <td>{{helper::getNamaMhs($d->C_NPM)}}</td>
                            <td>{{ helper::getNamaDosenByKode($d->pembimbing_I_id) }}</td>
                            <td>{{ helper::getNamaDosenByKode($d->pembimbing_II_id) }}</td>
                            <td>{{$d->judul}}</td>
                            <td>{{$d->updated_at}}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data mahasiswa pada status ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->
</div>
@endsection
