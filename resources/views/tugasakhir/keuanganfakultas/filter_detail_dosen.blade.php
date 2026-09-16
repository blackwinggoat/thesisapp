@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ route('report_dosen_home') }}">Laporan Dosen</a></li>
                <li class="active">Rentang Tanggal</li>
            </ol>

            <h3 class="page-heading">Laporan Honorarium <b>{{ helper::getDeskripsi($nidn) }}</b></h3>
            <div class="the-box">
                @php
                    $dataDitetapkan = $data->filter(function ($item) {
                        return $item->tipe_ditetapkan;
                    });
                    $totalDasar = $dataDitetapkan->sum('base_amount');
                    $totalPenyesuaian = $dataDitetapkan->sum('adjustment_amount');
                    $totalAkhir = $dataDitetapkan->sum('amount');
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

                <div class="range-report-heading">
                    <p>
                        <strong>{{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}</strong>
                        sampai
                        <strong>{{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}</strong>
                    </p>
                    <small class="text-muted">Honor akhir sudah memperhitungkan penyesuaian kehadiran pembimbing.</small>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="range-dosen-table">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Ujian</th>
                                <th>Mahasiswa</th>
                                <th>Peran</th>
                                <th>Status</th>
                                <th class="text-right">Honor Dasar</th>
                                <th class="text-right">Penyesuaian</th>
                                <th class="text-right">Honor Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $honorarium)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td data-order="{{ $honorarium->date }}">{{ \Carbon\Carbon::parse($honorarium->date)->format('d/m/Y') }}</td>
                                    <td>
                                        <strong>{{ $honorarium->nama_mahasiswa }}</strong>
                                        <small class="text-muted range-report-nim">{{ $honorarium->C_NPM }}</small>
                                    </td>
                                    <td>
                                        {{ $honorarium->role }}
                                        <small class="text-muted range-report-type">{{ $honorarium->tipe_ditetapkan ? $honorarium->tipe_ujian : 'Tipe belum ditetapkan' }}</small>
                                    </td>
                                    <td>
                                        @if (!$honorarium->tipe_ditetapkan)
                                            <span class="label label-danger">Perlu penetapan</span>
                                        @elseif ((int) $honorarium->status === 3)
                                            <span class="label label-primary">Sudah diterima</span>
                                        @elseif ((int) $honorarium->status === 1)
                                            <span class="label label-success">Tersedia</span>
                                        @else
                                            <span class="label label-warning">Belum tersedia</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $honorarium->tipe_ditetapkan ? helper::formatRupiah($honorarium->base_amount) : '-' }}</td>
                                    <td class="text-right">
                                        @if ($honorarium->tipe_ditetapkan)
                                            <span class="{{ $honorarium->adjustment_amount < 0 ? 'text-danger' : ($honorarium->adjustment_amount > 0 ? 'text-success' : 'text-muted') }}">
                                                {{ $formatPenyesuaian($honorarium->adjustment_amount) }}
                                            </span>
                                            @if ($honorarium->adjustment_note !== '')
                                                <small class="text-muted range-report-adjustment-note">{{ $honorarium->adjustment_note }}</small>
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

                <div class="range-report-summary">
                    <span>Honor Dasar <strong>{{ helper::formatRupiah($totalDasar) }}</strong></span>
                    <span>Penyesuaian <strong class="{{ $totalPenyesuaian < 0 ? 'text-danger' : ($totalPenyesuaian > 0 ? 'text-success' : '') }}">{{ $formatPenyesuaian($totalPenyesuaian) }}</strong></span>
                    <span>Honor Akhir <strong>{{ helper::formatRupiah($totalAkhir) }}</strong></span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        .range-report-heading {
            margin-bottom: 18px;
        }

        .range-report-heading p {
            margin-bottom: 4px;
        }

        .range-report-nim,
        .range-report-type,
        .range-report-adjustment-note {
            display: block;
            margin-top: 3px;
        }

        .range-report-summary {
            border-top: 1px solid #e5e9ed;
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            justify-content: flex-end;
            margin-top: 18px;
            padding-top: 15px;
        }

        .range-report-summary span {
            white-space: nowrap;
        }

        .range-report-summary strong {
            margin-left: 5px;
        }
    </style>
    <script>
        $(function() {
            $('#range-dosen-table').DataTable({
                order: [[1, 'desc']],
                pageLength: 25,
                columnDefs: [
                    { orderable: false, targets: [0, 6] }
                ],
                language: {
                    search: 'Cari:',
                    zeroRecords: 'Honorarium tidak ditemukan pada rentang tanggal ini'
                }
            });
        });
    </script>
@endsection
