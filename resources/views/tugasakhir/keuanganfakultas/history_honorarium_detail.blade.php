@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ route('honorarium_home') }}">Honorarium</a></li>
                <li><a href="{{ route('honorarium_history') }}">Riwayat Pembayaran</a></li>
                <li class="active">{{ $date }}</li>
            </ol>

            <div class="clearfix">
                <h3 class="page-heading pull-left">Riwayat Honorarium Tanggal {{ $date }}</h3>
                <a href="{{ route('honorarium_history') }}" class="btn btn-primary pull-right" style="margin-top: 20px;">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="alert alert-success square">
                <strong>Total Honor Terbayar:</strong>
                <span style="font-size: 18px; margin-left: 5px;">{{ helper::formatRupiah($data->sum('total_honor')) }}</span>
            </div>

            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-hover" id="honorarium-history-detail-table">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Program Studi</th>
                                <th>Tipe Ujian</th>
                                <th class="text-center">Total Honor</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $honorarium)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $honorarium->C_NPM }}</td>
                                    <td>{{ $honorarium->nama_mahasiswa }}</td>
                                    <td>
                                        @if (strpos($honorarium->C_NPM, '130') === 0)
                                            Teknik Informatika
                                        @elseif (strpos($honorarium->C_NPM, '131') === 0)
                                            Sistem Informasi
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $honorarium->tipe_ujian }}</td>
                                    <td class="text-right"><strong>{{ helper::formatRupiah($honorarium->total_honor) }}</strong></td>
                                    <td class="text-center"><span class="label label-success">Terbayar</span></td>
                                </tr>
                            @endforeach
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
            var tableSelector = '#honorarium-history-detail-table';
            if ($.fn.dataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().destroy();
            }

            $(tableSelector).DataTable({
                order: [[1, 'asc']],
                paging: false,
                info: false,
                lengthChange: false
            });
        });
    </script>
@endsection
