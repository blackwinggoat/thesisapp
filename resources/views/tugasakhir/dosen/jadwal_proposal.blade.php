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
        <h3 class="page-heading">Jadwal Ujian Proposal</h3>
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
                                {{"-"}}
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
                                {{"-"}}
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
                                {{"-"}}
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
                                {{"-"}}
                                @else
                                @if ($d->ketua_sidang_id == auth()->user()->name)
                                <b>{{ helper::getNamaDosenByKode($d->ketua_sidang_id) }}</b>
                                @else
                                {{ helper::getNamaDosenByKode($d->ketua_sidang_id) }}
                                @endif
                                @endif
                            </td>
                                @if (helper::getStatusSKUjianProposalForMahasiswa($d->C_NPM) != '')
                            <td><a target="_blank"
                                    href="{{ url('dsn/surat_sk_proposal')}}/{{helper::getPendaftaranIdForDosen($d->C_NPM)}}/{{$d->C_NPM}}"><i
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
