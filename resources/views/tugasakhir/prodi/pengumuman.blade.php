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
        <form method="post" action="{{url('prodi/pengumuman')}}" enctype="multipart/form-data">
            {{ csrf_field() }}
        <div class="the-box">
            <fieldset>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Perihal</label>
                    <div class="col-xs-5">
                        <input type="text" class="form-control bold-border" name="judul" required/>
                    </div><!-- /.col-xs-5 -->
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Tanggal </label>
                    <div class="col-lg-5">
                        <input type="text" name="last_update" class="form-control datepicker bold-border" data-date-format="mm-dd-yy" placeholder="mm-dd-yy" required>
                    </div>
                </div>
                <br><br>
                <div class="form-group">
                    <label class="col-lg-2 control-label">Gambar</label>
                    <div class="col-lg-5">
                            <div class="input-group">
										<span class="input-group-btn">
											<span class="btn btn-default btn-file">
												Browse&hellip; <input type="file" name="gambar" class="bold-border">
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
                        <textarea class="summernote-lg" name="isi" required>Assalamualaikum Warahmatuulahi Wabarakaatuh, </textarea>
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
        <!-- End breadcrumb -->
        <h3 class="page-heading">Daftar Pengumuman </h3>
        <!-- BEGIN DATA TABLE -->
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Perihal</th>
                        <th>Isi</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $key => $value)
                    <tr class="odd gradeX">
                        <td width="1%" align="center">{{++$key}}</td>
                        <td>{{$value->last_update}}</td>
                        <td>{{$value->judul}}</td>
                        <td>{{mb_strimwidth(strip_tags($value->isi), 0, 100, '...')}}</td>
                        @if ($value->gambar == '')
                            <td><img src="{{asset('gambar/no_image.jpg')}}" height="50" width="50"></td>
                        @else
                            <td><img src="{{asset('gambar/'.$value->gambar)}}" height="50" width="50"></td>
                        @endif
                        <td>
                            <a href="{{ url('prodi/pengumuman/'.$value->pengumuman_id)}}"><i class="fa fa-pencil icon-square icon-xs icon-dark"></i></a>
                            <a class="btn btn-danger" href="{{ url('prodi/pengumumandel/'.$value->pengumuman_id)}}"><i class="fa fa-trash-o"></i></a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
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



