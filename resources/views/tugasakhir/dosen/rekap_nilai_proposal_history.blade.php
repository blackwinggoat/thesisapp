@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
            <!-- End page heading -->

            <!-- Begin breadcrumb -->
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">History Rekap Nilai Proposal</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <div style="display: flex; justify-content: flex-start; align-items: center; gap: 12px; margin-bottom: 8px;">
                <h3 class="page-heading" style="margin: 0;">History Daftar Hasil Ujian Per Periode</h3>
                <a href="{{ url('dsn/rekap_nilai_proposal') }}" class="btn btn-default btn-sm">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Ujian</th>
                            <th>Nama Periode</th>
                            <th>Kuota</th>
                            <th>Jumlah Peserta</th>
                            <th>Detail Peserta</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{ ++$key }}</td>
                                <td>{{ $value->tgl_ujian }}</td>
                                <td>{{ $value->nama_periode }}</td>
                                <td>{{ $value->kuota }}</td>
                                <td>{{ $value->jml_peserta }}</td>
                                <td><a href="{{ url('dsn/detail_rekap_nilai_proposal_history/'.$value->pendaftaran_id) }}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a></td>
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
