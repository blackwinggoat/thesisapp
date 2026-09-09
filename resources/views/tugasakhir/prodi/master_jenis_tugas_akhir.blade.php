@extends('tugasakhir.index')
@section('isi')
    <style>
        .jenis-ta-form-box {
            padding: 0;
            overflow: hidden;
            border-color: #dfe4ea;
        }

        .jenis-ta-form-header {
            padding: 16px 20px;
            border-bottom: 1px solid #e3e7eb;
            background: #f7f9fb;
        }

        .jenis-ta-form-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #263238;
        }

        .jenis-ta-form-body {
            padding: 20px 20px 4px;
        }

        .jenis-ta-form-body .form-group {
            margin-bottom: 20px;
        }

        .jenis-ta-form-body label {
            display: block;
            margin-bottom: 7px;
            color: #37474f;
            font-weight: 600;
        }

        .jenis-ta-form-body .form-control,
        .jenis-ta-form-body .input-group-addon {
            height: 42px;
        }

        .jenis-ta-form-body .input-group-addon {
            min-width: 58px;
            color: #52616b;
            background: #f7f9fb;
            border-color: #bfc8ce;
        }

        .jenis-ta-availability {
            display: flex;
            align-items: center;
            min-height: 42px;
        }

        .jenis-ta-switch {
            position: relative;
            display: inline-block !important;
            width: 48px;
            height: 26px;
            margin: 0 !important;
        }

        .jenis-ta-switch input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
        }

        .jenis-ta-switch-track {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            cursor: pointer;
            border: 1px solid #aeb8bf;
            border-radius: 13px;
            background: #c5ccd1;
            transition: background-color .2s ease, border-color .2s ease;
        }

        .jenis-ta-switch-track::before {
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            content: '';
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .22);
            transition: transform .2s ease;
        }

        .jenis-ta-switch input:checked + .jenis-ta-switch-track {
            border-color: #218c55;
            background: #249a5a;
        }

        .jenis-ta-switch input:checked + .jenis-ta-switch-track::before {
            transform: translateX(22px);
        }

        .jenis-ta-switch input:focus + .jenis-ta-switch-track {
            box-shadow: 0 0 0 3px rgba(36, 154, 90, .2);
        }

        .jenis-ta-availability-label {
            margin-left: 10px;
            color: #37474f;
            font-weight: 600;
        }

        .jenis-ta-form-actions {
            display: flex;
            justify-content: flex-end;
            padding: 14px 20px;
            border-top: 1px solid #e3e7eb;
            background: #fafbfc;
        }

        .jenis-ta-form-actions .btn {
            min-width: 112px;
        }

        @media (max-width: 767px) {
            .jenis-ta-form-body {
                padding: 16px 15px 2px;
            }

            .jenis-ta-form-header,
            .jenis-ta-form-actions {
                padding-right: 15px;
                padding-left: 15px;
            }

            .jenis-ta-form-actions .btn {
                width: 100%;
            }
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="#fakelink">Master</a></li>
                <li class="active">Jenis Tugas Akhir</li>
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

            <div class="the-box jenis-ta-form-box">
                <div class="jenis-ta-form-header">
                    <h3>Form Jenis Tugas Akhir</h3>
                </div>
                <form id="formTambahJenisTugasAkhir" method="post" action="{{ url('prodi/master/jenis_tugas_akhir') }}">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="jenis-ta-form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="kode_jenis_tugas_akhir">Kode Jenis Tugas Akhir</label>
                                        <input type="text" id="kode_jenis_tugas_akhir" class="form-control bold-border"
                                            name="kode_jenis_tugas_akhir" value="{{ old('kode_jenis_tugas_akhir') }}"
                                            maxlength="50" placeholder="Contoh: NS-KT" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="deskripsi">Deskripsi</label>
                                        <input type="text" id="deskripsi" class="form-control bold-border" name="deskripsi"
                                            value="{{ old('deskripsi') }}" maxlength="255"
                                            placeholder="Nama lengkap jenis tugas akhir" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nilai_maksimal">Nilai Maksimal</label>
                                        <div class="input-group">
                                            <input type="number" id="nilai_maksimal" class="form-control bold-border"
                                                name="nilai_maksimal" value="{{ old('nilai_maksimal', 100) }}"
                                                min="1" max="100" step="0.01" required />
                                            <span class="input-group-addon">poin</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tersedia_untuk_mahasiswa">Ketersediaan bagi Mahasiswa</label>
                                        <div class="jenis-ta-availability">
                                            <input type="hidden" name="tersedia_untuk_mahasiswa" value="0">
                                            <label class="jenis-ta-switch" for="tersedia_untuk_mahasiswa"
                                                title="Atur ketersediaan pada pilihan mahasiswa">
                                                <input type="checkbox" id="tersedia_untuk_mahasiswa"
                                                    name="tersedia_untuk_mahasiswa" value="1"
                                                    {{ (string) old('tersedia_untuk_mahasiswa', '1') === '1' ? 'checked' : '' }}>
                                                <span class="jenis-ta-switch-track" aria-hidden="true"></span>
                                            </label>
                                            <span id="jenisTaAvailabilityLabel" class="jenis-ta-availability-label"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="jenis-ta-form-actions">
                            <button class="btn btn-primary btn-perspective" type="button"
                                onclick="showPostModal(this)"
                                data-formaction="{{ url('prodi/master/jenis_tugas_akhir') }}"
                                data-target="#modalPrimary">
                                <i class="fa fa-save" aria-hidden="true"></i> Simpan
                            </button>
                        </div>
                    </fieldset>
                </form>
            </div>

            <h3 class="page-heading">Daftar Jenis Tugas Akhir</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Kode Jenis Tugas Akhir</th>
                                <th>Deskripsi</th>
                                <th>Nilai Maksimal</th>
                                <th>Tersedia bagi Mahasiswa</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $key => $value)
                                <tr class="odd gradeX">
                                    <td width="1%" align="center">{{ ++$key }}</td>
                                    <td>{{ $value->kode_jenis_tugas_akhir }}</td>
                                    <td>{{ $value->deskripsi }}</td>
                                    <td>
                                        <strong>{{ rtrim(rtrim(number_format((float) ($value->nilai_maksimal ?? 100), 2, ',', '.'), '0'), ',') }}</strong>
                                    </td>
                                    <td>
                                        @if (($value->tersedia_untuk_mahasiswa ?? 1) == 1)
                                            <span class="label label-success">Tersedia</span>
                                        @else
                                            <span class="label label-default">Tidak Tersedia</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button
                                            class="btn btn-warning"
                                            type="button"
                                            title="Ubah jenis tugas akhir"
                                            data-toggle="modal"
                                            data-target="#modalEditJenisTugasAkhir"
                                            data-id="{{ $value->jenis_tugas_akhir_id }}"
                                            data-kode="{{ $value->kode_jenis_tugas_akhir }}"
                                            data-deskripsi="{{ $value->deskripsi }}"
                                            data-nilai-maksimal="{{ $value->nilai_maksimal ?? 100 }}"
                                            data-tersedia="{{ $value->tersedia_untuk_mahasiswa ?? 1 }}"
                                            onclick="showEditJenisTugasAkhir(this)">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @if ($hasMahasiswaAvailability)
                                            <form method="post" action="{{ url('prodi/master/jenis_tugas_akhir/' . $value->jenis_tugas_akhir_id . '/availability') }}" style="display: inline-block; margin-right: 6px;">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="tersedia_untuk_mahasiswa" value="{{ ($value->tersedia_untuk_mahasiswa ?? 1) == 1 ? 0 : 1 }}">
                                                <label title="{{ ($value->tersedia_untuk_mahasiswa ?? 1) == 1 ? 'Sembunyikan dari pilihan mahasiswa' : 'Tampilkan pada pilihan mahasiswa' }}" style="margin: 0; cursor: pointer;">
                                                    <input type="checkbox" {{ ($value->tersedia_untuk_mahasiswa ?? 1) == 1 ? 'checked' : '' }} onchange="this.form.submit()">
                                                </label>
                                            </form>
                                        @endif
                                        <button class="btn btn-danger" onclick="showModal(this)"
                                            data-target="#modalDanger" data-toggle="modal"
                                            data-href="{{ url('prodi/master/jenis_tugas_akhir/delete/' . $value->jenis_tugas_akhir_id) }}">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data jenis tugas akhir.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="modalEditJenisTugasAkhir" tabindex="-1" role="dialog" aria-labelledby="modalEditJenisTugasAkhirLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="formEditJenisTugasAkhir" method="post">
                            {{ csrf_field() }}
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title" id="modalEditJenisTugasAkhirLabel">Edit Jenis Tugas Akhir</h4>
                            </div>
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="edit_kode_jenis_tugas_akhir">Kode Jenis Tugas Akhir</label>
                                    <input type="text" id="edit_kode_jenis_tugas_akhir" name="kode_jenis_tugas_akhir" class="form-control" maxlength="50" required>
                                    <p class="help-block">Kode akan diperbarui pada label jenis tugas akhir yang telah memakai data master ini.</p>
                                </div>
                                <div class="form-group">
                                    <label for="edit_deskripsi">Deskripsi</label>
                                    <input type="text" id="edit_deskripsi" name="deskripsi" class="form-control" maxlength="255" required>
                                </div>
                                <div class="form-group">
                                    <label for="edit_nilai_maksimal">Nilai Maksimal</label>
                                    <input type="number" id="edit_nilai_maksimal" name="nilai_maksimal" class="form-control" min="1" max="100" step="0.01" required>
                                    <p class="help-block">Batas nilai akhir tertinggi untuk jenis tugas akhir ini.</p>
                                </div>
                                @if ($hasMahasiswaAvailability)
                                    <div class="form-group">
                                        <label for="edit_tersedia_untuk_mahasiswa">Tersedia bagi Mahasiswa</label>
                                        <select id="edit_tersedia_untuk_mahasiswa" name="tersedia_untuk_mahasiswa" class="form-control">
                                            <option value="1">Tersedia</option>
                                            <option value="0">Tidak Tersedia</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("modalPrimaryTitle")
    Tambah Jenis Tugas Akhir
