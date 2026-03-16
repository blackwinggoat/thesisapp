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
        <h3 class="page-heading">Jadwal Ujian TA</h3>
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nim</th>
                            <th>Nama Mahasiswa</th>
                            <th>Tipe Ujian</th>
                            <th>Ruangan</th>
                            <th>Jam</th>
                            <th>Waktu Ujian</th>
                            <th>Pembimbing Utama</th>
                            <th>Pembimbing Pendamping</th>
                            <th>Penguji I</th>
                            <th>Penguji II</th>
                            <th>Penguji III</th>
                            <th>Ketua Sidang</th>
                            <th>SK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $i => $d)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$i}}</td>
                            <td>{{$d->C_NPM}}</td>
                            <td>{{$d->NAMA_MAHASISWA}}</td>
                            <td>{{$d->tipe_ujian == 0 ? "Proposal" : "Ujian Meja"}}</td>
                            <td>{{$d->nama_ruangan}}</td>
                            <td>{{$d->jam_ujian}}</td>
                            <td>{{$d->tgl_ujian}}</td>
                            @php
                            $pembimbing1 = \App\Dosen::where("C_KODE_DOSEN",$d->pembimbing_I_id)->first();
                            $pembimbing2 = \App\Dosen::where("C_KODE_DOSEN",$d->pembimbing_II_id)->first();
                            $penguji1 = \App\Dosen::where("C_KODE_DOSEN",$d->penguji_I_id)->first();
                            $penguji2 = \App\Dosen::where("C_KODE_DOSEN",$d->penguji_II_id)->first();
                            $penguji3 = \App\Dosen::where("C_KODE_DOSEN",$d->penguji_III_id)->first();
                            $ketuasidang = \App\Dosen::where("C_KODE_DOSEN",$d->ketua_sidang_id)->first();
                            @endphp
                            <td>

                                @if ($pembimbing1->C_KODE_DOSEN == auth()->user()->name)
                                <b>{{$pembimbing1->NAMA_DOSEN}}</b>
                                @else
                                {{$pembimbing1->NAMA_DOSEN}}
                                @endif
                            </td>
                            <td>
                                @if ($pembimbing2->C_KODE_DOSEN == auth()->user()->name)
                                <b>{{$pembimbing2->NAMA_DOSEN}}</b>
                                @else
                                {{$pembimbing2->NAMA_DOSEN}}
                                @endif
                            </td>
                            <td>
                                @if ($penguji1 == null)
                                {{"-"}}
                                @else
                                @if ($penguji1->C_KODE_DOSEN == auth()->user()->name)
                                <b>{{$penguji1->NAMA_DOSEN}}</b>
                                @else
                                {{$penguji1->NAMA_DOSEN}}
                                @endif
                                @endif
                            </td>
                            <td>
                                @if ($penguji2 == null)
                                {{"-"}}
                                @else
                                @if ($penguji2->C_KODE_DOSEN == auth()->user()->name)
                                <b>{{$penguji2->NAMA_DOSEN}}</b>
                                @else
                                {{$penguji2->NAMA_DOSEN}}
                                @endif
                                @endif
                            </td>
                            <td>
                                @if ($penguji3 == null)
                                {{"-"}}
                                @else
                                @if ($penguji3->C_KODE_DOSEN == auth()->user()->name)
                                <b>{{$penguji3->NAMA_DOSEN}}</b>
                                @else
                                {{$penguji3->NAMA_DOSEN}}
                                @endif
                                @endif
                            </td>
                            <td>
                                @if ($ketuasidang == null)
                                {{"-"}}
                                @else
                                @if ($ketuasidang->C_KODE_DOSEN == auth()->user()->name)
                                <b>{{$ketuasidang->NAMA_DOSEN}}</b>
                                @else
                                {{$ketuasidang->NAMA_DOSEN}}
                                @endif
                                @endif
                            </td>
                            @if (helper::getStatusSKUjianMejaForMahasiswa($d->C_NPM) != '')
                            <td><a target="_blank"
                                    href="{{ url('dsn/surat_sk_ujian_meja')}}/{{$d->C_NPM}}"><i
                                        class="fa fa-paperclip icon-square icon-xs icon-dark"></i></a></td>
                            @else
                            <td><span class="badge badge-danger">SK Ujian Proposal Belum Ada</span></td>
                            @endif
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