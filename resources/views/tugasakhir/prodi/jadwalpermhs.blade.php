@extends('tugasakhir.index')
@section('isi')
    @php
        $isRiwayat = isset($isRiwayat) ? $isRiwayat : false;
        $tipeUjian = isset($tipe_ujian) ? $tipe_ujian : 'ujianmeja';
        $namaTipeUjian = $tipeUjian == 'proposal' ? 'Proposal' : ($tipeUjian == 'seminarhasil' ? 'Seminar Hasil' : 'Ujian Meja');
        $pageTitle = $isRiwayat
            ? 'Riwayat Jadwal ' . $namaTipeUjian . ' Per Mahasiswa'
            : 'Daftar Jadwal ' . $namaTipeUjian . ' Per Mahasiswa';
    @endphp
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
            <!-- End page heading -->

            <!-- Begin breadcrumb -->
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">{{$pageTitle}}</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <div class="clearfix">
                <h3 class="page-heading pull-left">{{$pageTitle}}</h3>
                <div class="pull-right" style="margin-top: 15px;">
                    @if($isRiwayat)
                        <a href="{{ url('prodi/jadwalpermhs/'.$tipeUjian) }}" class="btn btn-primary btn-perspective">
                            <i class="fa fa-calendar"></i> Jadwal Berjalan
                        </a>
                    @else
                        <a href="{{ url('prodi/jadwalpermhs/'.$tipeUjian.'/riwayat') }}" class="btn btn-default btn-perspective">
                            <i class="fa fa-history"></i> Lihat Riwayat
                        </a>
                    @endif
                </div>
            </div>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Ujian</th>
                            <th>Nama Periode</th>
                            <th>Kuota</th>
                            <th>Jumlah Peserta</th>
                            {{-- <th>Status Ujian</th> --}}
                            <th>Detail Peserta</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->tgl_ujian}}</td>
                                <td>{{$value->nama_periode}}</td>
                                <td>{{$value->kuota}}</td>
                                <td>{{$value->jml_peserta}}</td>
                                {{-- <<td>{{$value->status == 0 ? "td>{{$value->status == 0 ? "<td>{{$d->status == 0 ? "Belum terlaksana" : "Terlaksana"}}</td>" : "Terlaksana"}}</td>" : "Terlaksana"}}</td> --}}
                                <td><a href="{{ url('prodi/detail_jadwalpermhs/'.$value->pendaftaran_id)}}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    @if($isRiwayat)
                                        Belum ada riwayat jadwal ujian yang tanggalnya sudah lewat.
                                    @else
                                        Belum ada jadwal ujian hari ini atau yang akan datang.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

