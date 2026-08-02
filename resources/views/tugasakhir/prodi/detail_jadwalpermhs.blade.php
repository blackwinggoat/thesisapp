@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">Jadwal Ujian Per Mahasiswa</li>
            </ol>
            <div class="the-box">
                <div class="form-group">
                    <label class="col-lg-2 control-label">Tanggal Ujian</label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control bold-border" name="jadwal" value="{{$info->tgl_ujian}}" disabled>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Jumlah Peserta</label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control datepicker bold-border" name="jml_peserta" value="{{$info->jml_peserta}}" disabled>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Tipe Ujian</label>
                    <div class="col-lg-6">
                        @php
                            if($info->tipe_ujian == 0):
                                $tipe = "Proposal";
                            elseif($info->tipe_ujian == 2):
                                $tipe = "Ujian Meja";
                            endif;
                        @endphp
                        <input type="text" class="form-control bold-border" value="{{$tipe}}" name="jml_peserta" disabled>
                    </div>
                </div>
                <br>
                <br>
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
                            <th>Pengaturan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($data as $i => $d)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$i}}</td>
                                <td>{{$d->C_NPM}}</td>
                                <td>{{$d->NAMA_MAHASISWA}}</td>
                                @php
                                    $pembimbing1 = helper::getNamaDosenByKode($d->pembimbing_I_id);
                                    $pembimbing2 = helper::getNamaDosenByKode($d->pembimbing_II_id);
                                    $penguji1 = helper::getNamaDosenByKode($d->penguji_I_id);
                                    $penguji2 = helper::getNamaDosenByKode($d->penguji_II_id);
                                    $penguji3 = helper::getNamaDosenByKode($d->penguji_III_id);
                                    $ketuasidang = helper::getNamaDosenByKode($d->ketua_sidang_id);
                                @endphp
                                <td>{{$pembimbing1}}</td>
                                <td>{{$pembimbing2}}</td>
                                <td>
                                    @if ($penguji1 == '--')
                                        {{"-"}}
                                    @else
                                        {{$penguji1}}
                                    @endif
                                </td>
                                <td>
                                    @if ($penguji2 == '--')
                                        {{"-"}}
                                    @else
                                        {{$penguji2}}
                                    @endif
                                </td>
                                <td>
                                    @if ($penguji3 == '--')
                                        {{"-"}}
                                    @else
                                        {{$penguji3}}
                                    @endif
                                </td>
                                <td>
                                    @if ($ketuasidang == '--')
                                        {{"-"}}
                                    @else
                                        {{$ketuasidang}}
                                    @endif
                                </td>
                                <td>
                                    @if ($penguji1 == '--' && $penguji2 == '--' && $penguji3 == '--' && $ketuasidang == '--')
                                        Silahkan Set Penguji dan Ketua Sidang
                                    @else
                                    <a href="{{ url("prodi/set_jadwalpermhs/$info->pendaftaran_id/$d->C_NPM")}}"><i class="fa fa-file-archive-o icon-square icon-xs icon-dark"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">Belum ada data peserta pada jadwal ini.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div>
        </div>
    </div>
@endsection
