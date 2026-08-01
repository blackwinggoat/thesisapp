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
                <li class="active">Peserta ujian</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Berita Acara</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nim</th>
                            <th>Nama Mahasiswa</th>
                            <th>Pembimbing Utama</th>
                            <th>Pembimbing Pendamping</th>
                            <th>Penguji I</th>
                            <th>Penguji II</th>
                            <th>Penguji III</th>
                            <th>Ketua Sidang</th>
                            <th style="text-align: center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $i => $d)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$i}}</td>
                                <td>{{$d->C_NPM}}</td>
                                <td>{{$d->NAMA_MAHASISWA}}</td>
                                <td>{{ helper::getNamaDosenByKode($d->pembimbing_I_id) }}</td>
                                <td>{{ helper::getNamaDosenByKode($d->pembimbing_II_id) }}</td>
                                <td>{{ helper::getNamaDosenByKode($d->penguji_I_id) }}</td>
                                <td>{{ helper::getNamaDosenByKode($d->penguji_II_id) }}</td>
                                <td>{{ helper::getNamaDosenByKode($d->penguji_III_id) }}</td>
                                <td>{{ helper::getNamaDosenByKode($d->ketua_sidang_id) }}</td>
                                <td style="width: 240px; text-align: center">
                                    @if (empty(helper::getRuanganUjianTAByNim($d->C_NPM)))
                                        Berita Acara Belum Terbit
                                    @else
                                    <a href="{{ url("mhs/cetak_beritaacara_ujian/$d->pendaftaran_id/$d->C_NPM")}}" class="btn btn-info"><i class="fa fa-file-text"></i>Berita Acara</a>
                                    @endif
                                </td>
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

