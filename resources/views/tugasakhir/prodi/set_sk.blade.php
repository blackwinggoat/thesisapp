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
            <li><a href="{{ url('/')}}">Home</a></li>
            <li class="active">SK Pengusulan Pembimbing</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Penetapan Nomor SK Pembimbing</h3>
        @if (session('status') == 'success')
            <div class="alert alert-success alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>Nomor SK Berhasil Ditambahkan <i class="fa fa-smile-o"></i></p>
            </div>
        @elseif(session('status') == 'error')
            <div class="alert alert-danger alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>Nomor SK Gagal Ditambahkan, Ada Data Yang Duplikat <i class="fa fa-smile-o"></i></p>
            </div>
        @endif
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-th-block">
                    <thead class="the-box dark full">
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Pembimbing Utama</th>
                        <th>Pembimbing Pendamping</th>
                        <th>Judul</th>
                        <th>Nomor SK Pembimbing</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @php
                            $path = "";
                            if (Auth::user()->level==6) {
                                $path = "akademikprodi";
                            }else {
                                $path = "prodi";
                            }
                        @endphp
                    @foreach($data as $key => $value)
                        <form method="post" action="{{url("$path/add_sk_pembimbing")}}">
                            {{ csrf_field() }}
                    <tr class="odd gradeX">
                        <td width="1%" align="center">{{++$key}}</td>
                        <td>{{$value->C_NPM}}</td>
                        <td>{{isset($value->C_NPM) ? helper::getNamaMhs($value->C_NPM) :'' }}</td>
                        <td>{{isset($value->pembimbing_I_id) ? helper::getDeskripsi($value->pembimbing_I_id) :'' }}</td>
                        <td>{{isset($value->pembimbing_II_id) ? helper::getDeskripsi($value->pembimbing_II_id) :'' }}</td>
                        <td>{{$value->judul}}</td>
                        <td>
                            <input required type="text" class="form-control bold-border" name="nomor_sk" value="{{isset($value->bimbingan_id) ? helper::getNomorSkPerMhsFromTrtPenguji($value->C_NPM) :'' }}"/>
                            *0000/H.20/FIK-UMI/XII/2019
                            <input type="hidden" class="form-control bold-border" name="bimbingan_id" value="{{$value->bimbingan_id}}"/>
                            <input type="hidden" class="form-control bold-border" name="pendaftaran_id" value="{{$value->pendaftaran_id}}"/>
                            <input type="hidden" class="form-control bold-border" name="c_npm" value="{{$value->C_NPM}}"/>
                        </td>
                        <td><button class="btn btn-primary btn-perspective" type="submit">Simpan</button></td>
                    </tr>
                        </form>
                    @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->

        <!-- BEGIN DATA TABLE -->

        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->
@endsection