@endsection
@section("modalPrimaryBody")
    Apakah Anda yakin ingin menambah jenis tugas akhir?
@endsection
@section("modalPrimaryFooter")
    <button onclick="submit(this)" class="btn btn-default">Tambah</button>
@endsection

@section("modalDangerTitle")
    Hapus Jenis Tugas Akhir
@endsection
@section("modalDangerBody")
    Apakah Anda yakin ingin menghapus data?
@endsection
@section("modalDangerFooter")
    <button onclick="goOn(this)" class="btn btn-default">Hapus</button>
@endsection

@section("script")
    <script>
        let modal, modalId, modalFooter, link, form, formaction;
        const showPostModal = e => {
            formaction = e.getAttribute("data-formaction");
            form = document.querySelector(`form[action="${formaction}"]`);
            if (form && !form.checkValidity()) {
                if (typeof form.reportValidity === 'function') {
                    form.reportValidity();
                }
                return;
            }

            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
            $(modalId).modal('show');
        };

        const showModal = e => {
            link = e.getAttribute("data-href");
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
        };

        const goOn = () => {
            window.location.href = link;
        };

        const submit = () => {
            form = document.querySelector(`form[action="${formaction}"]`);
            form.submit();
        };

        const showEditJenisTugasAkhir = button => {
            const id = button.getAttribute('data-id');
            const formEdit = document.getElementById('formEditJenisTugasAkhir');

            formEdit.setAttribute('action', `{{ url('prodi/master/jenis_tugas_akhir') }}/${id}/update`);
            document.getElementById('edit_kode_jenis_tugas_akhir').value = button.getAttribute('data-kode') || '';
            document.getElementById('edit_deskripsi').value = button.getAttribute('data-deskripsi') || '';
            document.getElementById('edit_nilai_maksimal').value = button.getAttribute('data-nilai-maksimal') || '100';

            const availability = document.getElementById('edit_tersedia_untuk_mahasiswa');
            if (availability) {
                availability.value = button.getAttribute('data-tersedia') || '1';
            }
        };

        const availabilityInput = document.getElementById('tersedia_untuk_mahasiswa');
        const availabilityLabel = document.getElementById('jenisTaAvailabilityLabel');
        const updateAvailabilityLabel = () => {
            if (!availabilityInput || !availabilityLabel) return;
            availabilityLabel.textContent = availabilityInput.checked ? 'Tersedia' : 'Tidak tersedia';
        };

        if (availabilityInput) {
            availabilityInput.addEventListener('change', updateAvailabilityLabel);
            updateAvailabilityLabel();
        }
    </script>
@endsection
