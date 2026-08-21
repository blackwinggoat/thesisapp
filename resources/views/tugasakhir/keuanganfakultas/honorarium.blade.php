@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading">Sistem Informasi Program Studi <small>TUGAS AKHIR</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">Honorarium</li>
            </ol>

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <div class="clearfix">
                <h3 class="page-heading pull-left">Manajemen Honorarium per Tanggal Ujian</h3>
                <a href="{{ route('honorarium_history') }}" class="btn btn-primary pull-right" style="margin-top: 20px;">
                    <i class="fa fa-history"></i> Riwayat Pembayaran
                </a>
            </div>

            @if ($belumTerhubungJadwal > 0)
                <div class="alert alert-warning square">
                    <i class="fa fa-calendar-times-o"></i>
                    {{ $belumTerhubungJadwal }} data honorarium belum terhubung ke jadwal ujian dan tidak ditampilkan pada tanggal yang keliru.
                </div>
            @endif

            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-hover" id="honorarium-date-table">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Ujian</th>
                                <th class="text-center">Jumlah Mahasiswa</th>
                                <th class="text-center">Total Honor Belum Dibayar</th>
                                <th class="text-center">Belum Tersedia</th>
                                <th class="text-center">Perlu Penetapan Tipe</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $honorarium)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $honorarium->date }}</td>
                                    <td class="text-center">
                                        <span class="label label-info">{{ $honorarium->total_mahasiswa }} mahasiswa</span>
                                        <div class="text-muted" style="margin-top: 5px; white-space: nowrap;">
                                            TI: {{ $honorarium->total_teknik_informatika }} &nbsp; SI: {{ $honorarium->total_sistem_informasi }}
                                        </div>
                                    </td>
                                    <td class="text-right"><strong>{{ helper::formatRupiah($honorarium->total_honor) }}</strong></td>
                                    <td class="text-center">
                                        <span class="label {{ $honorarium->belum_tersedia > 0 ? 'label-warning' : 'label-success' }}">
                                            {{ $honorarium->belum_tersedia }} data
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="label {{ $honorarium->perlu_penetapan > 0 ? 'label-danger' : 'label-success' }}">
                                            {{ $honorarium->perlu_penetapan }} data
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('honorarium_detail_tanggal', $honorarium->date) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-users"></i> Kelola Mahasiswa
                                        </a>
                                        <a href="{{ route('honorarium_tanda_terima_pdf', $honorarium->date) }}" class="btn btn-danger btn-sm"
                                            title="Download tanda terima honorarium PDF" aria-label="Download tanda terima honorarium PDF">
                                            <i class="fa fa-file-pdf-o"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada honorarium yang masih perlu dikelola.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            var tableSelector = '#honorarium-date-table';
            if ($.fn.dataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().destroy();
            }

            $(tableSelector).DataTable({
                order: [[1, 'desc']],
                paging: false,
                info: false,
                lengthChange: false
            });
        });
    </script>
@endsection
