@extends('tugasakhir.index')
@section('isi')

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Daftar Usulan Judul</li>
        </ol>
        <h3 class="page-heading">Usulan Judul</h3>
        <div class="the-box">
            <br>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Topik</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data_riwayat_usulan as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->C_NPM}}</td>
                                <td>{{$value->NAMA_MAHASISWA}}</td>
                                <th>{{$value->topik}}</th>
                                <th>
                                    @if($value->status==0)
                                        Belum dikonfirmasi
                                    @elseif($value->status==1)
                                        Diterima
                                    @elseif($value->status==2)
                                        Ditolak
                                    @endif
                                </th>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
    </div><!-- /.container-fluid -->
    @endsection