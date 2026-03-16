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
                <li><a href="index.html"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/')}}">Home</a></li>
                <li class="active">Mahasiswa</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Daftar Mahasiswa</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
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
                        @foreach ($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->C_NPM}}</td>
                                <td>{{$value->NAMA_MAHASISWA}}</td>
                                <td><?= helper::getStatusAkunPerMahasiswa($value->C_NPM)?></td>
                                <td>
                                    <a href="{{ url('prodi/detail_mahasiswa/'.$value->C_NPM)}}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a>
                                </td>
                                <td>
                                    <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal" title="Aktifasi Akun" class="btn btn-danger" data-href="{{ url('prodi/make_user/'.$value->C_NPM)}}"><i class="fa fa-sign-in"></i></button>
                                    <button onclick="showModal(this)" data-target="#modalInfo" data-toggle="modal" title="Reset Akun" class="btn btn-default" data-href="{{ url('prodi/reset_user/'.$value->C_NPM)}}"><i class="fa fa-recycle"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
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


