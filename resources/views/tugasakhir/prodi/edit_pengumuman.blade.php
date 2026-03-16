@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
<div class="page-content">
    <div class="container-fluid">
        <!-- Begin page heading -->
        <h1 class="page-heading">Sistem Informasi Program Studi <small>Tugas Akhir</small></h1>
        <!-- End page heading -->

        <!-- Begin breadcrumb -->
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="index.html"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/')}}">Home</a></li>
            <li class="active">Pengumuman</li>
        </ol>


        <h3 class="page-heading">Form Pengumuman</h3>
        @if (session('status') == 'success')
            <div class="alert alert-success alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>Data Pengumuman Berhasil Ditambahkan<i class="fa fa-smile-o"></i></p>
            </div>
        @elseif(session('status') == 'error')
            <div class="alert alert-danger alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>Data Gagal Ditambahkan, Konten Masih Kosong!<i class="fa fa-smile-o"></i></p>
            </div>
        @endif
        <!-- BEGIN DATA TABLE -->
        <form method="post" action="{{url('prodi/edit_pengumuman_post')}}" enctype="multipart/form-data">
            {{ csrf_field() }}
        <div class="the-box">
            <fieldset>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Perihal</label>
                    <div class="col-xs-5">
                        <input type="text" value="{{$data->judul}}" class="form-control bold-border" name="judul" required/>
                        <input type="hidden" name="id" value="{{$data->pengumuman_id}}">
                    </div><!-- /.col-xs-5 -->
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Tanggal </label>
                    <div class="col-lg-5">
                        <input type="text" value="{{helper::tgl1_new($data->last_update)}}" name="last_update" class="form-control datepicker bold-border" data-date-format="mm-dd-yy" placeholder="mm-dd-yy" required>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Gambar</label>
                    <div class="col-lg-5">
                            <div class="input-group">
										<span class="input-group-btn">
											<span class="btn btn-default btn-file">
												Browse&hellip; <input value="{{asset('gambar/'.$data->gambar)}}" type="file" name="gambar" class="bold-border">
											</span>
										</span>
                                <input type="text" class="form-control" readonly>
                            </div><!-- /.input-group -->
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Isi Pengumuman</label>
                    <div class="col-lg-5">
                        <textarea class="summernote-lg" name="isi" required><?= $data->isi ?></textarea>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <div class="col-xs-7" align="right">
                        <button id="tombol_simpan_satu" class="btn btn-primary btn-perspective" type="submit">Simpan</button>
                    </div>
                </div>
            </fieldset>
        </form>
        </div><!-- /.the-box -->
    </div><!-- /.container-fluid -->
@endsection

{{--ModalTambah--}}
@section("modalPrimaryTitle")
    Tambah Pengumuman
@endsection
@section("modalPrimaryBody")
    Apakah Anda yakin ingin menambah pengumuman?
@endsection
@section("modalPrimaryFooter")
    <button onclick="submit(this)" id="tombol_tambah_dua" class="btn btn-default">Tambah</button>
@endsection

{{--ModalHapus--}}
@section("modalDangerTitle")
    Hapus Pengumuman
@endsection
@section("modalDangerBody")
    Apakah Anda yakin ingin menghapus pengumuman?
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



