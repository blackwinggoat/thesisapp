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
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/')}}">Home</a></li>
                <li class="active">Mahasiswa</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Daftar Mahasiswa</h3>
            <div class="the-box">
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
                            <th>Status Akun</th>
                            <th>Detail</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if ($data->count() === 0)
                            <tr>
                                <td colspan="6" class="text-center">Data mahasiswa tidak ditemukan.</td>
                            </tr>
                        @endif
                        @foreach ($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{ $data->firstItem() + $key }}</td>
                                <td>{{$value->C_NPM}}</td>
                                <td>{{$value->NAMA_MAHASISWA}}</td>
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
    </script>
@endsection
