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
            <li><a href="#fakelink">Home</a></li>
            <li class="active">Daftar Ujian</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Daftar Periode Pendaftaran</h3>
        <form method="post" action="{{url('mhs/registrasi')}}" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="tipe_ujian" value="0">
            <div class="the-box">
                <div class="table-responsive">

                    <table class="table table-th-block">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Nama Periode</th>
                                <th>Jadwal Periode</th>
                                <th>Kuota</th>
                                <th>Jumlah Peserta</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $mstsyaratujian = \App\Model\mst_syarat_ujian::where(["tipe_ujian" => 0])->count();
                            $trtsyaratujian = \App\TrtSyaratUjian::where(["C_NPM" => auth()->user()->name, "status" =>
                            1])->whereIn("syarat_ujian_id", \App\Model\mst_syarat_ujian::where(["tipe_ujian" =>
                            0])->select("syarat_ujian_id"))->count();
                            $trtreg =
                            \App\Model\trt_reg::whereIn("bimbingan_id",\App\Model\trt_bimbingan::where("C_NPM",
                            auth()->user()->name)->select("bimbingan_id"))->whereIn("pendaftaran_id",\App\Model\mst_pendaftaran::where("tipe_ujian",
                            0)->select("pendaftaran_id"))->count();
                            @endphp
                            @foreach ($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->nama_periode}}</td>
                                <td>{{$value->tgl_start}} - {{$value->tgl_end}}</td>
                                <td>{{$value->kuota}}</td>
                                <td>{{$value->jml_peserta}}</td>
                                <td>
                                    <div class="btn-group">
                                        @if($mstsyaratujian == $trtsyaratujian && !empty($mstsyaratujian) &&
                                        $value->jml_peserta < $value->kuota && empty($trtreg))
                                            <button class="btn btn-primary btn-perspective" type="submit"
                                                name="pendaftaran_id" value="{{$value->pendaftaran_id}}">Daftar</button>
                                            @else
                                            <button class="btn btn-primary btn-perspective" type="button"
                                                disabled>Daftar</button>
                                            @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
        </form>
        <!-- END DATA TABLE -->


        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Persyaratan Ujian</h3>
        @if (session('status') == 'success')
            <div class="alert alert-success alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>Catatan Berhasil Ditambahkan<i class="fa fa-smile-o"></i></p>
            </div>
        @elseif(session('status') == 'error')
            <div class="alert alert-danger alert-block square fade in alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><strong>Status!</strong></p>
                <p>Data Gagal Ditambahkan, Konten Masih Kosong!<i class="fa fa-smile-o"></i></p>
            </div>
        @endif
        <div class="the-box">
            <div class="table-responsive">
                <form action="{{url("mhs/syarat_ujianpost")}}" onsubmit="return showPostModal(this)" method="post">
                    {{csrf_field()}}
                    <table class="table table-striped table-hover" id="">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Nama Dokumen</th>
                                <th>Link Dokumen</th>
                                <th>Status</th>
                                <th>Aksi</th>
                                <th>Catatan</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($syarat as $key => $value)
                            @php
                            $trtsyaratujian = \App\TrtSyaratUjian::where(["syarat_ujian_id" => $value->syarat_ujian_id,
                            "C_NPM" => auth()->user()->name])->first()
                            @endphp
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->nama_syarat}}</td>
                                <td>
                                    <div class="col-lg-5">
                                        <input type="hidden" name="syarat_ujian_id[]"
                                            value="{{$value->syarat_ujian_id}}" />
                                        @if($key == 1)
                                        <input type="hidden" name="sui" />
                                        @endif
                                        <input type="text" class="form-control bold-border" name="link[]"
                                            value="{{empty($trtsyaratujian) ?"": $trtsyaratujian->link}}" />
                                    </div>
                                </td>
                                <td>
                                    @if(!empty($trtsyaratujian))
                                    @if($trtsyaratujian->status == "0")
                                    <span class="badge badge-danger">ditolak</span>
                                    @elseif($trtsyaratujian->status == "1")
                                    <span class="badge badge-primary">diterima</span>
                                    @elseif($trtsyaratujian->status == "2")
                                    <span class="badge badge-warning">menunggu</span>
                                    @endif
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($trtsyaratujian))
                                    <button type="button" value="{{$key-1}}" onclick="showPostModal(this)"
                                        data-formaction="{{url("mhs/syarat_ujianpost")}}" data-target="#modalInfo"
                                        data-toggle="modal" class="btn btn-info"><i class="fa fa-edit"></i></button>
                                    <button type="button" onclick="showModal(this)"
                                        data-href="{{ url("mhs/syarat_ujiandel/0/$value->syarat_ujian_id")}}"
                                        data-target="#modalDanger" data-toggle="modal" class="btn btn-danger"><i
                                            class="fa fa-trash"></i></button>
                                    @else
                                    <button id="tombol_satu" type="button" value="{{$key-1}}"
                                        onclick="showPostModal(this)" data-formaction="{{url("mhs/syarat_ujianpost")}}"
                                        data-target="#modalPrimary" data-toggle="modal" class="btn btn-primary"><i
                                            class="fa fa-save"></i></button>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($trtsyaratujian))
                                    <a class="btn btn-info" href="{{url('mhs/signup_proposal/catatan')}}/{{$trtsyaratujian->id}}"><i
                                            class="fa fa-newspaper-o"></i></a>
                                    @endif
                                    
                                </td>
                                <td>
                                    @if(!empty($trtsyaratujian))
                                    <button type="button" onclick="showModal(this)"
                                        data-href="{{$trtsyaratujian->link}}" data-target="#modalDefault"
                                        data-toggle="modal" class="btn bg-dark" style="color: #fff"><i
                                            class="fa fa-paperclip"></i></button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <span>*Gunakan http/https untuk link dokumen</span>
                </form>
            </div><!-- /.table-responsive -->
            <div style="display: flex">
                @php
                $trtsyaratujianx = \App\TrtSyaratUjian::where("C_NPM",auth()->user()->name)->whereIn("syarat_ujian_id",
                \App\Model\mst_syarat_ujian::where("tipe_ujian",0)->select("syarat_ujian_id"))->count();
                $trtpengajuandokumen = \App\TrtPengajuanDokumen::where(["C_NPM" => auth()->user()->name, "type" =>
                0])->count();
                @endphp
                @if(count($syarat) == $trtsyaratujianx && count($syarat) != 0)
                @if($trtpengajuandokumen == 0)
                <button data-href="{{url("/mhs/ajukan_dokumen/0")}}" onclick="showModal(this)"
                    data-target="#modalWarning" data-toggle="modal" class="btn btn-warning btn-perspective"
                    style="margin-left: auto">Ajukan</button>
                @else
                <button data-href="{{url("/mhs/ajukan_dokumen/0")}}" onclick="showModal(this)"
                    data-target="#modalDanger1" data-toggle="modal" class="btn btn-danger btn-perspective"
                    style="margin-left: auto">Batalkan ajuan</button>
                @endif
                @else
                <button class="btn btn-warning btn-perspective" disabled style="margin-left: auto">Ajukan</button>
                @endif
            </div>
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->
</div>


