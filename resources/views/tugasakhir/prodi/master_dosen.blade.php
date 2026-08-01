@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading">Sistem Informasi Program Studi <small>Tugas Akhir</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="#fakelink">Master</a></li>
                <li class="active">Dosen</li>
            </ol>

            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    <strong>Berhasil! </strong>{{ session('success') }}
                </div>
            @endif

            @if (session('danger'))
                <div class="alert alert-danger" role="alert">
                    <strong>Gagal! </strong>{{ session('danger') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <strong>Gagal! </strong>{{ $errors->first() }}
                </div>
            @endif

            <div class="alert alert-info" role="alert">
                Data dosen di menu ini akan disimpan ke master dosen utama dan master dosen migrasi agar tetap sinkron untuk kebutuhan pembimbingan, pengujian, dan penilaian.
            </div>

            <h3 class="page-heading">{{ $editData ? 'Ubah Data Dosen' : 'Form Dosen' }}</h3>
            <div class="the-box">
                <form method="post"
                    action="{{ $editData ? url('prodi/master/dosen/update/' . $editData->C_KODE_DOSEN) : url('prodi/master/dosen') }}"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Kode Dosen / NIDN</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="C_KODE_DOSEN"
                                    value="{{ old('C_KODE_DOSEN', $editData->C_KODE_DOSEN ?? '') }}" />
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">NIP</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="C_NIP"
                                    value="{{ old('C_NIP', $editData->C_NIP ?? '') }}" />
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Nama Dosen</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="NAMA_DOSEN"
                                    value="{{ old('NAMA_DOSEN', $editData->NAMA_DOSEN ?? '') }}" />
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Program Studi</label>
                            <div class="col-lg-5">
                                <select class="form-control bold-border" name="C_KODE_PRODI">
                                    <option value="">- Pilih Program Studi -</option>
                                    @foreach ($prodiList as $prodi)
                                        @php
                                            $selectedProdi = old(
                                                'C_KODE_PRODI',
                                                $editData->C_KODE_PRODI ?? ($currentProdi->kode_prodi ?? ''),
                                            );
                                        @endphp
                                        <option value="{{ $prodi->kode_prodi }}"
                                            {{ $selectedProdi == $prodi->kode_prodi ? 'selected' : '' }}>
                                            {{ $prodi->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Jenis Kelamin</label>
                            <div class="col-lg-5">
                                <select class="form-control bold-border" name="JENIS_KELAMIN">
                                    <option value="">- Pilih Jenis Kelamin -</option>
                                    <option value="Pria"
                                        {{ old('JENIS_KELAMIN', $editData->JENIS_KELAMIN ?? '') == 'Pria' ? 'selected' : '' }}>
                                        Pria
                                    </option>
                                    <option value="Wanita"
                                        {{ old('JENIS_KELAMIN', $editData->JENIS_KELAMIN ?? '') == 'Wanita' ? 'selected' : '' }}>
                                        Wanita
                                    </option>
                                </select>
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">No. HP</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="NO_HP"
                                    value="{{ old('NO_HP', $editData->NO_HP ?? '') }}" />
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Pangkat</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="pangkat"
                                    value="{{ old('pangkat', $editData->website ?? '') }}" />
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Email</label>
                            <div class="col-lg-5">
                                <input type="email" class="form-control bold-border" name="EMAIL"
                                    value="{{ old('EMAIL', $editData->EMAIL ?? '') }}" />
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Jabatan Fungsional</label>
                            <div class="col-lg-5">
                                <select class="form-control bold-border" name="jabatan_fungsional">
                                    <option value="">- Pilih Jabatan Fungsional -</option>
                                    @foreach (['Asisten Ahli', 'Lektor', 'Lektor Kepala', 'Guru Besar'] as $jabatan)
                                        <option value="{{ $jabatan }}"
                                            {{ old('jabatan_fungsional', $editData->jabatan_fungsional ?? '') == $jabatan ? 'selected' : '' }}>
                                            {{ $jabatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Status Aktif</label>
                            <div class="col-lg-5">
                                @php
                                    $statusAktif = old('F_AKTIF', isset($editData) ? (string) ($editData->F_AKTIF ?? 1) : '1');
                                @endphp
                                <select class="form-control bold-border" name="F_AKTIF">
                                    <option value="1" {{ $statusAktif === '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ $statusAktif === '0' ? 'selected' : '' }}>Non Aktif</option>
                                </select>
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Alamat</label>
                            <div class="col-lg-5">
                                <textarea class="form-control bold-border" name="ALAMAT" rows="3">{{ old('ALAMAT', $editData->ALAMAT ?? '') }}</textarea>
                            </div>
                        </div>

                        <br><br><br><br>

                        <div class="form-group">
                            <div class="col-xs-7" align="right">
                                @if ($editData)
                                    <a href="{{ url('prodi/master/dosen') }}" class="btn btn-default">Batal</a>
                                @endif
                                <button class="btn btn-primary btn-perspective" type="button"
                                    onclick="showPostModal(this)"
                                    data-formaction="{{ $editData ? url('prodi/master/dosen/update/' . $editData->C_KODE_DOSEN) : url('prodi/master/dosen') }}"
                                    data-target="#modalPrimary" data-toggle="modal">
                                    {{ $editData ? 'Update' : 'Simpan' }}
                                </button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>

            <h3 class="page-heading">Daftar Dosen</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Kode Dosen</th>
                                <th>Nama</th>
                                <th>Program Studi</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Pangkat</th>
                                <th>Jabatan Fungsional</th>
                                <th>Status Aktif</th>
                                <th>Sinkronisasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $key => $value)
                                <tr class="odd gradeX">
                                    <td width="1%" align="center">{{ ++$key }}</td>
                                    <td>{{ $value->C_KODE_DOSEN }}</td>
                                    <td>{{ $value->NAMA_DOSEN }}</td>
                                    <td>{{ $value->nama_prodi }}</td>
                                    <td>{{ $value->EMAIL }}</td>
                                    <td>{{ $value->NO_HP }}</td>
                                    <td>{{ $value->website }}</td>
                                    <td>{{ $value->jabatan_fungsional }}</td>
                                    <td>{{ $value->status_aktif_label }}</td>
                                    <td>
                                        @if ($value->exists_t_mst_dosen && $value->exists_mig_t_mst_dosen)
                                            <span class="label label-success">{{ $value->status_sinkron }}</span>
                                        @else
                                            <span class="label label-warning">{{ $value->status_sinkron }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('prodi/master/dosen/edit/' . $value->C_KODE_DOSEN) }}"
                                            class="btn btn-primary">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center">Belum ada data dosen.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("modalPrimaryTitle")
    {{ $editData ? 'Update Dosen' : 'Tambah Dosen' }}
@endsection
@section("modalPrimaryBody")
    {{ $editData ? 'Apakah Anda yakin ingin memperbarui data dosen?' : 'Apakah Anda yakin ingin menambah data dosen?' }}
@endsection
@section("modalPrimaryFooter")
    <button onclick="submit(this)" class="btn btn-default">{{ $editData ? 'Update' : 'Simpan' }}</button>
@endsection

@section("script")
    <script>
        let modal, modalId, modalFooter, form, formaction;
        const showPostModal = e => {
            formaction = e.getAttribute("data-formaction");
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
        };

        const submit = () => {
            form = document.querySelector(`form[action="${formaction}"]`);
            form.submit();
        }
    </script>
@endsection
