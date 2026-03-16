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
            <h3 class="page-heading">Daftar Peserta Ujian</h3>
            <div class="the-box">
                <div class="form-group">
                    <label class="col-lg-2 control-label">Jadwal Ujian</label>
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
                <br><br>
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
                        @php
                            $path = "";
                            if (Auth::user()->level==6) {
                                $path = "akademikprodi";
                            }else if(Auth::user()->level == 4){
                                $path = "fakultas";
                            }else {
                                $path = "prodi";
                            }
                        @endphp
                        @foreach($data as $i => $d)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$i}}</td>
                                <td>{{$d->C_NPM}}</td>
                                <td>{{$d->NAMA_MAHASISWA}}</td>
                                @php
                                    $pembimbing1 = \App\Dosen::where("C_KODE_DOSEN",$d->pembimbing_I_id)->first();
                                    $pembimbing2 = \App\Dosen::where("C_KODE_DOSEN",$d->pembimbing_II_id)->first();
                                    $penguji1 = \App\Dosen::where("C_KODE_DOSEN",$d->penguji_I_id)->first();
                                    $penguji2 = \App\Dosen::where("C_KODE_DOSEN",$d->penguji_II_id)->first();
                                    $penguji3 = \App\Dosen::where("C_KODE_DOSEN",$d->penguji_III_id)->first();
                                    $ketuasidang = \App\Dosen::where("C_KODE_DOSEN",$d->ketua_sidang_id)->first();
                                @endphp
                                <td>{{$pembimbing1->NAMA_DOSEN}}</td>
                                <td>{{$pembimbing2->NAMA_DOSEN}}</td>
                                <td>
                                    @if ($penguji1 == null)
                                        {{"-"}}
                                    @else
                                        {{$penguji1->NAMA_DOSEN}}
                                    @endif
                                </td>
                                <td>
                                    @if ($penguji2 == null)
                                        {{"-"}}
                                    @else
                                        {{$penguji2->NAMA_DOSEN}}
                                    @endif
                                </td>
                                <td>
                                    @if ($penguji3 == null)
                                        {{"-"}}
                                    @else
                                        {{$penguji3->NAMA_DOSEN}}
                                    @endif
                                </td>
                                <td>
                                    @if ($ketuasidang == null)
                                        {{"-"}}
                                    @else
                                        {{$ketuasidang->NAMA_DOSEN}}
                                    @endif
                                </td>

                                <td style="width: 240px; text-align: center">
                                    @if ($penguji1 == null && $penguji2 == null && $penguji3 == null && $ketuasidang == null)
                                        SK Belum Terbit, Silahkan Set Penguji dan Ketua Sidang
                                    @else
                                    <a target="_blank"  href="{{ url("$path/cetakskpenguji/$info->pendaftaran_id/$d->C_NPM")}}" class="btn btn-primary"><i class="fa fa-file-text"></i>SK Penguji</a>
                                    <a target="_blank" href="{{ url("$path/cetak_berita_acara/$info->pendaftaran_id/$d->C_NPM")}}" class="btn btn-info"><i class="fa fa-file-text"></i>Berita Acara</a>
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


