@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
@php
$data_count_nilai = 0;
if (helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_I_id, $data[0]->reg_id) != 0) {
$data_count_nilai = $data_count_nilai+ 1;
}

if (helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_II_id, $data[0]->reg_id) != 0) {
$data_count_nilai = $data_count_nilai+ 1;
}

if (helper::getTotalNilaByNidnAndRegId($data[0]->penguji_I_id, $data[0]->reg_id) != 0) {
$data_count_nilai = $data_count_nilai+ 1;
}

if (helper::getTotalNilaByNidnAndRegId($data[0]->penguji_II_id, $data[0]->reg_id) != 0) {
$data_count_nilai = $data_count_nilai+ 1;
}

if (helper::getTotalNilaByNidnAndRegId($data[0]->penguji_III_id, $data[0]->reg_id) != 0) {
$data_count_nilai = $data_count_nilai+ 1;
}

if (helper::getTotalNilaByNidnAndRegId($data[0]->ketua_sidang_id, $data[0]->reg_id) != 0) {
$data_count_nilai = $data_count_nilai+ 1;
}


@endphp
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading">Sistem Informasi Program Studi <small>Tugas Akhir</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Home</a></li>
            <li><a href="#fakelink">Riwayat Ujian</a></li>
            <li class="active">Detail Ujian</li>
        </ol>


        <h3 class="page-heading">Detail Ujian</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <fieldset>
                <div class="form-group">
                    <label class="col-lg-2 control-label">NIM</label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control bold-border" name="nim" disabled
                            value="{{$data[0]->C_NPM}}" />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Nama</label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control bold-border" name="nama" disabled
                            value="{{$data[0]->NAMA_MAHASISWA}}" />
                    </div>
                </div>
                <br><br>
                @php
                $nama_tipe_ujian = "";
                if ($data[0]->tipe_ujian == 0) {
                $nama_tipe_ujian = "Proposal";
                }else if($data[0]->tipe_ujian == 2){
                $nama_tipe_ujian = "Ujian Meja";
                }
                @endphp
                <div class="form-group">
                    <label class="col-lg-2 control-label">Tipe Ujian</label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control bold-border" name="tipe_ujian" disabled
                            value="{{$nama_tipe_ujian}}" />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Nilai Ujian</label>
                    <div class="col-lg-6">
                        @if ($data_count_nilai == 0)
                        <input type="text" class="form-control bold-border" name="nilai_ujian" disabled value="0" />
                        @else
                        <input type="text" class="form-control bold-border" name="nilai_ujian" disabled
                            value="{{((helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_I_id, $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_II_id, $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->penguji_I_id, $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->penguji_II_id, $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->penguji_III_id, $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->ketua_sidang_id, $data[0]->reg_id))/$data_count_nilai)}}" />
                        @endif
                    </div>
                </div>

                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Index Nilai</label>
                    <div class="col-lg-6">
                        @php
                        if ($data_count_nilai == 0) {
                            $nilai_final = 0;
                        } else {
                        $nilai_final = ((helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_I_id,
                        $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_II_id,
                        $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->penguji_I_id,
                        $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->penguji_II_id,
                        $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->penguji_III_id,
                        $data[0]->reg_id)+helper::getTotalNilaByNidnAndRegId($data[0]->ketua_sidang_id,
                        $data[0]->reg_id))/$data_count_nilai);
                        }
                        
                        $index_nilai = "";
                        @endphp
                        @if ($nilai_final > 85)
                        @php
                        $index_nilai = "A";
                        @endphp
                        @elseif($nilai_final >= 81 && $nilai_final <= 85) @php $index_nilai="A-" ; @endphp
                            @elseif($nilai_final>= 76 && $nilai_final <= 80) @php $index_nilai="B+" ; @endphp
                                @elseif($nilai_final>= 71 && $nilai_final
                                <= 75) @php $index_nilai="B" ; @endphp @endif <input type="text"
                                    class="form-control bold-border" name="tipe_ujian" disabled
                                    value="{{$index_nilai}}" />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Keterangan</label>
                    <div class="col-lg-6">
