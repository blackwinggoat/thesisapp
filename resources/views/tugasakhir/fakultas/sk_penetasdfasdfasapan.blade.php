@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading">Sistem Informasi Program Studi <small>Tugas Akhir</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/')}}">Home</a></li>
            <li class="active">SK Penetapan Pembimbing</li>
        </ol>


        <h3 class="page-heading">Set Nomor Surat Penetapan Pembimbing</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <form method="post" action="{{url('prodi/surat_pengusulan')}}" target=”_blank”>
                {{ csrf_field() }}
                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Daftar</label>
                        <div class="table-responsive col-lg-6">
                            <table class="table table-th-block">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nim</th>
                                    <th>Nama</th>
                                    <th>No SK</th>
                                    <th>Tanggal Penetapan</th>
                                    <th>Pembimbing Utama</th>
                                    <th>Pembimbing Pendamping</th>
                                    <th>Cetak SK</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $a=0 ?>


                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div><!-- /.table-responsive -->
                    </div>
                </fieldset>
            </form>
        </div><!-- /.the-box -->




@endsection




