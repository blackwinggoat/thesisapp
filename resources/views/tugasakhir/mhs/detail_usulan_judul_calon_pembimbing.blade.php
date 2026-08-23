@extends('tugasakhir.index')
@section('isi')

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
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
                            <th>Stambuk</th>
                            <th>Nama</th>
                            <th>Judul</th>
                            <th>Jenis</th>
                            <th>Waktu Usul</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $value)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$key}}</td>
                            <td>{{$value->C_NPM}}</td>
                            <td>{{helper::getNamaMhs($value->C_NPM)}}</td>
                            <td>{{$value->judul}}</td>
                            <td>{!! helper::jenisTugasAkhirBadge($value->jenis_tugas_akhir_id ?? null) !!}</td>
                            <td>{{$value->created_at}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
    </div><!-- /.container-fluid -->
    @endsection
