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
                <li><a href="{{ url('/')}}">Home</a></li>
                <li class="active">Mahasiswa</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Daftar Mahasiswa</h3>
            <div class="the-box">
                @if ($siakadSyncEnabled ?? false)
                    <div class="clearfix" style="margin-bottom: 18px;">
                        <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#modalSinkronisasiMahasiswa">
                            <i class="fa fa-cloud-download"></i> Tambah dari SIAKAD
                        </button>
                    </div>
                @endif
                <form method="get" action="{{ url('prodi/mahasiswa') }}" class="form-horizontal" style="margin-bottom: 18px;">
                    <div class="row">
                        <div class="col-md-5">
                            <label style="display:block;">Cari NIM / Nama</label>
                            <input type="text" name="q" class="form-control" value="{{ $q ?? '' }}" placeholder="Contoh: 1302022 atau nama mahasiswa">
                        </div>
                        <div class="col-md-2">
                            <label style="display:block;">Status Akun</label>
                            <select name="status_akun" class="form-control">
                                <option value="semua" {{ ($statusAkun ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua</option>
                                <option value="aktif" {{ ($statusAkun ?? 'semua') === 'aktif' ? 'selected' : '' }}>Sudah Aktif</option>
                                <option value="belum" {{ ($statusAkun ?? 'semua') === 'belum' ? 'selected' : '' }}>Belum Aktif</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label style="display:block;">Per Halaman</label>
                            <select name="per_page" class="form-control">
                                @foreach ([25, 50, 100, 200] as $size)
                                    <option value="{{ $size }}" {{ (int) ($perPage ?? 25) === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label style="display:block;">Aksi</label>
                            <div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
                                <a href="{{ url('prodi/mahasiswa') }}" class="btn btn-default">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div style="margin-bottom: 10px;">
                    Menampilkan <strong>{{ $data->count() }}</strong> dari <strong>{{ $data->total() }}</strong> data mahasiswa
                    @if (!empty($scopeMahasiswaLabel))
                        <span class="label label-info" style="margin-left: 8px;">{{ $scopeMahasiswaLabel }}</span>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Jenis Kelas</th>
                            <th>Status Akun</th>
                            <th>Detail</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if ($data->count() === 0)
                            <tr>
                                <td colspan="7" class="text-center">Data mahasiswa tidak ditemukan.</td>
                            </tr>
                        @endif
                        @foreach ($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{ $data->firstItem() + $key }}</td>
                                <td>{{$value->C_NPM}}</td>
                                <td>{{$value->NAMA_MAHASISWA}}</td>
                                <td>
                                    @if ($studentClassFeatureReady ?? false)
                                        <form id="jenis-kelas-{{ $value->C_NPM }}" action="{{ route('prodi.mahasiswa.jenis_kelas', $value->C_NPM) }}" method="post" class="student-class-form">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="jenis_kelas" value="">
                                            <span class="student-class-control">
                                                <label class="student-class-switch" title="Ubah jenis kelas mahasiswa">
                                                    <input type="checkbox"
                                                           class="student-class-toggle"
                                                           aria-label="Ubah jenis kelas {{ $value->NAMA_MAHASISWA }}"
                                                           data-form-id="jenis-kelas-{{ $value->C_NPM }}"
                                                           data-student-name="{{ $value->NAMA_MAHASISWA }}"
                                                           {{ (int) ($value->is_eksekutif ?? 0) === 1 ? 'checked' : '' }}>
                                                    <span class="student-class-slider" aria-hidden="true"></span>
                                                </label>
                                                @if ((int) ($value->is_eksekutif ?? 0) === 1)
                                                    <span class="label label-warning">Eksekutif</span>
                                                @else
                                                    <span class="label label-info">Reguler</span>
                                                @endif
                                            </span>
                                        </form>
                                    @elseif ((int) ($value->is_eksekutif ?? 0) === 1)
                                        <span class="label label-warning">Eksekutif</span>
                                    @else
                                        <span class="label label-info">Reguler</span>
                                    @endif
                                </td>
                                <td>
                                    @if ((int) ($value->has_user ?? 0) === 1)
                                        <i class="fa fa-check-circle text-success"></i>
                                    @else
                                        <i class="fa fa-times-circle text-danger"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ url('prodi/detail_mahasiswa/'.$value->C_NPM)}}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a>
                                </td>
                                <td>
                                    <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal" title="Aktifasi Akun" class="btn btn-danger" data-href="{{ url('prodi/make_user/'.$value->C_NPM)}}"><i class="fa fa-sign-in"></i></button>
                                    <button onclick="showModal(this)" data-target="#modalInfo" data-toggle="modal" title="Reset Akun" class="btn btn-default" data-href="{{ url('prodi/reset_user/'.$value->C_NPM)}}"><i class="fa fa-recycle"></i></button>
                                    <a class="btn btn-info" href="{{ url('prodi/login_as_mahasiswa/'.$value->C_NPM) }}" title="Login sebagai mahasiswa">
                                        <i class="fa fa-user"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
                <div>
                    {{ $data->links() }}
                </div>
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div>

    @if ($siakadSyncEnabled ?? false)
        <div class="modal fade" id="modalSinkronisasiMahasiswa" tabindex="-1" role="dialog" aria-labelledby="modalSinkronisasiMahasiswaLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="modalSinkronisasiMahasiswaLabel">Tambah Mahasiswa dari SIAKAD</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4 style="margin-top: 0;">Per NIM</h4>
                                <form method="post" action="{{ route('prodi.mahasiswa.siakad.nim') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="siakad-nim">NIM</label>
                                        <input id="siakad-nim" type="text" name="nim" class="form-control" maxlength="11" inputmode="numeric" pattern="[0-9]{11}" value="{{ preg_match('/^[0-9]{11}$/', old('nim', $q ?? '')) ? old('nim', $q ?? '') : '' }}" placeholder="Contoh: 13020250206" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Cari dan Tambah</button>
                                </form>
                            </div>
                            <div class="col-sm-6" style="border-left: 1px solid #e5e5e5;">
                                <h4 style="margin-top: 0;">Per Angkatan</h4>
                                <form method="post" action="{{ route('prodi.mahasiswa.siakad.angkatan') }}" onsubmit="return window.confirm('Tambahkan mahasiswa yang belum ada dari angkatan ini? Data yang sudah tersedia tidak akan diubah.');">
                                    {{ csrf_field() }}
                                    @if ($siakadSyncNeedsProgramChoice ?? false)
                                        <div class="form-group">
                                            <label for="siakad-kode-prodi">Program Studi</label>
                                            <select id="siakad-kode-prodi" name="kode_prodi" class="form-control" required>
                                                <option value="">Pilih program studi</option>
                                                @foreach ($siakadPrograms as $programCode => $program)
                                                    <option value="{{ $programCode }}" {{ old('kode_prodi') === $programCode ? 'selected' : '' }}>{{ $program['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @else
                                        <input type="hidden" name="kode_prodi" value="{{ $siakadSyncProgramCode }}">
                                        <div class="form-group">
                                            <label>Program Studi</label>
                                            <p class="form-control-static">{{ $siakadPrograms[$siakadSyncProgramCode]['label'] ?? '-' }}</p>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="siakad-angkatan">Angkatan</label>
                                        <input id="siakad-angkatan" type="text" name="angkatan" class="form-control" maxlength="4" inputmode="numeric" pattern="20[0-9]{2}" value="{{ old('angkatan') }}" placeholder="Contoh: 2025" required>
                                    </div>
                                    <button type="submit" class="btn btn-success"><i class="fa fa-users"></i> Tambah Angkatan</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

{{--ModalSetUser--}}
@section("modalPrimaryTitle")
    Daftarkan User
@endsection
@section("modalPrimaryBody")
    Apakah Anda yakin ingin mendaftarkan user?
@endsection
@section("modalPrimaryFooter")
    <button onclick="goOn(this)" class="btn btn-default">Daftarkan</button>
@endsection

{{--ModalResetUser--}}
@section("modalInfoTitle")
    Reset User
@endsection
@section("modalInfoBody")
    Apakah Anda yakin ingin me-reset user?
@endsection
@section("modalInfoFooter")
    <button onclick="goOn(this)" class="btn btn-default">Reset</button>
@endsection

@section("script")
    <style>
        .student-class-form {
            display: inline-block;
            margin: 0;
        }

        .student-class-control {
            align-items: center;
            display: inline-flex;
            gap: 8px;
            white-space: nowrap;
        }

        .student-class-switch {
            cursor: pointer;
            display: inline-block;
            flex: 0 0 42px;
            height: 22px;
            margin: 0;
            position: relative;
            width: 42px;
        }

        .student-class-switch input {
            height: 0;
            opacity: 0;
            width: 0;
        }

        .student-class-slider {
            background-color: #5bc0de;
            border-radius: 11px;
            bottom: 0;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            transition: background-color .2s ease;
        }

        .student-class-slider:before {
            background-color: #ffffff;
            border-radius: 50%;
            bottom: 3px;
            content: '';
            height: 16px;
            left: 3px;
            position: absolute;
            transition: transform .2s ease;
            width: 16px;
        }

        .student-class-switch input:checked + .student-class-slider {
            background-color: #f0ad4e;
        }

        .student-class-switch input:checked + .student-class-slider:before {
            transform: translateX(20px);
        }

        .student-class-switch input:focus + .student-class-slider {
            box-shadow: 0 0 0 2px rgba(91, 192, 222, .28);
        }
    </style>
    <script>
        let modal, modalId, modalFooter, link;

        const showModal = e => {
            link = e.getAttribute("data-href");
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
        };

        const goOn = e => {
            window.location.href = link;
        }

        document.querySelectorAll('.student-class-toggle').forEach(function (toggle) {
            toggle.addEventListener('change', function () {
                const jenisKelas = this.checked ? 'eksekutif' : 'reguler';
                const labelKelas = jenisKelas === 'eksekutif' ? 'Eksekutif' : 'Reguler';
                const namaMahasiswa = this.getAttribute('data-student-name');

                if (!window.confirm('Ubah jenis kelas ' + namaMahasiswa + ' menjadi ' + labelKelas + '?')) {
                    this.checked = !this.checked;
                    return;
                }

                const form = document.getElementById(this.getAttribute('data-form-id'));
                form.querySelector('input[name="jenis_kelas"]').value = jenisKelas;
                form.submit();
            });
        });
    </script>
@endsection
