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
            <li class="active">Riwayat SK Pengusulan Ujian TA</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Daftar Riwayat SK Pengusulan Ujian TA</h3>
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                    <tr>
                        <th>No</th>
                        <th>Nomor SK</th>
                        <th>Tgl Pengusulan</th>
                        <th>Detail</th>
                        <th>Cetak</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $key => $value)
                    <tr class="odd gradeX">
                        <td width="1%" align="center">{{++$key}}</td>
                        <td>{{$value->nomor}}</td>
                        <td>{{$value->tgl_surat}}</td>
                        <td>
                            <a class="btn btn-info" href="{{url('prodi/detail_riwayat_sk_pengusulan_tim_ujian_ta')}}/{{str_replace("/", "$", $value->nomor)}}">Detail</a>
                        </td>
                        <td>
                            <a class="btn btn-success" href="{{url('prodi/cetak_riwayat_sk_pengusulan_tim_ujian_ta')}}/{{str_replace("/", "$", $value->nomor)}}">Cetak</a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->
@endsection


