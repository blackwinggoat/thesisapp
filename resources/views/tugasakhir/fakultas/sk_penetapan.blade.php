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
                    @foreach($data as $key => $value)
                        <form method="post" action="{{url('fakultas/addskpembimbing')}}">
                            {{ csrf_field() }}
                    <tr class="odd gradeX">
                        <td width="1%" align="center">{{++$key}}</td>
                        <td>{{$value->C_NPM}}</td>
                        <td>{{isset($value->C_NPM) ? helper::getNamaMhs($value->C_NPM) :'' }}</td>
                        <td>{{isset($value->pembimbing_I_id) ? helper::getDeskripsi($value->pembimbing_I_id) :'' }}</td>
                        <td>{{isset($value->pembimbing_II_id) ? helper::getDeskripsi($value->pembimbing_II_id) :'' }}</td>
                        <td>{{$value->judul}}</td>
                        <td>
                            <input type="text" class="form-control bold-border" name="nomor_sk" value="{{isset($value->bimbingan_id) ? helper::getNomorSkPerMhs($value->bimbingan_id) :'' }}"/>
                            *0000/H.20/FIK-UMI/XII/2019
                            <input type="hidden" class="form-control bold-border" name="bimbingan_id" value="{{$value->bimbingan_id}}"/>
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


