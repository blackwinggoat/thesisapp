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

        <h3 class="page-heading">Daftar Riwayat SK Pembimbing</h3>
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
                <form method="post" action="{{url('fakultas/cetakskpembimbing')}}" target="_blank">
                    {{ csrf_field() }}
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>No SK Pembimbing</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Pembimbing Utama</th>
                                <th>Pembimbing Pendamping</th>
                                <th>Status</th>
                                <th>SK</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data_sk as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->nomor_sk}}</td>
                                <td>{{$value->C_NPM}}</td>
                                <td>{{isset($value->C_NPM) ? helper::getNamaMhs($value->C_NPM) :'' }}</td>
                                <td>{{isset($value->pembimbing_I_id) ? helper::getDeskripsi($value->pembimbing_I_id) :'' }}
                                </td>
                                <td>{{isset($value->pembimbing_II_id) ? helper::getDeskripsi($value->pembimbing_II_id) :'' }}
                                </td>
                                <td>
                                    @if (helper::getStatusApproveWakilDekan($value->sk_pembimbing_id) == 0)
                                    <a href="#" class="btn btn-sm btn-danger">Belum di Approve</a>
                                    @elseif(helper::getStatusApproveWakilDekan($value->sk_pembimbing_id) == 1)
                                    <a href="#" class="btn btn-sm btn-info">Approve Wakil Dekan</a>
                                    @elseif(helper::getStatusApproveWakilDekan($value->sk_pembimbing_id) == 2)
                                    <a href="#" class="btn btn-sm btn-success">Approve Dekan</a>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-primary fa fa-paperclip btn-perspective" name="nomor"
                                        value="{{$value->nomor_sk}}" type="submit"></button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->


        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Daftar Surat Usulan Pembimbing</h3>
        <div class="the-box">
            <div class="table-responsive">
                <form method="post" action="{{url('fakultas/sk_penetapan')}}">
                    {{ csrf_field() }}
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>No Surat Usulan</th>
                                <th>Tanggal</th>
                                <th>Detail</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayat_usulan as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->nomor}}</td>
                                <td>{{$value->tgl_surat}}</td>
                                <td>
                                    <button class="btn btn-primary fa fa-paperclip btn-perspective" name="nomor"
                                        value="{{$value->nomor}}" type="submit"></button>
                                </td>
                                <td>
                                    <?= helper::getStatusSuratUsulan($value->nomor) ?>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </form>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->


    </div><!-- /.container-fluid -->
    @endsection