@extends('tugasakhir.index')
@section('isi')
<style>
    .peminatan-status-form { display: inline-block; margin: 0; }
    .peminatan-switch { position: relative; display: inline-block; width: 44px; height: 24px; margin: 0; vertical-align: middle; }
    .peminatan-switch input { opacity: 0; width: 0; height: 0; }
    .peminatan-slider { position: absolute; cursor: pointer; inset: 0; background: #aeb6bf; border-radius: 24px; transition: .2s; }
    .peminatan-slider:before { position: absolute; content: ""; width: 18px; height: 18px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: .2s; }
    .peminatan-switch input:checked + .peminatan-slider { background: #27ae60; }
    .peminatan-switch input:checked + .peminatan-slider:before { transform: translateX(20px); }
    .peminatan-switch input:focus + .peminatan-slider { box-shadow: 0 0 0 2px rgba(39, 174, 96, .2); }
</style>
<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Master</a></li>
            <li class="active">Bidang Ilmu Peminatan</li>
        </ol>

        @if (session('success'))
            <div class="alert alert-success" role="alert"><strong>Berhasil! </strong>{{ session('success') }}</div>
        @endif
        @if (session('danger'))
            <div class="alert alert-danger" role="alert"><strong>Gagal! </strong>{{ session('danger') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><strong>Gagal! </strong>{{ $errors->first() }}</div>
        @endif

        <h3 class="page-heading">Tambah Bidang Ilmu Peminatan</h3>
        <div class="the-box">
            <form id="formTambahPeminatan" method="post" action="{{ url('prodi/master/bidang_ilmu_peminatan') }}">
                {{ csrf_field() }}
                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Program Studi</label>
                        <div class="col-lg-5">
                            @if ($isAdmin)
                                <select class="form-control bold-border" name="kode_prodi" required>
                                    <option value="">Pilih program studi</option>
                                    <option value="130" {{ old('kode_prodi') === '130' ? 'selected' : '' }}>Teknik Informatika</option>
                                    <option value="131" {{ old('kode_prodi') === '131' ? 'selected' : '' }}>Sistem Informasi</option>
                                </select>
                            @else
                                <input type="text" class="form-control bold-border" value="{{ $scope['label'] }}" readonly>
                            @endif
                        </div>
                    </div>
                    <br><br>

                    <div class="form-group">
                        <label class="col-lg-2 control-label">Nama Peminatan</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="nama_peminatan"
                                maxlength="150" value="{{ old('nama_peminatan') }}" required>
                        </div>
                    </div>
                    <br><br>

                    <div class="form-group">
                        <label class="col-lg-2 control-label">Status</label>
                        <div class="col-lg-5">
                            <select class="form-control bold-border" name="status_aktif">
                                <option value="1" {{ old('status_aktif', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status_aktif') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <br><br>

                    <div class="form-group">
                        <div class="col-xs-7" align="right">
                            <button class="btn btn-primary btn-perspective" type="button"
                                data-target="#modalPrimary" data-toggle="modal">
                                <i class="fa fa-save"></i> Simpan
                            </button>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>

        <h3 class="page-heading">Daftar Bidang Ilmu Peminatan</h3>
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="the-box dark full">
                        <tr>
                            <th style="width: 55px;">No</th>
                            <th>Program Studi</th>
                            <th>Nama Peminatan</th>
                            <th style="width: 110px;">Digunakan</th>
                            <th style="width: 110px;">Status</th>
                            <th style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $peminatan)
                            <tr>
                                <td align="center">{{ $key + 1 }}</td>
                                <td>
                                    <span class="label {{ $peminatan->kode_prodi === '130' ? 'label-primary' : 'label-info' }}">
                                        {{ $peminatan->kode_prodi === '130' ? 'TI' : 'SI' }}
                                    </span>
                                    {{ $peminatan->program_studi }}
                                </td>
                                <td>{{ $peminatan->nama_peminatan }}</td>
                                <td>{{ number_format($peminatan->jumlah_penggunaan, 0, ',', '.') }} usulan</td>
                                <td>
                                    <form method="post" action="{{ url('prodi/master/bidang_ilmu_peminatan/' . $peminatan->bidang_ilmu_peminatan_id . '/availability') }}" class="peminatan-status-form">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="status_aktif" value="{{ (int) $peminatan->status_aktif === 1 ? 0 : 1 }}">
                                        <label class="peminatan-switch" title="{{ (int) $peminatan->status_aktif === 1 ? 'Aktif' : 'Tidak Aktif' }}">
                                            <input type="checkbox" {{ (int) $peminatan->status_aktif === 1 ? 'checked' : '' }} onchange="this.form.submit()">
                                            <span class="peminatan-slider" aria-hidden="true"></span>
                                        </label>
                                    </form>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning" title="Edit"
                                        data-toggle="modal" data-target="#modalEditPeminatan"
                                        data-id="{{ $peminatan->bidang_ilmu_peminatan_id }}"
                                        data-prodi="{{ $peminatan->program_studi }}"
                                        data-nama="{{ $peminatan->nama_peminatan }}"
                                        data-status="{{ $peminatan->status_aktif }}"
                                        onclick="showEditPeminatan(this)">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger" title="Hapus"
                                        {{ $peminatan->jumlah_penggunaan > 0 ? 'disabled' : '' }}
                                        data-toggle="modal" data-target="#modalDeletePeminatan"
                                        data-id="{{ $peminatan->bidang_ilmu_peminatan_id }}"
                                        data-nama="{{ $peminatan->nama_peminatan }}"
                                        onclick="showDeletePeminatan(this)">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">Belum ada bidang ilmu peminatan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalEditPeminatan" tabindex="-1" role="dialog" aria-labelledby="modalEditPeminatanLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="formEditPeminatan" method="post">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modalEditPeminatanLabel">Edit Bidang Ilmu Peminatan</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Program Studi</label>
                                <input type="text" id="edit_program_studi" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label for="edit_nama_peminatan">Nama Peminatan</label>
                                <input type="text" id="edit_nama_peminatan" name="nama_peminatan" class="form-control" maxlength="150" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_status_aktif">Status</label>
                                <select id="edit_status_aktif" name="status_aktif" class="form-control">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalDeletePeminatan" tabindex="-1" role="dialog" aria-labelledby="modalDeletePeminatanLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="formDeletePeminatan" method="post">
                        {{ csrf_field() }}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="modalDeletePeminatanLabel">Hapus Bidang Ilmu Peminatan</h4>
                        </div>
                        <div class="modal-body">
                            <p>Hapus <strong id="delete_nama_peminatan"></strong>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger"><i class="fa fa-trash-o"></i> Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modalPrimaryTitle')
Tambah Bidang Ilmu Peminatan
@endsection
@section('modalPrimaryBody')
Simpan bidang ilmu peminatan baru?
@endsection
@section('modalPrimaryFooter')
<button type="button" class="btn btn-primary" onclick="document.getElementById('formTambahPeminatan').submit()">Simpan</button>
@endsection

@section('script')
<script>
    function showEditPeminatan(button) {
        document.getElementById('formEditPeminatan').action = `{{ url('prodi/master/bidang_ilmu_peminatan') }}/${button.dataset.id}/update`;
        document.getElementById('edit_program_studi').value = button.dataset.prodi || '';
        document.getElementById('edit_nama_peminatan').value = button.dataset.nama || '';
        document.getElementById('edit_status_aktif').value = button.dataset.status || '1';
    }

    function showDeletePeminatan(button) {
        document.getElementById('formDeletePeminatan').action = `{{ url('prodi/master/bidang_ilmu_peminatan') }}/${button.dataset.id}/delete`;
        document.getElementById('delete_nama_peminatan').textContent = button.dataset.nama || '';
    }
</script>
@endsection
