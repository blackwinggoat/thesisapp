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
                <li class="active">Peserta Ujian Meja</li>
            </ol>
            <!-- End breadcrumb -->

            <?php if(session('status') == 'success'): ?>
                <div class="alert alert-success alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><strong>Status!</strong></p>
                    <p>Berhasil Konfirmasi Hasil Ujian Meja Semua Mahasiswa. Total Mahasiswa Terkonfirmasi Adalah <b><?= session('total') ?></b></p>
                </div>
            <?php elseif(session('status') == 'error'): ?>
                <div class="alert alert-danger alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><strong>Status!</strong></p>
                    <p>Gagal Konfirmasi Hail Ujian Meja Semua Mahasiswa. Total Mahasiswa Terkonfirmasi Adalah <b><?= session('total') ?></b></p>
                </div>
            <?php endif; ?>

            <div class="the-box">
                <a href="{{url('prodi/approve_hasilujian_ta_all_post/')}}" class="btn btn-info mb-5" style="margin-bottom: 20px">Konfirmasi Semua</a>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Ujian</th>
                            <th>Nama Periode</th>
                            <th>Kuota</th>
                            <th>Jumlah Peserta</th>
                            <th>Detail Peserta</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->tgl_ujian}}</td>
                                <td>{{$value->nama_periode}}</td>
                                <td>{{$value->kuota}}</td>
                                <td>{{$value->jml_peserta}}</td>
                                {{-- <<td>{{$value->status == 0 ? "td>{{$value->status == 0 ? "<td>{{$d->status == 0 ? "Belum terlaksana" : "Terlaksana"}}</td>" : "Terlaksana"}}</td>" : "Terlaksana"}}</td> --}}
                                <td><a href="{{ url('prodi/detail_hasilujian_ta/'.$value->pendaftaran_id)}}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a></td>
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


