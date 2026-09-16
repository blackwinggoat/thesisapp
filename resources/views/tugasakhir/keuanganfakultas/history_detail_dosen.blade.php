@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ route('report_dosen_home') }}">Laporan Dosen</a></li>
                <li><a href="{{ route('report_dosen_detail', $nidn) }}">Rincian Harian</a></li>
                <li class="active">Riwayat Pembayaran</li>
            </ol>

            <h3 class="page-heading">Riwayat Honorarium <b>{{ helper::getDeskripsi($nidn) }}</b></h3>
            <div class="the-box">
                @php
                    $totalDasar = $data->filter(function ($item) {
                        return $item->tipe_ditetapkan;
                    })->sum('base_amount');
                    $totalPenyesuaian = $data->filter(function ($item) {
                        return $item->tipe_ditetapkan;
                    })->sum('adjustment_amount');
                    $totalDiterima = $data->filter(function ($item) {
                        return $item->tipe_ditetapkan;
                    })->sum('amount');
                    $formatPenyesuaian = function ($nilai) {
                        if ($nilai > 0) {
                            return '+' . helper::formatRupiah($nilai);
                        }
                        if ($nilai < 0) {
                            return '-' . helper::formatRupiah(abs($nilai));
                        }
                        return helper::formatRupiah(0);
                    };
                @endphp

                <p class="text-muted report-adjustment-help">Nominal diterima sudah memperhitungkan penyesuaian kehadiran pembimbing yang berlaku pada tanggal ujian.</p>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="history-dosen-table">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Ujian</th>
                                <th>Mahasiswa</th>
                                <th>Peran</th>
                                <th>Tipe Ujian</th>
                                <th class="text-right">Honor Dasar</th>
                                <th class="text-right">Penyesuaian</th>
                                <th class="text-right">Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $honorarium)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td data-order="{{ $honorarium->date }}">{{ \Carbon\Carbon::parse($honorarium->date)->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $honorarium->nama_mahasiswa }}</strong>
                                        <small class="text-muted report-student-nim">{{ $honorarium->C_NPM }}</small>
                                    </td>
                                    <td>{{ $honorarium->role }}</td>
                                    <td>{{ $honorarium->tipe_ditetapkan ? $honorarium->tipe_ujian : 'Belum ditetapkan' }}</td>
                                    <td class="text-right">{{ $honorarium->tipe_ditetapkan ? helper::formatRupiah($honorarium->base_amount) : '-' }}</td>
                                    <td class="text-right">
                                        @if ($honorarium->tipe_ditetapkan)
                                            <span class="{{ $honorarium->adjustment_amount < 0 ? 'text-danger' : ($honorarium->adjustment_amount > 0 ? 'text-success' : 'text-muted') }}">
                                                {{ $formatPenyesuaian($honorarium->adjustment_amount) }}
                                            </span>
                                            @if ($honorarium->adjustment_note !== '')
                                                <small class="text-muted report-adjustment-note">{{ $honorarium->adjustment_note }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right"><strong>{{ $honorarium->tipe_ditetapkan ? helper::formatRupiah($honorarium->amount) : '-' }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="report-total-summary">
                    <span>Honor Dasar <strong>{{ helper::formatRupiah($totalDasar) }}</strong></span>
                    <span>Penyesuaian <strong class="{{ $totalPenyesuaian < 0 ? 'text-danger' : ($totalPenyesuaian > 0 ? 'text-success' : '') }}">{{ $formatPenyesuaian($totalPenyesuaian) }}</strong></span>
                    <span>Total Diterima <strong>{{ helper::formatRupiah($totalDiterima) }}</strong></span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        .report-adjustment-help {
            margin-bottom: 18px;
        }

        .report-student-nim,
        .report-adjustment-note {
            display: block;
            margin-top: 3px;
        }

        .report-total-summary {
            border-top: 1px solid #e5e9ed;
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            justify-content: flex-end;
            margin-top: 18px;
            padding-top: 15px;
        }

        .report-total-summary span {
            white-space: nowrap;
        }

        .report-total-summary strong {
            margin-left: 5px;
        }
    </style>
    <script>
        $(function() {
            $('#history-dosen-table').DataTable({
                order: [[1, 'desc']],
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: [0, 6] }
                ],
                language: {
                    search: 'Cari:',
                    zeroRecords: 'Riwayat honorarium tidak ditemukan'
                }
            });
        });
    </script>
@endsection
