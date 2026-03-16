@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading">Sistem Informasi Program Studi <small>Tugas Akhir</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="index.html"><i class="fa fa-home"></i></a></li>
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

            <h3 class="page-heading">Form Jenis Tugas Akhir</h3>
            <div class="the-box">
                <form method="post" action="{{ url('prodi/master/jenis_tugas_akhir') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Kode Jenis Tugas Akhir</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="kode_jenis_tugas_akhir"
                                    value="{{ old('kode_jenis_tugas_akhir') }}" />
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">Deskripsi</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="deskripsi"
                                    value="{{ old('deskripsi') }}" />
                            </div>
                        </div>

                        <br><br>

                        <div class="form-group">
                            <div class="col-xs-7" align="right">
                                <button class="btn btn-primary btn-perspective" type="button"
                                    onclick="showPostModal(this)"
                                    data-formaction="{{ url('prodi/master/jenis_tugas_akhir') }}"
                                    data-target="#modalPrimary" data-toggle="modal">Simpan</button>
                            </div>
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
                                        <button class="btn btn-danger" onclick="showModal(this)"
                                            data-target="#modalDanger" data-toggle="modal"
                                            data-href="{{ url('prodi/master/jenis_tugas_akhir/delete/' . $value->jenis_tugas_akhir_id) }}">
                                            <i class="fa fa-trash-o"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data jenis tugas akhir.</td>
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
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
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
        }
    </script>
@endsection
