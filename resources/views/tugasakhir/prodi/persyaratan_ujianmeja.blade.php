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
                <li class="active">Peserta Proposal</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Daftar Pengajuan Persyaratan Ujian Meja</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Detail</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->C_NPM}}</td>
                                <td>{{$value->NAMA_MAHASISWA}}</td>
                                @if (auth()->user()->name == 'akademikproditi' || auth()->user()->name == 'akademikprodisi')
                                    <td><a href="{{ url('akademikprodi/detail_persyaratan_ujianmeja/'.$value->C_NPM)}}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a></td>
                                @else
                                    <td><a href="{{ url('prodi/detail_persyaratan_ujianmeja/'.$value->C_NPM)}}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a></td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div>
@endsection


