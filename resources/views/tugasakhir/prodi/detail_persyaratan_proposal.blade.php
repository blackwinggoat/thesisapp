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
            <li class="active">Peserta Proposal</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Detail Pengajuan Persyaratan Proposal</h3>
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
            <div class="row">
                <div class="col-sm-6">
                    <h4><b>Mahasiswa:</b></h4>
                    <div class="row">
                        <div class="col-sm-1" style="width: 150px;display: inline-block">NIM</div>
                        <div class="col-sm-6" style="width: 200px;display: inline-block">: {{$mhs->C_NPM}}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-1" style="width: 150px;display: inline-block">Nama Mahasiswa</div>
                        <div class="col-sm-6" style="width: 200px;display: inline-block">: {{$mhs->NAMA_MAHASISWA}}
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row text-left">
                <div class="col-sm-6">
                    @if (auth()->user()->name == 'akademikproditi' || auth()->user()->name == 'akademikprodisi')
                    <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                        data-href="{{ url("akademikprodi/konfirmasi_persyaratan_ujian_by_nim/1")}}/{{$data[0]['C_NPM']}}"
                        class="btn btn-primary btn-perspective">Terima Semua</button>
                    <button onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal"
                        data-href="{{ url("akademikprodi/konfirmasi_persyaratan_ujian_by_nim/0")}}/{{$data[0]['C_NPM']}}"
                        class="btn btn-danger btn-perspective">Tolak Semua</button>
                    <button data-href="{{url("/akademikprodi/selesai_konfirmasi/$mhs->C_NPM/0")}}"
                        onclick="showModal(this)" data-target="#modalWarning" data-toggle="modal"
                        class="btn btn-warning btn-perspective" style="margin-left: auto">Selesai</button>
                    @else
                    <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                        data-href="{{ url("prodi/konfirmasi_persyaratan_ujian_by_nim/1")}}/{{$data[0]['C_NPM']}}"
                        class="btn btn-primary btn-perspective">Terima Semua</button>
                    <button onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal"
                        data-href="{{ url("prodi/konfirmasi_persyaratan_ujian_by_nim/0")}}/{{$data[0]['C_NPM']}}"
                        class="btn btn-danger btn-perspective">Tolak Semua</button>
                    <button data-href="{{url("/prodi/selesai_konfirmasi/$mhs->C_NPM/0")}}" onclick="showModal(this)"
                        data-target="#modalWarning" data-toggle="modal" class="btn btn-warning btn-perspective"
                        style="margin-left: auto">Selesai</button>
                    @endif
                </div>
            </div>
            <br>
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Nama Dokumen</th>
                            <th>File</th>
                            <th>Aksi</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $key => $value)
                        @php
                        $trtsyaratujian = \App\TrtSyaratUjian::where(["syarat_ujian_id" => $value->syarat_ujian_id,
                        "C_NPM" => $mhs->C_NPM])->first()
                        @endphp
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$key}}</td>
                            <td>{{$value->nama_syarat}}</td>
                            <td>
                                <button type="button" onclick="showModal(this)" data-href="{{$value->link}}"
                                    data-target="#modalDefault" data-toggle="modal" class="btn bg-dark"
                                    style="color: #fff"><i class="fa fa-paperclip"></i></button>
                            </td>
                            <td>
                                @if (auth()->user()->name == 'akademikproditi' || auth()->user()->name == 'akademikprodisi')
                                @if($value->status == 2)
                                <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                                    data-href="{{ url("akademikprodi/konfirmasi_persyaratan_ujian/1/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-default">Terima</button>
                                <button onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal"
                                    data-href="{{ url("akademikprodi/konfirmasi_persyaratan_ujian/0/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-default">Tolak</button>
                                @elseif($value->status == 1)
                                <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                                    data-href="{{ url("akademikprodi/konfirmasi_persyaratan_ujian/1/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-primary">Terima</button>
                                <button onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal"
                                    data-href="{{ url("akademikprodi/konfirmasi_persyaratan_ujian/0/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-default">Tolak</button>
                                @elseif($value->status == 0)
                                <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                                    data-href="{{ url("akademikprodi/konfirmasi_persyaratan_ujian/1/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-default">Terima</button>
                                <button onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal"
                                    data-href="{{ url("akademikprodi/konfirmasi_persyaratan_ujian/0/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-danger">Tolak</button>
                                @endif
                                @else
                                @if($value->status == 2)
                                <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                                    data-href="{{ url("prodi/konfirmasi_persyaratan_ujian/1/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-default">Terima</button>
                                <button onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal"
                                    data-href="{{ url("prodi/konfirmasi_persyaratan_ujian/0/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-default">Tolak</button>
                                @elseif($value->status == 1)
                                <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                                    data-href="{{ url("prodi/konfirmasi_persyaratan_ujian/1/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-primary">Terima</button>
                                <button onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal"
                                    data-href="{{ url("prodi/konfirmasi_persyaratan_ujian/0/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-default">Tolak</button>
                                @elseif($value->status == 0)
                                <button onclick="showModal(this)" data-target="#modalPrimary" data-toggle="modal"
                                    data-href="{{ url("prodi/konfirmasi_persyaratan_ujian/1/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-default">Terima</button>
                                <button onclick="showModal(this)" data-target="#modalDanger" data-toggle="modal"
                                    data-href="{{ url("prodi/konfirmasi_persyaratan_ujian/0/$value->syarat_ujian_id/$value->C_NPM")}}"
                                    class="btn btn-danger">Tolak</button>
                                @endif
                                @endif
                            </td>
                            <td>
                                @if (auth()->user()->name == 'akademikproditi' || auth()->user()->name == 'akademikprodisi')
                                <a class="btn btn-info"
                                    href="{{url('akademikprodi/detail_persyaratan_proposal/catatan')}}/{{$trtsyaratujian->id}}/{{$mhs->C_NPM}}"><i
                                        class="fa fa-newspaper-o"></i></a>
                                @else
                                <a class="btn btn-info" href="{{url('prodi/detail_persyaratan_proposal/catatan')}}/{{$trtsyaratujian->id}}/{{$mhs->C_NPM}}"><i
                                        class="fa fa-newspaper-o"></i></a>
                                @endif
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

{{--ModalSelesai--}}
@section("modalWarningTitle")
Selesai
@endsection
@section("modalWarningBody")
Apakah Anda yakin selesai mengkonfirmasi dokumen?
@endsection
@section("modalWarningFooter")
<button onclick="goOn(this)" class="btn btn-default">Selesai</button>
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

{{--ModalTerima--}}
@section("modalPrimaryTitle")
Terima
@endsection
@section("modalPrimaryBody")
Apakah Anda yakin ingin menerima pengajuan dokumen?
@endsection
@section("modalPrimaryFooter")
<button onclick="goOn(this)" class="btn btn-default">Terima</button>
@endsection

{{--ModalTolak--}}
@section("modalDangerTitle")
Tolak
@endsection
@section("modalDangerBody")
Apakah Anda yakin ingin menolak pengajuan dokumen?
@endsection
@section("modalDangerFooter")
<button onclick="goOn(this)" class="btn btn-default">Tolak</button>
@endsection

@section("script")
<script>
    //Modal
    let modal, modalId, modalFooter, link, form, formaction, sui;

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
</script>
@endsection