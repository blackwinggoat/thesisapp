@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li class="active">Laporan Dosen</li>
            </ol>

            <h3 class="page-heading">Laporan Honorarium Dosen</h3>
            <div class="the-box">
                <div class="lecturer-report-intro">
                    <p class="text-muted">
                        Total belum diterima = honor dasar + penyesuaian kehadiran pembimbing. Penugasan yang tipenya belum ditetapkan tidak dihitung.
                    </p>
                    <span class="label label-default">{{ $data->count() }} dosen</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="report-dosen-table">
                        <thead class="the-box dark full">
                            <tr>
                                <th class="lecturer-report-number">No</th>
                                <th>Dosen</th>
                                <th class="text-center lecturer-report-signature-column">Tanda Tangan</th>
                                <th class="text-right lecturer-report-amount-column">Honorarium Belum Diterima</th>
                                <th class="text-center lecturer-report-action-column">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $item->NAMA_DOSEN }}</strong>
                                        <small class="text-muted lecturer-report-code">{{ $item->C_KODE_DOSEN }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if ($item->tanda_tangan_data_uri !== '')
                                            <img src="{{ $item->tanda_tangan_data_uri }}"
                                                class="lecturer-report-signature"
                                                alt="Tanda tangan {{ $item->NAMA_DOSEN }}">
                                        @else
                                            <span class="lecturer-report-signature-empty">
                                                <i class="fa fa-pencil-square-o"></i>
                                                Belum tersedia
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-right" data-order="{{ $item->total_honorarium_belum_diterima }}">
                                        @if ($item->total_honorarium_belum_diterima > 0)
                                            <strong class="lecturer-report-amount">{{ helper::formatRupiah($item->total_honorarium_belum_diterima) }}</strong>
                                            <small class="lecturer-report-base">
                                                Dasar: {{ helper::formatRupiah($item->total_honorarium_dasar_belum_diterima) }}
                                            </small>
                                            @if ($item->total_penyesuaian_belum_diterima != 0)
                                                <small class="{{ $item->total_penyesuaian_belum_diterima > 0 ? 'lecturer-report-adjustment-positive' : 'lecturer-report-adjustment-negative' }}">
                                                    Penyesuaian:
                                                    {{ $item->total_penyesuaian_belum_diterima > 0 ? '+' : '-' }}{{ helper::formatRupiah(abs($item->total_penyesuaian_belum_diterima)) }}
                                                </small>
                                            @endif
                                        @elseif ($item->jumlah_penugasan_belum_ditetapkan > 0)
                                            <span class="text-muted">Belum dapat dihitung</span>
                                        @else
                                            <span class="text-muted">Rp 0</span>
                                        @endif
                                        @if ($item->jumlah_penugasan_belum_ditetapkan > 0)
                                            <small class="lecturer-report-unset">
                                                {{ $item->jumlah_penugasan_belum_ditetapkan }} penugasan menunggu tipe
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="lecturer-report-actions" role="group" aria-label="Aksi laporan dosen">
                                            <button type="button"
                                                class="btn btn-default btn-sm"
                                                data-toggle="modal"
                                                data-target="#customReportModal"
                                                data-id="{{ $item->C_KODE_DOSEN }}"
                                                data-name="{{ $item->NAMA_DOSEN }}">
                                                <i class="fa fa-calendar"></i> Rentang Tanggal
                                            </button>
                                            <a href="{{ route('report_dosen_detail', $item->C_KODE_DOSEN) }}" class="btn btn-primary btn-sm">
                                                <i class="fa fa-list"></i> Rincian Harian
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customReportModal" tabindex="-1" role="dialog" aria-labelledby="customReportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="customReportModalLabel">Laporan Berdasarkan Rentang Tanggal</h4>
                </div>
                <div class="modal-body">
                    <p class="text-muted" id="customReportLecturer"></p>
                    <form id="customReportForm">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="end_date">Tanggal Selesai</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="dosen_id" name="dosen_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="processReport">
                        <i class="fa fa-filter"></i> Tampilkan Laporan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <style>
        .lecturer-report-intro {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .lecturer-report-intro p {
            margin: 0;
        }

        .lecturer-report-number {
            width: 54px;
        }

        .lecturer-report-code {
            display: block;
            margin-top: 4px;
        }

        .lecturer-report-signature-column {
            width: 150px;
        }

        .lecturer-report-signature {
            display: inline-block;
            height: 48px;
            max-width: 125px;
            object-fit: contain;
            vertical-align: middle;
        }

        .lecturer-report-signature-empty {
            color: #8a9299;
            display: inline-block;
            font-size: 12px;
            line-height: 1.4;
        }

        .lecturer-report-signature-empty i {
            display: block;
            font-size: 18px;
            margin-bottom: 3px;
        }

        .lecturer-report-amount-column {
            width: 215px;
        }

        .lecturer-report-amount {
            color: #226b45;
            white-space: nowrap;
        }

        .lecturer-report-base,
        .lecturer-report-adjustment-positive,
        .lecturer-report-adjustment-negative {
            display: block;
            font-size: 11px;
            margin-top: 3px;
            white-space: nowrap;
        }

        .lecturer-report-base {
            color: #7a838b;
        }

        .lecturer-report-adjustment-positive {
            color: #226b45;
        }

        .lecturer-report-adjustment-negative {
            color: #a94442;
        }

        .lecturer-report-unset {
            color: #b26a00;
            display: block;
            margin-top: 4px;
        }

        .lecturer-report-action-column {
            width: 255px;
        }

        .lecturer-report-actions {
            display: inline-flex;
            gap: 6px;
        }

        @media (max-width: 767px) {
            .lecturer-report-intro {
                align-items: flex-start;
                flex-direction: column;
                gap: 8px;
            }

            .lecturer-report-actions {
                align-items: stretch;
                flex-direction: column;
            }
        }
    </style>
    <script>
        $(function() {
            var tableSelector = '#report-dosen-table';
            if ($.fn.dataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().destroy();
            }

            $(tableSelector).DataTable({
                order: [[1, 'asc']],
                paging: false,
                info: false,
                lengthChange: false,
                columnDefs: [
                    { orderable: false, targets: [0, 2, 4] }
                ],
                language: {
                    search: 'Cari dosen:',
                    zeroRecords: 'Data dosen tidak ditemukan'
                }
            });

            $('#customReportModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var modal = $(this);
                modal.find('#dosen_id').val(button.data('id'));
                modal.find('#customReportLecturer').text(button.data('name') + ' (' + button.data('id') + ')');
            });

            $('#processReport').on('click', function() {
                var dosenId = $('#dosen_id').val();
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();

                if (!startDate || !endDate) {
                    alert('Pilih tanggal mulai dan tanggal selesai.');
                    return;
                }

                if (startDate > endDate) {
                    alert('Tanggal mulai tidak boleh melewati tanggal selesai.');
                    return;
                }

                window.location.href = '/report/dosen/' + encodeURIComponent(dosenId)
                    + '/' + encodeURIComponent(startDate)
                    + '/' + encodeURIComponent(endDate);
            });
        });
    </script>
@endsection
