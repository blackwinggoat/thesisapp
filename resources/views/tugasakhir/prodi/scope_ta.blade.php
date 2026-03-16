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
                <li class="active"> Bidang Ilmu TA</li>
            </ol>

            <!-- Begin breadcrumb -->
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="index.html"><i class="fa fa-home"></i></a></li>
                <li><a href="#fakelink">Chart or graph</a></li>
                <li class="active">Morris chart</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN MORRIS CHART -->
            <div class="row">
                <div class="col-sm-6">
                    <div class="the-box">
                        <h4 class="small-title">Grafik Jumlah Lulusan pertahun Ajaran</h4>
                        <div id="morris-line-example" style="height: 250px;"></div>
                    </div><!-- .the-box -->
                </div><!-- /.col-sm-6 -->
                <div class="col-sm-6">
                    <div class="the-box">
                        <h4 class="small-title">Grafik Jumlah Lulusan berdasarkan Bidang Ilmu</h4>
                        <div id="morris-area-example" style="height: 250px;"></div>
                    </div><!-- .the-box  -->
                </div><!-- /.col-sm-6 -->
            </div>
            <!-- END MORRIS CHART -->

            <h3 class="page-heading">Form Bidang Ilmu Tugas Akhir</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <form method="post" action="{{url('prodi/scope_add')}}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <fieldset>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Bidang Ilmu</label>
                            <div class="col-lg-5">
                                <input type="text" class="form-control bold-border" name="bidang_ilmu"/>
                            </div>
                        </div>

                        <br><br>
                        <div class="form-group">
                            <div class="col-xs-7" align="right">
                                <button class="btn btn-primary btn-perspective" type="button" onclick="showPostModal(this)" data-formaction="{{url('prodi/scope_add')}}" data-target="#modalPrimary" data-toggle="modal">Simpan</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div><!-- /.the-box -->
            <!-- End breadcrumb -->
            <h3 class="page-heading">Daftar Bidang Ilmu Tugas Akhir</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nama Bidang Ilmu</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->bidang_ilmu}}</td>
                                <td>
                                    <button class="btn btn-danger" onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal" data-href="{{ url('prodi/scope_del/'.$value->bidangilmu_id)}}"><i class="fa fa-trash-o"></i></button>
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
    Apakah Anda yakin ingin menambah bidang ilmu?
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



