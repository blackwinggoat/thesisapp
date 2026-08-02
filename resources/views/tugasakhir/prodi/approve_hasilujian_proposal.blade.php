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
                <li class="active">Peserta Proposal</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">
                {{ !empty($isHistory) ? 'Riwayat Hasil Ujian Per Periode' : 'Daftar Hasil Ujian Per Periode' }}
            </h3>

            <?php if(session('status') == 'success'): ?>
                <div class="alert alert-success alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><strong>Status!</strong></p>
                    <p><b><?= session('total') ?></b> mahasiswa dengan penilaian proposal lengkap berhasil dikonfirmasi.</p>
                    <?php if((int) session('total_belum_lengkap') > 0): ?>
                        <p><b><?= session('total_belum_lengkap') ?></b> mahasiswa dilewati karena masih ada penilai yang belum mengisi nilai.</p>
                    <?php endif; ?>
                </div>
            <?php elseif(session('status') == 'warning'): ?>
                <div class="alert alert-warning alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><strong>Belum dapat dikonfirmasi.</strong></p>
                    <p>Tidak ada status yang berubah. <b><?= session('total_belum_lengkap') ?></b> mahasiswa masih memiliki penilaian yang belum lengkap.</p>
                </div>
            <?php elseif(session('status') == 'error'): ?>
                <div class="alert alert-danger alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <p><strong>Status!</strong></p>
                    <p>Konfirmasi hasil proposal gagal. Tidak ada status tambahan yang diubah.</p>
                </div>
            <?php endif; ?>

            <div class="the-box">
                @if (!empty($isHistory))
                    <a href="{{ url('prodi/approve_hasilujian_proposal') }}" class="btn btn-default mb-5" style="margin-bottom: 20px">Kembali</a>
                @else
                    <form method="POST" action="{{ url('prodi/approve_hasilujian_proposal_all_post') }}" style="display: inline-block" onsubmit="return confirm('Konfirmasi semua mahasiswa yang seluruh penilainya sudah lengkap?')">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-info mb-5" style="margin-bottom: 20px">Konfirmasi Semua Nilai Lengkap</button>
                    </form>
                    <a href="{{ url('prodi/approve_hasilujian_proposal_history') }}" class="btn btn-primary mb-5" style="margin-bottom: 20px">Riwayat</a>
                @endif
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Ujian</th>
                            <th>Nama Periode</th>
                            <th>Kuota</th>
                            <th>Jumlah Peserta</th>
                            <th>Penilaian</th>
                            <th>Detail Peserta</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->tgl_ujian}}</td>
                                <td>{{$value->nama_periode}}</td>
                                <td>{{$value->kuota}}</td>
                                <td>{{$value->jml_peserta}}</td>
                                <td>
                                    @if (!empty($isHistory))
                                        <span class="label label-primary">Terkonfirmasi: {{ (int) ($value->total_terkonfirmasi ?? 0) }}</span>
                                    @else
                                        <span class="label label-success">Lengkap: {{ (int) ($value->total_penilaian_lengkap ?? 0) }}</span>
                                        <span class="label label-danger">Tidak Lengkap: {{ (int) ($value->total_penilaian_tidak_lengkap ?? 0) }}</span>
                                    @endif
                                </td>
                                {{-- <<td>{{$value->status == 0 ? "td>{{$value->status == 0 ? "<td>{{$d->status == 0 ? "Belum terlaksana" : "Terlaksana"}}</td>" : "Terlaksana"}}</td>" : "Terlaksana"}}</td> --}}
                                <td>
                                    @if (!empty($isHistory))
                                        <a href="{{ url('prodi/detail_hasilujian_proposal_history/'.$value->pendaftaran_id) }}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a>
                                    @else
                                        <a href="{{ url('prodi/detail_hasilujian_proposal/'.$value->pendaftaran_id) }}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a>
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

@section('script')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable && $('#datatable-example').length > 0) {
                if ($.fn.dataTable.isDataTable('#datatable-example')) {
                    $('#datatable-example').DataTable().destroy();
                }

                $('#datatable-example').DataTable({
                    paging: false,
                    info: false,
                    lengthChange: false
                });
            }
        });
    </script>
@endsection
