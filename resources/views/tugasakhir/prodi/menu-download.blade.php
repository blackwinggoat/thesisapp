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
                <li><a href="$#fakelink"><i class="fa fa-home"></i></a></li>
                <li><a href="#fakelink">Home</a></li>
                <li class="active">Menu Download</li>
            </ol>

            <h3 class="page-heading">Form Download</h3>
            <!-- BEGIN DATA TABLE -->
            @if (Session::get("status") == "berhasil")
                <div class="alert alert-success" role="alert"><strong>Berhasil! </strong>Data Berhasil Disimpan</div>
            @elseif(Session::get("status") == "gagal")
                <div class="alert alert-danger" role="alert"><strong>Gagal! </strong>Data Gagal Disimpan</div>
            @endif
            <div class="the-box">
                <form method="post" action="{{route('kirimDownload')}}">
                    @csrf
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Nama Dokumen</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="nama_dokumen" id="nama_dokumen" placeholder="Kerangka Berpikir"/>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Link Download</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="link_download" id="link_download" placeholder="https://drive.google.com/drive/u/2/my-drive"/>
                            </div>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <div class="col-xs-7" align="right">
                                <button class="btn btn-primary btn-perspective" type="button" onclick="showPostModal(this)" data-formaction="{{route('kirimDownload')}}" data-target="#modalPrimary" data-toggle="modal">Simpan</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div><!-- /.the-box -->
            <!-- End breadcrumb -->
            <h3 class="page-heading">Daftar Bidang Ilmu Tugas Akhir</h3>
            <!-- BEGIN DATA TABLE -->
            @if (Session::get("status") == "berhasil_hapus")
                <div class="alert alert-success" role="alert"><strong>Berhasil! </strong>Data Berhasil Dihapus</div>
            @elseif(Session::get("status") == "gagal_hapus")
                <div class="alert alert-danger" role="alert"><strong>Gagal! </strong>Data Gagal Dihapus</div>
            @endif
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nama Dokumen</th>
                            <th>Link Download</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->nama_dokumen}}</td>
                                <td><a href="{{$value->link_download}}" target="_blank" class="btn btn-primary">Download</a></td>
                                <td>
                                    <button id="tampilDataDownload" data-id="{{$value->id_download}}" data-nama-dokumen="{{$value->nama_dokumen}}" data-link-download="{{$value->link_download}}" class="btn btn-info"><i class="fa fa-edit"></i></button>
                                    <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{route('hapusDownload', ["id" => $value->id_download])}}"><i class="fa fa-trash-o"></i></button>
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
    Tambah Bidang Ilmu
@endsection
@section("modalPrimaryBody")
    Apakah Anda yakin ingin menambah daftar download?
@endsection
@section("modalPrimaryFooter")
    <button onclick="submit(this)" class="btn btn-default">Tambah</button>
@endsection

{{--ModalHapus--}}
@section("modalDangerTitle")
    Hapus Bidang Ilmu
@endsection
@section("modalDangerBody")
    Apakah Anda yakin ingin menghapus data?
@endsection
@section("modalDangerFooter")
    <button onclick="goOn(this)" class="btn btn-default">Hapus</button>
@endsection

@section("script")
    <script>

        $(document).ready(function () {
            
        });

        $(document).on('click', '#tampilDataDownload', function() { 
            var nama_dokumen = $(this).attr("data-nama-dokumen");
            var link_download = $(this).attr("data-link-download");
            $('#nama_dokumen').val(nama_dokumen);
            $('#link_download').val(link_download);
        });


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



