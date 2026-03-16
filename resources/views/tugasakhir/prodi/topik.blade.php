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
                <li class="active">Topik Usulan</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Daftar Pengusul</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-th-block">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_pengusul as $key => $value)
                                <tr class="odd gradeX">
                                    <td width="1%" align="center">{{ ++$key }}</td>
                                    <td>{{ $value->C_NPM }}</td>
                                    <td>{{ $value->NAMA_MAHASISWA }}</td>
                                    <td><a href="{{ url('prodi/detail_topikusulan/' . $value->C_NPM) }}"><i
                                                class="fa fa-copy icon-square icon-xs icon-primary"></i></a></td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->


            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Daftar Riwayat Usulan</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Topik</th>
                                <th>Kerangka Pikir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_riwayat_usulan as $key => $value)
                                <tr class="odd gradeX">
                                    <td width="1%" align="center">{{ ++$key }}</td>
                                    <td>{{ $value->C_NPM }}</td>
                                    <td>{{ $value->NAMA_MAHASISWA }}</td>
                                    <th>{{ $value->topik }}</th>
                                    <td>
                                        @if ($value->kerangka)
                                            <button class="btn btn-primary" onclick="showModal(this)"
                                                data-href="{{ asset('dokumen/' . $value->kerangka) }}"
                                                data-target="#modalPrimary" data-toggle="modal"><i
                                                    class="fa fa-paperclip"></i>
                                            </button>
                                        @else
                                            <span class="badge badge-danger">Mahasiswa tidak mengupload kerangka
                                                pikir</span>
                                        @endif
                                    </td>
                                    <th>
                                        @if ($value->status == 0)
                                            Belum dikonfirmasi
                                        @elseif($value->status == 1)
                                            Diterima
                                        @elseif($value->status == 2)
                                            Ditolak
                                        @endif
                                    </th>
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

{{-- ModalSetUser --}}
@section('modalPrimaryTitle')
    Download
@endsection
@section('modalPrimaryBody')
    Download kerangka pikir?
@endsection
@section('modalPrimaryFooter')
    <button onclick="goOnNewTab(this)" class="btn btn-default">Download</button>
@endsection

@section('script')
    <script>
        let modal, modalId, modalFooter, link, form, formaction;
        const showModal = e => {
            link = e.getAttribute("data-href");
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
            modalFooter = modal.querySelector(".modal-footer");
        };

        const goOn = e => {
            modal.querySelector(".modal-backdrop").click();
            window.location.href = link;
        };

        const goOnNewTab = () => {
            modal.querySelector(".modal-backdrop").click();
            window.open(link);
        };
    </script>
@endsection
