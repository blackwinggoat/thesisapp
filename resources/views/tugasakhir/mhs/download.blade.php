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
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Pengajuan Topik</li>
        </ol>
        <!-- End breadcrumb -->

        <!-- BEGIN DATA TABLE -->
        <!-- BEGIN DATA TABLE -->
        <h3 class="page-heading">Download</h3>
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-example">
                    <thead class="the-box dark full">
                    <tr>
                        <th>No</th>
                        <th>Nama File</th>
                        <th class="text-center" width="130">Akses Dokumen</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="odd gradeX">
                        <td width="1%" align="center">1</td>
                        <td>SK Pembimbing</td>
                        @if (helper::getStatusSKPembimbingForMahasiswa(auth()->user()->name) != '')
                            <td class="text-center"><a target="_blank" title="Buka tampilan web" href="{{ url('sk_pembimbing')}}/{{str_replace("/","",helper::getStatusSKPembimbingForMahasiswa(auth()->user()->name)->nomor_sk)}}"><i class="fa fa-external-link icon-square icon-xs icon-dark"></i></a> <a target="_blank" title="Unduh dokumen PDF" href="{{ url('sk_pembimbing_pdf')}}/{{str_replace("/","",helper::getStatusSKPembimbingForMahasiswa(auth()->user()->name)->nomor_sk)}}"><i class="fa fa-file-pdf-o icon-square icon-xs icon-danger"></i></a></td>
                        @else
                            <td><span class="badge badge-danger">SK Pembimbing Belum Ada</span></td>
                        @endif
                    </tr>
                    <tr class="odd gradeX">
                        <td width="1%" align="center">2</td>
                        <td>SK Ujian Proposal</td>
                        @if (helper::getStatusSKUjianProposalForMahasiswa(auth()->user()->name) != '')
                            <td class="text-center"><a target="_blank" title="Buka tampilan web" href="{{ url('mhs/surat_sk_proposal')}}/{{helper::getPendaftaranIdForMahasiswa()}}"><i class="fa fa-external-link icon-square icon-xs icon-dark"></i></a> <a target="_blank" title="Unduh dokumen PDF" href="{{ url('mhs/surat_sk_proposal_pdf')}}/{{helper::getPendaftaranIdForMahasiswa()}}"><i class="fa fa-file-pdf-o icon-square icon-xs icon-danger"></i></a></td>
                        @else
                            <td><span class="badge badge-danger">SK Ujian Proposal Belum Ada</span></td>
                        @endif
                    </tr>
                    <tr class="odd gradeX">
                        <td width="1%" align="center">3</td>
                        <td>SK Ujian Meja</td>
                        @if (helper::getStatusSKUjianMejaForMahasiswa(auth()->user()->name) != '')
                            <td class="text-center"><a target="_blank" title="Buka tampilan web" href="{{ url('mhs/surat_sk_ujian_meja')}}/{{str_replace("/","",helper::getStatusSKUjianMejaForMahasiswa(auth()->user()->name)->nomor_sk)}}"><i class="fa fa-external-link icon-square icon-xs icon-dark"></i></a> <a target="_blank" title="Unduh dokumen PDF" href="{{ url('mhs/surat_sk_ujian_meja_pdf')}}/{{str_replace("/","",helper::getStatusSKUjianMejaForMahasiswa(auth()->user()->name)->nomor_sk)}}"><i class="fa fa-file-pdf-o icon-square icon-xs icon-danger"></i></a></td>
                        @else
                            <td><span class="badge badge-danger">SK Ujian Meja Belum Ada</span></td>
                        @endif
                    </tr>
                    @foreach ($data as $key => $value)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{++$key+3}}</td>
                            <td>{{$value->nama_dokumen}}</td>
                            <td class="text-center"><a href="{{$value->link_download}}" target="_blank" title="Buka dokumen"><i class="fa fa-file-o icon-square icon-xs icon-dark"></i></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div><!-- /.table-responsive -->
        </div><!-- /.the-box .default -->
        <!-- END DATA TABLE -->
    </div><!-- /.container-fluid -->

@endsection
