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
            <li class="active">SK Penetapan Pembimbing</li>
        </ol>
        <!-- End breadcrumb -->

        <h3 class="page-heading">Daftar Riwayat SK Ujian TA</h3>
        @if (session('status') == 'success')
            <div class="alert alert-success alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>SK Berhasil Diapprove<i class="fa fa-smile-o"></i></p>
            </div>
        @elseif(session('status') == 'error')
            <div class="alert alert-danger alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>SK Gagal Diapprove <i class="fa fa-smile-o"></i></p>
            </div>
        @endif
        <div class="the-box">
            <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>No SK Pembimbing</th>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Pembimbing Utama</th>
                            <th>Pembimbing Pendamping</th>
                            <th>Approve</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data_sk as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->nomor_sk}}</td>
                                <td>{{$value->C_NPM}}</td>
                                <td>{{isset($value->C_NPM) ? helper::getNamaMhs($value->C_NPM) :'' }}</td>
                                <td>{{isset($value->pembimbing_I_id) ? helper::getDeskripsi($value->pembimbing_I_id) :'' }}</td>
                                <td>{{isset($value->pembimbing_II_id) ? helper::getDeskripsi($value->pembimbing_II_id) :'' }}</td>
                                <td>
                                    @if (helper::getStatusFromSkPenugasan($value->sk_penugasan_id) == 1)
                                        <a href="{{url('/dekan/appove_sk_ujian_ta/')}}/{{$value->sk_penugasan_id}}" class="btn btn-info fa fa-paperclip btn-perspective">Approve</a>
                                    @else
                                        <a href="" class="btn btn-secondary fa fa-paperclip btn-perspective disabled">Telah di Approve</a>
                                    @endif
                                    
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
    </div><!-- /.container-fluid -->
@endsection


