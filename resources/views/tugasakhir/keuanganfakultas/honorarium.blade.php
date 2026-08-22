@extends('tugasakhir.index')
@section('isi')
    @php
        $honorariumMode = $honorariumMode ?? 'keuangan';
        $isAkademikHonorarium = $honorariumMode === 'akademik';
        $homeRoute = $isAkademikHonorarium ? 'honorarium_penetapan_home' : 'honorarium_home';
        $detailRoute = $isAkademikHonorarium ? 'honorarium_penetapan_detail_tanggal' : 'honorarium_detail_tanggal';
    @endphp
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading">Sistem Informasi Program Studi <small>TUGAS AKHIR</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">{{ $isAkademikHonorarium ? 'Penetapan Honorarium' : 'Honorarium' }}</li>
            </ol>

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <div class="clearfix">
                <h3 class="page-heading pull-left">
                    {{ $isAkademikHonorarium ? 'Penetapan Tipe Honorarium per Tanggal Ujian' : 'Manajemen Honorarium per Tanggal Ujian' }}
                </h3>
                @if (!$isAkademikHonorarium)
                    <a href="{{ route('honorarium_history') }}" class="btn btn-primary pull-right" style="margin-top: 20px;">
                        <i class="fa fa-history"></i> Riwayat Pembayaran
                    </a>
                @endif
            </div>

            @if ($belumTerhubungJadwal > 0)
                <div class="alert alert-warning square">
                    <i class="fa fa-calendar-times-o"></i>
                    {{ $belumTerhubungJadwal }} data honorarium belum terhubung ke jadwal ujian dan tidak ditampilkan pada tanggal yang keliru.
                </div>
            @endif

            <div class="the-box">
                <form action="{{ $isAkademikHonorarium ? route($homeRoute) : route('honorarium_tandai_terbayar') }}" method="{{ $isAkademikHonorarium ? 'GET' : 'POST' }}" id="honorarium-date-form">
                    @csrf
                    @if (!$isAkademikHonorarium)
                        <div class="clearfix" style="margin-bottom: 15px;">
                            <button type="submit" class="btn btn-success pull-left" id="mark-honorarium-paid" disabled>
                                <i class="fa fa-check-circle"></i> Tandai Terbayar
                            </button>
                            <button type="submit" class="btn btn-danger pull-left" id="download-honorarium-pdf"
                                formaction="{{ route('honorarium_tanda_terima_pdf') }}" formtarget="_blank"
                                style="margin-left: 8px;" disabled>
                                <i class="fa fa-file-pdf-o"></i> Download PDF Terpilih
                            </button>
                            <span class="text-muted pull-left" id="honorarium-selected-count" style="margin: 8px 0 0 10px;">0 tanggal dipilih</span>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-hover" id="honorarium-date-table">
                        <thead class="the-box dark full">
                            <tr>
                                @if (!$isAkademikHonorarium)
                                    <th class="text-center" style="width: 45px;">
                                        <input type="checkbox" id="select-all-honorarium-dates" title="Pilih semua tanggal">
                                    </th>
                                @endif
                                <th>No</th>
                                <th>Tanggal Ujian</th>
                                <th class="text-center">Jumlah Mahasiswa</th>
                                @if (!$isAkademikHonorarium)
                                    <th class="text-center">Total Honor Belum Dibayar</th>
                                    <th class="text-center">Belum Tersedia</th>
                                @endif
                                <th class="text-center">Perlu Penetapan Tipe</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $honorarium)
                                <tr>
                                    @if (!$isAkademikHonorarium)
                                        <td class="text-center">
                                            <input type="checkbox" class="honorarium-date-checkbox" name="tanggal[]" value="{{ $honorarium->date }}" aria-label="Pilih tanggal {{ $honorarium->date }}">
                                        </td>
                                    @endif
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $honorarium->date }}</td>
                                    <td class="text-center">
                                        <span class="label label-info">{{ $honorarium->total_mahasiswa }} mahasiswa</span>
                                        <div class="text-muted" style="margin-top: 5px; white-space: nowrap;">
                                            TI: {{ $honorarium->total_teknik_informatika }} &nbsp; SI: {{ $honorarium->total_sistem_informasi }}
                                        </div>
                                    </td>
                                    @if (!$isAkademikHonorarium)
                                        <td class="text-right"><strong>{{ helper::formatRupiah($honorarium->total_honor) }}</strong></td>
                                        <td class="text-center">
                                            <span class="label {{ $honorarium->belum_tersedia > 0 ? 'label-warning' : 'label-success' }}">
                                                {{ $honorarium->belum_tersedia }} data
                                            </span>
                                        </td>
                                    @endif
                                    <td class="text-center">
                                        <span class="label {{ $honorarium->perlu_penetapan > 0 ? 'label-danger' : 'label-success' }}">
                                            {{ $honorarium->perlu_penetapan }} data
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route($detailRoute, $honorarium->date) }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-users"></i> {{ $isAkademikHonorarium ? 'Tetapkan Tipe' : 'Kelola Mahasiswa' }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isAkademikHonorarium ? 5 : 8 }}" class="text-center">Tidak ada honorarium yang masih perlu dikelola.</td>
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
            var tableSelector = '#honorarium-date-table';
            if ($.fn.dataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().destroy();
            }

            var datatable = $(tableSelector).DataTable({
                order: [[{{ $isAkademikHonorarium ? 1 : 2 }}, 'desc']],
                paging: false,
                info: false,
                lengthChange: false,
                columnDefs: [
                    { orderable: false, targets: {!! $isAkademikHonorarium ? '[4]' : '[0, 7]' !!} }
                ]
            });

            function updateSelectedDates() {
                var selected = $('.honorarium-date-checkbox:checked').length;
                var total = $('.honorarium-date-checkbox').length;

                $('#honorarium-selected-count').text(selected + ' tanggal dipilih');
                $('#download-honorarium-pdf').prop('disabled', selected === 0);
                $('#mark-honorarium-paid').prop('disabled', selected === 0);
                $('#select-all-honorarium-dates')
                    .prop('checked', total > 0 && selected === total)
                    .prop('indeterminate', selected > 0 && selected < total);
            }

            @if (!$isAkademikHonorarium)
                $(document).on('change', '.honorarium-date-checkbox', updateSelectedDates);
                $('#select-all-honorarium-dates').on('change', function() {
                    $('.honorarium-date-checkbox').prop('checked', $(this).prop('checked'));
                    updateSelectedDates();
                });
                $('#honorarium-date-form').on('submit', function(event) {
                    if ($('.honorarium-date-checkbox:checked').length === 0) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Pilih tanggal',
                            text: 'Pilih minimal satu tanggal ujian.'
                        });
                    }
                });
                $('#mark-honorarium-paid').on('click', function(event) {
                    if ($('.honorarium-date-checkbox:checked').length === 0) {
                        return;
                    }

                    event.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Tandai seluruhnya terbayar?',
                        text: 'Semua honorarium Available pada tanggal yang dipilih akan menjadi Terbayar dan dipindahkan ke Riwayat Pembayaran.',
                        showCancelButton: true,
                        confirmButtonColor: '#28a745',
                        confirmButtonText: 'Ya, tandai terbayar',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (result.value) {
                            document.getElementById('honorarium-date-form').submit();
                        }
                    });
                });
                datatable.on('draw', updateSelectedDates);
                updateSelectedDates();
            @endif
        });
    </script>
@endsection
