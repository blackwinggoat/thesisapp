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
                <li><a href="#fakelink">Home</a></li>
                <li class="active">Syarat Ujian</li>
            </ol>

            <h3 class="page-heading">Form Syarat Ujian</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <form method="post" action="{{url('prodi/syaratadd')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Nama Syarat</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="nama_syarat"/>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Tipe Ujian</label>
                            <div class="col-xs-5">
                                <select class="form-control bold-border" name="tipe_ujian">
                                    <option>--</option>
                                    <option value='0'>Proposal</option>
                                    <option value='2'>Ujian Meja</option>
                                </select>
                            </div><!-- /.col-xs-5 -->
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-xs-7" align="right">
                                <button class="btn btn-primary btn-perspective" type="button" onclick="showPostModal(this)" data-formaction="{{url('prodi/syaratadd')}}" data-target="#modalPrimary" data-toggle="modal">Simpan</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div><!-- /.the-box -->
            <!-- End breadcrumb -->
            <h3 class="page-heading">Daftar Syarat Ujian Proposal</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nama Syarat</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data0 as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->nama_syarat}}</td>
                                <td>
                                    <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{ url('prodi/syaratdel/'.$value->syarat_ujian_id)}}"><i class="fa fa-trash-o"></i></button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
            <h3 class="page-heading">Daftar Syarat Ujian Meja</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nama Syarat</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data2 as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->nama_syarat}}</td>
                                <td>
                                    <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{ url('prodi/syaratdel/'.$value->syarat_ujian_id)}}"><i class="fa fa-trash-o"></i></button>
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

{{--ModalTambah--}}
@section("modalPrimaryTitle")
    Tambah Syarat Ujian
@endsection
@section("modalPrimaryBody")
    Apakah Anda yakin ingin menambah syarat ujian?
@endsection
@section("modalPrimaryFooter")
    <button onclick="submit(this)" class="btn btn-default">Tambah</button>
@endsection

{{--ModalHapus--}}
@section("modalDangerTitle")
    Hapus Syarat Ujian
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