<div class="modal fade" id="modalDanger1" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content modal-no-shadow modal-no-border bg-danger">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Batalkan Ajuan</h4>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin membatalkan ajuan?
            </div>
            <div class="modal-footer">
                <button onclick="goOn(this)" class="btn btn-default">Batalkan</button>
            </div><!-- /.modal-footer -->
        </div><!-- /.modal-content .modal-no-shadow .modal-no-border .the-box .info .full -->
    </div><!-- /.modal-dialog -->
</div><!-- /#InfoModalColor -->


@endsection




{{--ModalAjukan--}}
@section("modalWarningTitle")
Ajukan
@endsection
@section("modalWarningBody")
Apakah Anda yakin ingin mengajukan dokumen?
@endsection
@section("modalWarningFooter")
<button onclick="goOn(this)" class="btn btn-default">Ajukan</button>
@endsection

{{--ModalSimpan--}}
@section("modalPrimaryTitle")
Simpan
@endsection
@section("modalPrimaryBody")
Apakah Anda yakin ingin menyimpan data?
<br>
<span id="status" class="badge badge-danger"></span>
@endsection
@section("modalPrimaryFooter")
<button onclick="submit(this)" class="btn btn-default">Simpan</button>
@endsection

{{--ModalUbah--}}
@section("modalInfoTitle")
Ubah
@endsection
@section("modalInfoBody")
Apakah Anda yakin ingin mengubah data?
@endsection
@section("modalInfoFooter")
<button onclick="submit(this)" class="btn btn-default">Ubah</button>
@endsection

{{--ModalDownload--}}
@section("modalDefaultTitle")
Download Lampiran
@endsection
@section("modalDefaultBody")
Apakah Anda yakin ingin men-download lampiran?
@endsection
@section("modalDefaultFooter")
<button onclick="goOnNewTab(this)" class="btn btn-primary">Download</button>
@endsection

{{--ModalHapus--}}
@section("modalDangerTitle")
Hapus
@endsection
@section("modalDangerBody")
Apakah Anda yakin ingin menghapus data?
@endsection
@section("modalDangerFooter")
<button onclick="goOn(this)" class="btn btn-default">Hapus</button>
@endsection




@section("script")
<script>
    //Modal

    $('#tombol_satu').on('click', function () {
        console.log("Selamat Datang di Bagian Satu");

        var p1 = $('input[name="link[1]"]').val();

        console.log(p1);


        // if (p1 == "--" || p2 == "--") {
        //     console.log("Ini Bagian Satu");
        //     $('#tombol_dua').attr("disabled", "disabled");
        //     $('#status').html("Data Pembimbing Belum Lengkap");
        // } else {
        //     $('#status').html("");
        //     $("#tombol_dua").removeAttr("disabled");
        // }
    });
    let modal, modalId, modalFooter, link, form, formaction, sui;
    const showPostModal = e => {
        formaction = e.getAttribute("data-formaction");
        modalId = e.getAttribute("data-target");
        modal = document.querySelector(modalId);
        modalFooter = modal.querySelector(".modal-footer");
        sui = e.value
    };

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

    const goOnNewTab = () => {
        modal.querySelector(".modal-backdrop").click();
        window.open(link);
    };

    const submit = () => {
        form = document.querySelector(`form[action="${formaction}"]`);
        form.querySelector("input[name=sui]").value = sui;
        form.submit();
    };
</script>
@endsection