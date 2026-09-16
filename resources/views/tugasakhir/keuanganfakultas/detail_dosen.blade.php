@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ route('report_dosen_home') }}">Laporan Dosen</a></li>
                <li class="active">Laporan Harian</li>
            </ol>

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <h3 class="page-heading">Laporan Honorarium Harian <b>{{ helper::getDeskripsi($nidn) }}</b></h3>
            <div class="the-box">
                @php
                    $totalTersedia = $reportHarian->sum('available_total');
                    $totalBelumTersedia = $reportHarian->sum('unavailable_total');
                    $jumlahMahasiswa = $reportHarian->flatMap(function ($report) {
                        return $report->items->pluck('C_NPM');
                    })->filter()->unique()->count();
                @endphp

                <div class="daily-report-toolbar">
                    <p class="text-muted">Setiap baris mewakili satu tanggal ujian. Rincian mahasiswa dan peran dosen tersedia pada tombol Detail.</p>
                    <a href="{{ route('report_dosen_history', $nidn) }}" class="btn btn-primary">
                        <i class="fa fa-history"></i> Riwayat Pembayaran
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                            <tr>
                                <th class="daily-report-number">No</th>
                                <th>Tanggal Ujian</th>
                                <th>Mahasiswa</th>
                                <th>Status Honorarium</th>
                                <th>Total Honorarium</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reportHarian as $report)
                                @php
                                    $modalId = 'daily-report-' . preg_replace('/[^a-zA-Z0-9_-]/', '', $report->date);
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>
                                            {{ $report->date === 'tanpa-tanggal' ? 'Tanggal belum tersedia' : \Carbon\Carbon::parse($report->date)->format('d/m/Y') }}
                                        </strong>
                                    </td>
                                    <td><strong>{{ $report->student_count }}</strong> mahasiswa</td>
                                    <td>
                                        @if ($report->available_count > 0)
                                            <span class="badge badge-success">{{ $report->available_count }} tersedia</span>
                                        @endif
                                        @if ($report->unavailable_count > 0)
                                            <span class="badge badge-warning">{{ $report->unavailable_count }} belum tersedia</span>
                                        @endif
                                        @if ($report->unset_count > 0)
                                            <span class="badge badge-danger">{{ $report->unset_count }} tipe belum ditetapkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($report->available_count + $report->unavailable_count > 0)
                                            <strong>{{ helper::formatRupiah($report->total_amount) }}</strong>
                                        @else
                                            <span class="text-muted">Belum dapat dihitung</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#{{ $modalId }}">
                                            <i class="fa fa-list"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($reportHarian->isEmpty())
                    <div class="alert alert-info square daily-report-empty">Belum ada honorarium aktif untuk dosen ini.</div>
                @endif

                <div class="daily-report-summary">
                    <div>
                        <small>Tanggal Ujian</small>
                        <strong>{{ $reportHarian->count() }} hari</strong>
                    </div>
                    <div>
                        <small>Mahasiswa Unik</small>
                        <strong>{{ $jumlahMahasiswa }} mahasiswa</strong>
                    </div>
                    <div>
                        <small>Honorarium Tersedia</small>
                        <strong>{{ helper::formatRupiah($totalTersedia) }}</strong>
                    </div>
                    <div>
                        <small>Honorarium Belum Tersedia</small>
                        <strong>{{ helper::formatRupiah($totalBelumTersedia) }}</strong>
                    </div>
                </div>
            </div>

            @foreach ($reportHarian as $report)
                @php
                    $modalId = 'daily-report-' . preg_replace('/[^a-zA-Z0-9_-]/', '', $report->date);
                @endphp
                <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}-title">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="{{ $modalId }}-title">
                                    Rincian Honorarium {{ $report->date === 'tanpa-tanggal' ? 'Tanpa Tanggal' : \Carbon\Carbon::parse($report->date)->format('d/m/Y') }}
                                </h4>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped daily-report-detail">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Mahasiswa</th>
                                                <th>Peran Dosen</th>
                                                <th>Tipe Ujian</th>
                                                <th>Status</th>
                                                <th>Honorarium</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($report->items as $honorarium)
                                                @php
                                                    $tipeBelumDitetapkan = trim((string) $honorarium->tipe_ujian) === ''
                                                        || in_array((string) $honorarium->tipe_ujian, ['0', '2'], true);
                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <strong>{{ $honorarium->nama_mahasiswa }}</strong>
                                                        <small class="text-muted daily-report-nim">{{ $honorarium->C_NPM }}</small>
                                                    </td>
                                                    <td>{{ $honorarium->role }}</td>
                                                    <td>
                                                        @if ($tipeBelumDitetapkan)
                                                            <span class="text-muted">Belum ditetapkan</span>
                                                        @else
                                                            {{ $honorarium->tipe_ujian }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($tipeBelumDitetapkan)
                                                            <span class="badge badge-danger">Perlu penetapan</span>
                                                        @elseif ((int) $honorarium->status === 1)
                                                            <span class="badge badge-success">Tersedia</span>
                                                        @elseif ((int) $honorarium->status === 0)
                                                            <span class="badge badge-warning">Belum tersedia</span>
                                                        @else
                                                            <span class="badge badge-default">Tidak diketahui</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($tipeBelumDitetapkan)
                                                            <span class="text-muted">-</span>
                                                        @else
                                                            {{ helper::formatRupiah($honorarium->amount) }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
    <style>
        .daily-report-toolbar {
            align-items: center;
            display: flex;
            gap: 15px;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .daily-report-toolbar p {
            margin: 0;
        }

        .daily-report-number {
            width: 54px;
        }

        .daily-report-summary {
            border-top: 1px solid #e5e9ed;
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(4, minmax(150px, 1fr));
            margin-top: 18px;
            padding-top: 18px;
        }

        .daily-report-summary > div {
            background: #f7f9fb;
            border-left: 3px solid #2f6f91;
            padding: 10px 12px;
        }

        .daily-report-summary small,
        .daily-report-summary strong,
        .daily-report-nim {
            display: block;
        }

        .daily-report-summary strong {
            margin-top: 4px;
        }

        .daily-report-detail th {
            white-space: nowrap;
        }

        .daily-report-nim {
            margin-top: 3px;
        }

        .daily-report-empty {
            margin-top: 16px;
        }

        @media (max-width: 767px) {
            .daily-report-toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .daily-report-summary {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection
