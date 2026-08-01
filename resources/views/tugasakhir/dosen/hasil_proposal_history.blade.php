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
                <li class="active">History hasil proposal</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                <h3 class="page-heading" style="margin: 0;">History Berita Acara Proposal</h3>
                <a href="{{ url('dsn/hasil_proposal') }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
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
                            <th style="text-align: center">Keterangan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $i => $d)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{ ++$i }}</td>
                                <td>{{ $d->C_NPM }}</td>
                                <td>{{ $d->NAMA_MAHASISWA }}</td>
                                <td>
                                    @if ($d->pembimbing_I_id == auth()->user()->name)
                                        <b>{{ helper::getNamaDosenByKode($d->pembimbing_I_id) }}</b>
                                    @else
                                        {{ helper::getNamaDosenByKode($d->pembimbing_I_id) }}
                                    @endif
                                </td>
                                <td>
                                    @if ($d->pembimbing_II_id == auth()->user()->name)
                                        <b>{{ helper::getNamaDosenByKode($d->pembimbing_II_id) }}</b>
                                    @else
                                        {{ helper::getNamaDosenByKode($d->pembimbing_II_id) }}
                                    @endif
                                </td>
                                <td>
                                    @if (empty($d->penguji_I_id))
                                        -
                                    @else
                                        @if ($d->penguji_I_id == auth()->user()->name)
                                            <b>{{ helper::getNamaDosenByKode($d->penguji_I_id) }}</b>
                                        @else
                                            {{ helper::getNamaDosenByKode($d->penguji_I_id) }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if (empty($d->penguji_II_id))
                                        -
                                    @else
                                        @if ($d->penguji_II_id == auth()->user()->name)
                                            <b>{{ helper::getNamaDosenByKode($d->penguji_II_id) }}</b>
                                        @else
                                            {{ helper::getNamaDosenByKode($d->penguji_II_id) }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if (empty($d->penguji_III_id))
                                        -
                                    @else
                                        @if ($d->penguji_III_id == auth()->user()->name)
                                            <b>{{ helper::getNamaDosenByKode($d->penguji_III_id) }}</b>
                                        @else
                                            {{ helper::getNamaDosenByKode($d->penguji_III_id) }}
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if (empty($d->ketua_sidang_id))
                                        -
                                    @else
                                        @if ($d->ketua_sidang_id == auth()->user()->name)
                                            <b>{{ helper::getNamaDosenByKode($d->ketua_sidang_id) }}</b>
                                        @else
                                            {{ helper::getNamaDosenByKode($d->ketua_sidang_id) }}
                                        @endif
                                    @endif
                                </td>
                                <td style="width: 260px; text-align: center">
                                    Nilai Mahasiswa Telah Diapprove Oleh Prodi
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
