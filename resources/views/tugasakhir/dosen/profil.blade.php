@extends('tugasakhir.index')

@section('isi')
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Profil Saya <small>Dosen</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Profil Saya</li>
        </ol>

        @if (session('dosen_profile_success'))
            <div class="alert alert-success" role="alert"><strong>Berhasil! </strong>{{ session('dosen_profile_success') }}</div>
        @endif
        @if (session('dosen_profile_error'))
            <div class="alert alert-danger" role="alert"><strong>Gagal! </strong>{{ session('dosen_profile_error') }}</div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="the-box text-center">
                    <img src="{{ helper::dosenPhotoUrl($profil->D_FOTO_DOSEN ?? '') }}" class="img-circle"
                        alt="Foto dosen" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4>{{ $profil->NAMA_DOSEN ?? auth()->user()->name }}</h4>
                    <p class="text-muted">{{ $profil->C_KODE_DOSEN ?? auth()->user()->name }}</p>
                </div>
            </div>
            <div class="col-md-8">
                <div class="the-box">
                    <form method="post" action="{{ url('/dsn/kelengkapan_profil') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="return_to" value="profil">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Kode Dosen / NIDN</label>
                                <input type="text" class="form-control" value="{{ $profil->C_KODE_DOSEN ?? auth()->user()->name }}" readonly>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Nama Dosen</label>
                                <input type="text" class="form-control" value="{{ $profil->NAMA_DOSEN ?? auth()->user()->name }}" readonly>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Program Studi</label>
                                <select class="form-control" name="C_KODE_PRODI" required>
                                    <option value="">- Pilih Program Studi -</option>
                                    <option value="55201" {{ old('C_KODE_PRODI', $profil->C_KODE_PRODI ?? '') == '55201' ? 'selected' : '' }}>Teknik Informatika</option>
                                    <option value="57201" {{ old('C_KODE_PRODI', $profil->C_KODE_PRODI ?? '') == '57201' ? 'selected' : '' }}>Sistem Informasi</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jenis Kelamin</label>
                                <select class="form-control" name="JENIS_KELAMIN" required>
                                    <option value="">- Pilih Jenis Kelamin -</option>
                                    <option value="Pria" {{ old('JENIS_KELAMIN', $profil->JENIS_KELAMIN ?? '') == 'Pria' ? 'selected' : '' }}>Pria</option>
                                    <option value="Wanita" {{ old('JENIS_KELAMIN', $profil->JENIS_KELAMIN ?? '') == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>No. HP / WhatsApp</label>
                                <input type="text" class="form-control" name="NO_HP" value="{{ old('NO_HP', $profil->NO_HP ?? '') }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="EMAIL" value="{{ old('EMAIL', $profil->EMAIL ?? auth()->user()->email) }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Pangkat</label>
                                <input type="text" class="form-control" name="pangkat" value="{{ old('pangkat', $profil->website ?? '') }}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jabatan Fungsional</label>
                                <select class="form-control" name="jabatan_fungsional" required>
                                    <option value="">- Pilih Jabatan Fungsional -</option>
                                    @foreach (['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'] as $jabatan)
                                        <option value="{{ $jabatan }}" {{ old('jabatan_fungsional', $profil->jabatan_fungsional ?? '') == $jabatan ? 'selected' : '' }}>{{ $jabatan }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Ganti Foto Profil</label>
                            <input type="file" class="form-control" name="foto_dosen" accept="image/jpeg,image/png,image/webp">
                            <small class="text-muted">Kosongkan bila foto tidak diubah. Format JPEG, PNG, atau WebP, maksimal 2 MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
