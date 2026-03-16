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
    <script>//Modal
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


