@extends('tugasakhir.index')
@section('isi')
    <style>
        .honorarium-toolbar {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            margin: -4px;
        }

        .honorarium-toolbar > * {
            margin: 4px;
        }

        .honorarium-toolbar .honorarium-selected-count {
            color: #64748b;
            font-size: 12px;
        }

        .honorarium-report-actions {
            display: inline-flex;
            flex-wrap: wrap;
        }

        .honorarium-report-actions .btn + .btn {
            margin-left: 6px;
        }

        @media (max-width: 767px) {
            .honorarium-report-actions {
                display: flex;
                width: 100%;
            }

            .honorarium-report-actions .btn {
                flex: 1 1 0;
                white-space: normal;
            }
        }
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ route('honorarium_home') }}">Honorarium</a></li>
                <li class="active">Riwayat Pembayaran</li>
            </ol>

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <div class="clearfix">
                <h3 class="page-heading pull-left">Riwayat Honorarium per Tanggal Ujian</h3>
                <a href="{{ route('honorarium_home') }}" class="btn btn-primary pull-right" style="margin-top: 20px;">
                    <i class="fa fa-arrow-left"></i> Kembali ke Honorarium
                </a>
            </div>

            <div class="the-box">
                <form action="{{ route('honorarium_kembalikan_belum_terbayar') }}" method="POST" id="honorarium-history-form">
                    @csrf
                    <div class="honorarium-toolbar" style="margin-bottom: 15px;">
                        <button type="submit" class="btn btn-warning" id="restore-honorarium-unpaid" disabled>
                            <i class="fa fa-undo"></i> Kembalikan ke Belum Terbayar
                        </button>
                        <button type="submit" class="btn btn-danger" id="download-history-honorarium-pdf"
                            formaction="{{ route('honorarium_history_tanda_terima_pdf') }}" formtarget="_blank" disabled>
                            <i class="fa fa-file-pdf-o"></i> Tanda Terima Dosen
                        </button>
                        <div class="honorarium-report-actions" role="group" aria-label="Unduh rekap honorarium terbayar">
                            <button type="submit" class="btn btn-danger" id="download-history-honorarium-daily-recap"
                                formaction="{{ route('honorarium_history_rekap_harian_pdf') }}" formtarget="_blank" disabled>
                                <i class="fa fa-file-pdf-o"></i> Rekap Honorarium Harian
                            </button>
                            <button type="submit" class="btn btn-danger" id="download-history-honorarium-tax-recap"
                                formaction="{{ route('honorarium_history_rekap_pajak_pdf') }}" formtarget="_blank" disabled>
                                <i class="fa fa-file-pdf-o"></i> Rekap Pajak Honorarium
                            </button>
                        </div>
                        <span class="honorarium-selected-count" id="honorarium-history-selected-count">0 tanggal dipilih</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover" id="honorarium-history-table">
                        <thead class="the-box dark full">
                            <tr>
                                <th class="text-center" style="width: 45px;">
                                    <input type="checkbox" id="select-all-history-honorarium" title="Pilih semua tanggal">
                                </th>
                                <th>No</th>
                                <th>Tanggal Ujian</th>
                                <th class="text-center">Jumlah Mahasiswa</th>
                                <th class="text-center">Total Honor Terbayar</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $honorarium)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="history-honorarium-checkbox" name="tanggal[]" value="{{ $honorarium->date }}" aria-label="Pilih tanggal {{ $honorarium->date }}">
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $honorarium->date }}</td>
                                    <td class="text-center">
                                        <span class="label label-info">{{ $honorarium->total_mahasiswa }} mahasiswa</span>
                                        <div class="text-muted" style="margin-top: 5px; white-space: nowrap;">
                                            TI: {{ $honorarium->total_teknik_informatika }} &nbsp; SI: {{ $honorarium->total_sistem_informasi }}
                                        </div>
                                    </td>
                                    <td class="text-right"><strong>{{ helper::formatRupiah($honorarium->total_honor) }}</strong></td>
                                    <td class="text-center"><span class="label label-success">Terbayar</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('honorarium_history_detail_tanggal', $honorarium->date) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-eye"></i> Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada riwayat honorarium yang telah dibayar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            var tableSelector = '#honorarium-history-table';
            if ($.fn.dataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().destroy();
            }

            $(tableSelector).DataTable({
                order: [[2, 'desc']],
                paging: false,
                info: false,
                lengthChange: false,
                columnDefs: [
                    { orderable: false, targets: [0, 6] }
                ]
            });

            function updateSelectedHistoryDates() {
                var selected = $('.history-honorarium-checkbox:checked').length;
                var total = $('.history-honorarium-checkbox').length;

                $('#honorarium-history-selected-count').text(selected + ' tanggal dipilih');
                $('#restore-honorarium-unpaid').prop('disabled', selected === 0);
                $('#download-history-honorarium-pdf').prop('disabled', selected === 0);
                $('#download-history-honorarium-daily-recap').prop('disabled', selected === 0);
                $('#download-history-honorarium-tax-recap').prop('disabled', selected === 0);
                $('#select-all-history-honorarium')
                    .prop('checked', total > 0 && selected === total)
                    .prop('indeterminate', selected > 0 && selected < total);
            }

            $(document).on('change', '.history-honorarium-checkbox', updateSelectedHistoryDates);
            $('#select-all-history-honorarium').on('change', function() {
                $('.history-honorarium-checkbox').prop('checked', $(this).prop('checked'));
                updateSelectedHistoryDates();
            });
            $('#honorarium-history-form').on('submit', function(event) {
                if ($('.history-honorarium-checkbox:checked').length === 0) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Pilih tanggal',
                        text: 'Pilih minimal satu tanggal ujian.'
                    });
                }
            });
            $('#restore-honorarium-unpaid').on('click', function(event) {
                if ($('.history-honorarium-checkbox:checked').length === 0) {
                    return;
                }

                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Kembalikan ke belum terbayar?',
                    text: 'Data pada tanggal yang dipilih akan kembali berstatus Available dan muncul lagi di halaman Honorarium.',
                    showCancelButton: true,
                    confirmButtonColor: '#f0ad4e',
                    confirmButtonText: 'Ya, kembalikan',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (result.value) {
                        document.getElementById('honorarium-history-form').submit();
                    }
                });
            });
            datatable.on('draw', updateSelectedHistoryDates);
            updateSelectedHistoryDates();
        });
    </script>
@endsection
