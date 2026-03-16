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
            <li><a href="{{ url('prodi/mahasiswa')}}">Mahasiswa</a></li>
            <li class="active">Detail Mahasiswa</li>
        </ol>


        <h3 class="page-heading">Detail Mahasiswa</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            @if(session()->has('success'))
            <div class="alert alert-success" role="alert">
                {{ session()->get('success') }}
            </div>
            @endif
            @if(session()->has('error'))
            <div class="alert alert-danger" role="alert">
                {{ session()->get('error') }}
            </div>
            @endif
            <fieldset>
                <div class="form-group">
                    <label class="col-lg-2 control-label">NIM</label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control bold-border" name="nim" value="{{$datax->C_NPM}}"
                            disabled />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Nama</label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control bold-border" name="nama"
                            value="{{$datax->NAMA_MAHASISWA}}" disabled />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Jenis Kelamin</label>
                    <div class="col-lg-5">
                        @if($datax->JENIS_KELAMIN == 'P')
                        <input type="text" class="form-control bold-border" name="jk" value="PEREMPUAN" disabled />
                        @else
                        <input type="text" class="form-control bold-border" name="jk" value="LAKI-LAKI" disabled />
                        @endif
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Alamat</label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control bold-border" name="alamat" value="{{$datax->ALAMAT}}"
                            disabled />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Status</label>
                    <div class="col-lg-5">
                        @if($datax->status == 0)
                        <input type="text" class="form-control bold-border" name="stat" value="Tidak Aktif" disabled />
                        @else
                        <input type="text" class="form-control bold-border" name="stat" value="Aktif" disabled />
                        @endif

                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">SKS Terprogram</label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control bold-border" name="jmlsks"
                            value="{{$datax->JML_SKS_DIAKUI}}" disabled />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">SKS Lulus</label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control bold-border" name="jmlBimbingan" disabled />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Semester Aktif</label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control bold-border" name="jmlBimbingan" disabled />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Semester Tidak Aktif</label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control bold-border" name="jmlBimbingan" disabled />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Total Semester</label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control bold-border" name="jmlBimbingan" disabled />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">IPK</label>
                    <div class="col-lg-5">
                        <input type="text" class="form-control bold-border" name="jmlBimbingan" disabled />
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Data Tugas Akhir</label>
                    <div class="table-responsive col-lg-7">
                        <table class="table table-th-block table-dark">
                            <tr>
                                <th>No SK Bimbingan</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th>Judul</th>
                                <td>{{isset($datax->pembimbing1->judul) ? $datax->pembimbing1->judul: ''}}
                                    @if (isset($datax->pembimbing1->judul))
                                    <a href="{{url('prodi/edit_judul_detail_mahasiswa')}}/{{$datax->C_NPM}}"
                                        style="margin-left: 20px" class="btn btn-primary btn-small">Ubah</a>
                                    @else

                                    @endif

                                </td>
                            </tr>
                            <tr>
                                <th>Tahapan Bimbingan</th>
                                <td>
                                    @if(isset($datax->pembimbing1->status_bimbingan))
                                    @if($datax->pembimbing1->status_bimbingan == 0)
                                    Persiapan Proposal
                                    @elseif ($datax->pembimbing1->status_bimbingan == 1)
                                    Persiapan Seminar Hasil
                                    @elseif ($datax->pembimbing1->status_bimbingan == 2)
                                    Persiapan Ujian Meja
                                    @elseif ($datax->pembimbing1->status_bimbingan == 3)
                                    Lulus
                                    @elseif ($datax->pembimbing1->status_bimbingan == 4)
                                    Non Aktif
                                    @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Pembimbing Utama</th>
                                <td>
                                    {{isset($datax->pembimbing1->pembimbing_I_id) ? helper::getDeskripsi($datax->pembimbing1->pembimbing_I_id) :'' }}
                                </td>
                            </tr>
                            <tr>
                                <th>Pembimbing Pendamping</th>
                                <td>
                                    {{isset($datax->pembimbing1->pembimbing_II_id) ? helper::getDeskripsi($datax->pembimbing1->pembimbing_II_id) : '' }}
                                </td>
                            </tr>
                            <tr>
                                @if (isset($datax->pembimbing1->pembimbing_II_id) &&
                                isset($datax->pembimbing1->pembimbing_I_id))
                                <th>Aksi</th>
                                <td>
                                    <a href="{{url('prodi/ubah_pembimbing_per_mahasiswa')}}/{{$datax->C_NPM}}"
                                        class="btn btn-primary btn-small">Ubah Pembimbing</a>

                                </td>

                                @else

                                @endif

                            </tr>
                            <tr>
                                <th>Ubah Tahapan Bimbingan</th>
                                <td>
                                    @if (isset($datax->pembimbing1->bimbingan_id))
                                    <form method="get" action="{{url('prodi/update_status_bimbingan')}}" class="form-inline">
                                        <input type="hidden" name="nim" value="{{$datax->C_NPM}}">
                                        <input type="hidden" name="bimbingan_id" value="{{$datax->pembimbing1->bimbingan_id}}">
                                        <select name="status_bimbingan" class="form-control" required>
                                            <option value="0" {{ isset($datax->pembimbing1->status_bimbingan) && $datax->pembimbing1->status_bimbingan == 0 ? 'selected' : '' }}>Persiapan Proposal</option>
                                            <option value="2" {{ isset($datax->pembimbing1->status_bimbingan) && $datax->pembimbing1->status_bimbingan == 2 ? 'selected' : '' }}>Persiapan Ujian Meja</option>
                                            <option value="3" {{ isset($datax->pembimbing1->status_bimbingan) && $datax->pembimbing1->status_bimbingan == 3 ? 'selected' : '' }}>Lulus</option>
                                            <option value="4" {{ isset($datax->pembimbing1->status_bimbingan) && $datax->pembimbing1->status_bimbingan == 4 ? 'selected' : '' }}>Non Aktif</option>
                                        </select>
                                        <button type="submit" class="btn btn-warning btn-small" style="margin-left: 8px;">Simpan</button>
                                    </form>
                                    @else
                                    Data bimbingan belum tersedia.
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div><!-- /.table-responsive -->
                </div>
            </fieldset>
        </div><!-- /.the-box -->

        @endsection
