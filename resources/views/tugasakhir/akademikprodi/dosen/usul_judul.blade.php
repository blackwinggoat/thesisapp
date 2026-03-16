@extends('tugasakhir.index')
@section('isi')

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Daftar Usulan Judul</li>
        </ol>
        <h3 class="page-heading">Usulan Judul Anak Bimbingan</h3>
        @if (session('status') == 'success')
            <div class="alert alert-success alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>Data Berhasil Dihapus <i class="fa fa-smile-o"></i></p>
            </div>
        @elseif(session('status') == 'error')
            <div class="alert alert-danger alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>Data Gagal Dihapus <i class="fa fa-smile-o"></i></p>
            </div>
        @endif
        <div class="the-box">
            <div class="row text-right">
                <div class="col-sm-12">
                    <a class="btn btn-primary btn-perspective" href="{{url('dsn/add_usul_judul')}}">Tambah</a>
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Judul</th>
                            <th>Waktu Usul</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $value)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$key}}</td>
                        <td>{{$value->NAMA_MAHASISWA}}</td>
                        <td>{{$value->C_NPM}}</td>
                        <td>{{$value->judul}}</td>
                        <td>{{$value->created_at}}</td>
                        <td>
                            <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{url("/dsn/usul_judul/delete/$value->usulan_judul_id")}}"><i class="fa fa-trash"></i></button>
                        </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
    </div><!-- /.container-fluid -->
    @endsection

    {{--ModalTolak--}}
@section("modalDangerTitle")
    Hapus Usulan Judul
@endsection
@section("modalDangerBody")
    Apakah Anda yakin menghapus usulan judul?
@endsection
@section("modalDangerFooter")
    <button onclick="goOn(this)" class="btn btn-danger">Hapus</button>
@endsection

@section("script")
    <script>
        let modal, modalId, modalFooter, link, form, formaction;

        const showModal = e => {
            link = e.getAttribute("data-href");
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
        };

        const goOn = () => {
            modal.querySelector(".modal-backdrop").click();
            window.location.href = link;
        };
    </script>
@endsection