@extends('tugasakhir.index')
@section('isi')
<!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Ubah Judul dan Kerangka Pikir</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Form Ubah Judul</h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Perubahan belum tersimpan.</strong>
                    <ul style="margin-bottom: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{url('mhs/ubah_judul')}}/{{$data[0]->topik_id}}">
                {{ csrf_field() }}
                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Judul Topik</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="topik" value="{{$data[0]->topik}}"/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Bidang Ilmu Peminatan</label>
                        <div class="col-lg-5">
                            <select class="form-control bold-border" name="bidang_ilmu_peminatan_id" required>
                                <option value="">Pilih bidang ilmu peminatan</option>
                                @foreach ($bidangIlmuPeminatan as $peminatan)
                                <option value="{{ $peminatan->bidang_ilmu_peminatan_id }}" @if((string) old('bidang_ilmu_peminatan_id', $data[0]->bidang_ilmu_peminatan_id) === (string) $peminatan->bidang_ilmu_peminatan_id) selected @endif>
                                    {{ $peminatan->nama_peminatan }}{{ (int) $peminatan->status_aktif === 0 ? ' (Tidak Aktif)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Link Kerangka Pikir</label>
                        <div class="col-lg-5">
                            <input type="url" class="form-control bold-border" name="kerangka" maxlength="255"
                                value="{{ old('kerangka', helper::isGoogleDriveUrl($data[0]->kerangka ?? '') ? $data[0]->kerangka : '') }}"
                                placeholder="https://drive.google.com/file/d/.../view">
                            <small class="text-muted">Gunakan link file Google Drive/Docs, bukan folder. Jika lampiran lama masih digunakan, kolom ini boleh dibiarkan kosong.</small>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Jenis Tugas Akhir</label>
                        <div class="col-lg-5">
                            <select class="form-control bold-border" name="jenis_tugas_akhir_id" required>
                                @foreach ($jenisTugasAkhir as $jenis)
                                <option value="{{$jenis->jenis_tugas_akhir_id}}" @if((int) $data[0]->jenis_tugas_akhir_id === (int) $jenis->jenis_tugas_akhir_id) selected @endif>
                                    {{$jenis->kode_jenis_tugas_akhir}} - {{$jenis->deskripsi}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                            <label class="col-lg-2 control-label"></label>
                            <div class="col-lg-10 mb-5">
                                Terakhir Kali Diubah : <span class="badge badge-primary">{{$data[0]->updated_at}}</span>
                            </div>
                    </div>
                    <div class="form-group mt-2">
                        <div class="col-lg-12" align="right">
                            <button class="btn btn-primary btn-perspective" type="submit">Ubah</button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div><!-- /.the-box -->
    </div><!-- /.container-fluid -->
</div>
@endsection
