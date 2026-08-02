@extends('tugasakhir.index')
@section('isi')

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Profil Saya <small>Mahasiswa</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Profil Saya</li>
        </ol>

        @if (session('mhs_contact_success'))
            <div class="alert alert-success" role="alert"><strong>Berhasil! </strong>{{ session('mhs_contact_success') }}</div>
        @endif
        @if (session('mhs_contact_error'))
            <div class="alert alert-danger" role="alert"><strong>Gagal! </strong>{{ session('mhs_contact_error') }}</div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="the-box text-center">
                    <img src="{{ helper::mahasiswaPhotoUrl($profil->D_FOTO_MAHASISWA ?? '') }}" class="img-circle" alt="Foto mahasiswa" style="width: 150px; height: 150px; object-fit: cover;">
                    <h4>{{ $profil->NAMA_MAHASISWA ?? '-' }}</h4>
                    <p class="text-muted">{{ $profil->C_NPM ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-8">
                <div class="the-box">
                    <form method="post" action="{{ url('/mhs/kelengkapan_kontak') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="return_to" value="profil">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>NIM</label>
                                <input type="text" class="form-control" value="{{ $profil->C_NPM ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Program Studi</label>
                                <input type="text" class="form-control" value="{{ $profil->nama_prodi ?? '-' }}" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Nama Mahasiswa</label>
                            <input type="text" class="form-control" value="{{ $profil->NAMA_MAHASISWA ?? '-' }}" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nomor WhatsApp</label>
                            <input type="text" class="form-control" name="no_wa" value="{{ old('no_wa', $profil->no_wa ?? '') }}" placeholder="Contoh: 6281234567890" required>
                            <small class="text-muted">Gunakan format 0812..., 62812..., atau +62812.... Sistem menyimpan dalam format 62....</small>
                        </div>
                        <div class="form-group">
                            <label>ID Telegram</label>
                            <input type="text" class="form-control" name="id_telegram" value="{{ old('id_telegram', $profil->id_telegram ?? '') }}" placeholder="Contoh: @username_telegram">
                        </div>
                        <div class="form-group">
                            <label>Ganti Foto</label>
                            <input type="file" class="form-control" name="foto" accept="image/jpeg,image/png">
                            <small class="text-muted">Kosongkan bila foto tidak diubah. Format JPEG atau PNG, maksimal 5 MB.</small>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