<div class="row">

<div class="col-md-12">
            <table border="1" width="100%" cellpadding="1" cellspacing="0">
            <tr align="center">
                <th width="100px">Nilai Angka</th>
                <th width="100px">Nilai Mutu</th>
                <th width="120px">Nilai Konversi</th>
            </tr>
            <tr align="center">
                <td> > 85</td>
                <td>A</td>
                <td>4.00</td>
            </tr>
            <tr align="center">
                <td>81 - 85</td>
                <td>A-</td>
                <td>3.75</td>
            </tr>
            <tr align="center">
                <td>76 - 80</td>
                <td>B+</td>
                <td>3.50</td>
            </tr>
            <tr align="center">
                <td>71 - 75</td>
                <td>B</td>
                <td>3.00</td>
            </tr>
        </table>
            <span style="font-size: 12px"><i>Sumber: Peraturan No. 1 Tahun 2014 UMI Tentang Ketentuan Pokok Akademik Pasal 43 Predikat Kelulusan</i></span>
</div>

    </div>
                    </div>
                </div>

                <br><br>
                <br><br>
                <br><br>
                <br><br>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Detail Bimbingan</label>
                </div>
                <br>
                @php
                $pembimbing1 = \App\Dosen::where("C_KODE_DOSEN",$data[0]->pembimbing_I_id)->first();
                $pembimbing2 = \App\Dosen::where("C_KODE_DOSEN",$data[0]->pembimbing_II_id)->first();
                $penguji1 = \App\Dosen::where("C_KODE_DOSEN",$data[0]->penguji_I_id)->first();
                $penguji2 = \App\Dosen::where("C_KODE_DOSEN",$data[0]->penguji_II_id)->first();
                $penguji3 = \App\Dosen::where("C_KODE_DOSEN",$data[0]->penguji_III_id)->first();
                $ketuasidang = \App\Dosen::where("C_KODE_DOSEN",$data[0]->ketua_sidang_id)->first();
                @endphp
                <div class="form-group">
                    <div class="table-responsive col-lg-8">
                        <table class="table table-th-block">
                            <thead>
                                <tr>
                                    <th width="1%">No</th>
                                    <th width="10%">Peserta Ujian</th>
                                    <th>Nama</th>
                                    <th>Saran & Tanggapan</th>
                                    @if (Auth::user()->level == 7 || Auth::user()->level == 6 || Auth::user()->level == 4)
                                        <th>Total Nilai</th>
                                        <th>Lembar Penilaian</th>
                                    @else
                                        
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    @if ($data[0]->tipe_ujian == 0)
                                    <td>Pembimbig Utama & Ketua Sidang</td>
                                    @else
                                    <td>Ketua Sidang</td>
                                    @endif

                                    <td>
                                        @if ($ketuasidang == null)
                                        {{"-"}}
                                        @else
                                        {{$ketuasidang->NAMA_DOSEN}}
                                        @endif
                                    </td>
                                    <td>{{helper::getSaranByNidnAndRegId($data[0]->ketua_sidang_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (Auth::user()->level == 7 || Auth::user()->level == 6 || Auth::user()->level == 4)
                                        <td>{{helper::getTotalNilaByNidnAndRegId($data[0]->ketua_sidang_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (helper::getTotalNilaByNidnAndRegId($data[0]->ketua_sidang_id, $data[0]->reg_id)
                                    == 0)
                                    <td><a href="#" class="btn btn-info disabled"><i class="fa fa-file-text"></i>Belum
                                            Ada Penilaian</a></td>
                                    @else

                                    @if ($data[0]->tipe_ujian == 0)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_proposal/")}}/{{$data[0]->ketua_sidang_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/1"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @elseif($data[0]->tipe_ujian == 2)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_ujianmeja/")}}/{{$data[0]->ketua_sidang_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/1"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @endif
                                    @endif
                                    @else
                                        
                                    @endif
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Penguji I</td>
                                    <td>
                                        @if ($penguji1 == null)
                                        {{"-"}}
                                        @else
                                        {{$penguji1->NAMA_DOSEN}}
                                        @endif
                                    </td>
                                    <td>{{helper::getSaranByNidnAndRegId($data[0]->penguji_I_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (Auth::user()->level == 7 || Auth::user()->level == 6 || Auth::user()->level == 4)
                                        <td>{{helper::getTotalNilaByNidnAndRegId($data[0]->penguji_I_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (helper::getTotalNilaByNidnAndRegId($data[0]->penguji_I_id, $data[0]->reg_id) ==
                                    0)
                                    <td><a href="#" class="btn btn-info disabled"><i class="fa fa-file-text"></i>Belum
                                            Ada Penilaian</a></td>
                                    @else

                                    @if ($data[0]->tipe_ujian == 0)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_proposal/")}}/{{$data[0]->penguji_I_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/2"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @elseif($data[0]->tipe_ujian == 2)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_ujianmeja/")}}/{{$data[0]->penguji_I_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/2"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @endif
                                    @endif
                                    @else
                                        
                                    @endif

                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Penguji II</td>
                                    <td>
                                        @if ($penguji2 == null)
                                        {{"-"}}
                                        @else
                                        {{$penguji2->NAMA_DOSEN}}
                                        @endif
                                    </td>
                                    <td>{{helper::getSaranByNidnAndRegId($data[0]->penguji_II_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (Auth::user()->level == 7 || Auth::user()->level == 6 || Auth::user()->level == 4)
                                        <td>{{helper::getTotalNilaByNidnAndRegId($data[0]->penguji_II_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (helper::getTotalNilaByNidnAndRegId($data[0]->penguji_II_id, $data[0]->reg_id)
                                    == 0)
                                    <td><a href="#" class="btn btn-info disabled"><i class="fa fa-file-text"></i>Belum
                                            Ada Penilaian</a></td>
                                    @else

                                    @if ($data[0]->tipe_ujian == 0)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_proposal/")}}/{{$data[0]->penguji_II_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/3"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @elseif($data[0]->tipe_ujian == 2)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_ujianmeja/")}}/{{$data[0]->penguji_II_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/3"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @endif
                                    @endif
                                    @else
                                        
                                    @endif

                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Penguji III</td>
                                    <td>
                                        @if ($penguji3 == null)
                                        {{"-"}}
                                        @else
                                        {{$penguji3->NAMA_DOSEN}}
                                        @endif
                                    </td>
                                    <td>{{helper::getSaranByNidnAndRegId($data[0]->penguji_III_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (Auth::user()->level == 7 || Auth::user()->level == 6 || Auth::user()->level == 4)
                                        <td>{{helper::getTotalNilaByNidnAndRegId($data[0]->penguji_III_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (helper::getTotalNilaByNidnAndRegId($data[0]->penguji_III_id, $data[0]->reg_id)
                                    == 0)
                                    <td><a href="#" class="btn btn-info disabled"><i class="fa fa-file-text"></i>Belum
                                            Ada Penilaian</a></td>
                                    @else
                                    @if ($data[0]->tipe_ujian == 0)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_proposal/")}}/{{$data[0]->penguji_III_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/4"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @elseif($data[0]->tipe_ujian == 2)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_ujianmeja/")}}/{{$data[0]->penguji_III_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/4"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @endif
                                    @endif
                                    @else
                                        
                                    @endif

                                </tr>
                                @if ($data[0]->tipe_ujian == 0)
                                @else
                                <tr>
                                    <td>5</td>
                                    <td>Pembimbing Utama</td>
                                    <td>
                                        @if ($pembimbing1 == null)
                                        {{"-"}}
                                        @else
                                        {{$pembimbing1->NAMA_DOSEN}}
                                        @endif
                                    </td>
                                    <td>{{helper::getSaranByNidnAndRegId($data[0]->pembimbing_I_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (Auth::user()->level == 7 || Auth::user()->level == 6 || Auth::user()->level == 4)
                                        <td>{{helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_I_id, $data[0]->reg_id)}}
                                    </td>

                                    @if (helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_I_id, $data[0]->reg_id)
                                    == 0)
                                    <td><a href="#" class="btn btn-info disabled"><i class="fa fa-file-text"></i>Belum
                                            Ada Penilaian</a></td>
                                    @else

                                    @if ($data[0]->tipe_ujian == 0)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_proposal/")}}/{{$data[0]->pembimbing_I_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/5"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @elseif($data[0]->tipe_ujian == 2)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_ujianmeja/")}}/{{$data[0]->pembimbing_I_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/5"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @endif
                                    @endif
                                    @else
                                        
                                    @endif

                                </tr>
                                @endif
                                <tr>
                                    @if (Auth::user()->level == 8)
                                        <td>5</td>
                                    @else
                                        <td>6</td>
                                    @endif
                                    <td>Pembimbing Pendamping</td>
                                    <td>
                                        @if ($pembimbing2 == null)
                                        {{"-"}}
                                        @else
                                        {{$pembimbing2->NAMA_DOSEN}}
                                        @endif
                                    </td>
                                    <td>{{helper::getSaranByNidnAndRegId($data[0]->pembimbing_II_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (Auth::user()->level == 7 || Auth::user()->level == 6 || Auth::user()->level == 4)
                                        <td>{{helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_II_id, $data[0]->reg_id)}}
                                    </td>
                                    @if (helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_II_id,
                                    $data[0]->reg_id) == 0)
                                    <td><a href="#" class="btn btn-info disabled"><i class="fa fa-file-text"></i>Belum
                                            Ada Penilaian</a></td>
                                    @else

                                    @if ($data[0]->tipe_ujian == 0)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_proposal/")}}/{{$data[0]->pembimbing_II_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/6"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @elseif($data[0]->tipe_ujian == 2)
                                    <td><a target="_blank"
                                            href="{{ url("dsn/detailhasil_ujianmeja/")}}/{{$data[0]->pembimbing_II_id}}/{{$data[0]->reg_id}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/6"
                                            class="btn btn-info"><i class="fa fa-file-text"></i>Penilaian</a></td>
                                    @endif


                                    @endif
                                    @else
                                        
                                    @endif
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if (Auth::user()->level == 7 || Auth::user()->level == 6 || Auth::user()->level == 4)
                        @if ((helper::getTotalNilaByNidnAndRegId($data[0]->penguji_I_id, $data[0]->reg_id) == 0) ||
                    (helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_II_id, $data[0]->reg_id) == 0) ||
                    (helper::getTotalNilaByNidnAndRegId($data[0]->pembimbing_I_id, $data[0]->reg_id) == 0) ||
                    (helper::getTotalNilaByNidnAndRegId($data[0]->penguji_II_id, $data[0]->reg_id) == 0) ||
                    (helper::getTotalNilaByNidnAndRegId($data[0]->penguji_III_id, $data[0]->reg_id) == 0) ||
                    (helper::getTotalNilaByNidnAndRegId($data[0]->ketua_sidang_id, $data[0]->reg_id) == 0) )
                    <div class="form-group">
                        <div class="col-xs-8" align="right">
                            <a class="btn btn-info btn-perspective disabled" target="_blank">Belum Bisa Dicetak</a>
                        </div>
                    </div>
                    @else
                    <div class="form-group">
                        <div class="col-xs-8" align="right">
                            @if ($data[0]->tipe_ujian == 0)
                            <a class="btn btn-info btn-perspective"
                                href="{{url('dsn/lembaran_hasilujian_proposal/')}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/{{$data[0]->reg_id}}"
                                target="_blank">Cetak</a>
                            @elseif($data[0]->tipe_ujian == 2)
                            <a class="btn btn-info btn-perspective"
                                href="{{url('dsn/lembaran_hasilujian_ujian_ta/')}}/{{$data[0]->pendaftaran_id}}/{{$data[0]->C_NPM}}/{{$data[0]->reg_id}}"
                                target="_blank">Cetak</a>
                            @endif
                        </div>
                    </div>
                    @endif
                    @else
                        
                    @endif

                </div>
            </fieldset>
        </div>
    </div>

    @endsection