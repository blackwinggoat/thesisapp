@extends('tugasakhir.index')
@section('isi')
    @php
        $honorariumMode = $honorariumMode ?? 'keuangan';
        $isAkademikHonorarium = $honorariumMode === 'akademik';
        $homeRoute = $isAkademikHonorarium ? 'honorarium_penetapan_home' : 'honorarium_home';
        $detailRoute = $isAkademikHonorarium ? 'honorarium_penetapan_detail_tanggal' : 'honorarium_detail_tanggal';
    @endphp
    <style>
        .honorarium-availability-cell {
            min-width: 145px;
        }

        .honorarium-availability-state {
            display: block;
            margin-top: 5px;
            color: #64748b;
            font-size: 11px;
            line-height: 1.3;
        }

        .honorarium-availability-state.is-available {
            color: #15803d;
        }

        .honorarium-availability-state.is-locked {
            color: #b45309;
        }

        .honorarium-selected-availability-toggle {
            display: inline-block;
            vertical-align: middle;
        }

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
    </style>
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">{{ $isAkademikHonorarium ? 'Verifikasi Honorarium' : 'Honorarium' }}</li>
            </ol>

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <div class="clearfix">
                <h3 class="page-heading pull-left">
                    {{ $isAkademikHonorarium ? 'Verifikasi Honorarium per Tanggal Ujian' : 'Manajemen Honorarium per Tanggal Ujian' }}
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
                        <div class="honorarium-toolbar" style="margin-bottom: 15px;">
                            <button type="submit" class="btn btn-success" id="mark-honorarium-paid" disabled>
                                <i class="fa fa-check-circle"></i> Tandai Terbayar
                            </button>
                            <button type="submit" class="btn btn-danger" id="download-honorarium-pdf"
                                formaction="{{ route('honorarium_tanda_terima_pdf') }}" formtarget="_blank" disabled>
                                <i class="fa fa-file-pdf-o"></i> Tanda Terima Dosen
                            </button>
                            <button type="submit" class="btn btn-primary" id="download-honorarium-daily-recap"
                                formaction="{{ route('honorarium_rekap_harian_pdf') }}" formtarget="_blank" disabled>
                                <i class="fa fa-file-pdf-o"></i> Rekap Honorarium Harian
                            </button>
                            <input type="checkbox"
                                id="selected-dates-availability-toggle"
                                data-toggle="toggle"
                                data-width="174"
                                data-on="Dana Tersedia"
                                data-off="Dana Belum Tersedia"
                                data-onstyle="success"
                                data-offstyle="warning"
                                data-style="honorarium-selected-availability-toggle"
                                data-current-state="0"
                                disabled>
                            <span class="honorarium-selected-count" id="honorarium-selected-count">0 tanggal dipilih</span>
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
                                <th class="text-center">Type Mahasiswa</th>
                                @if (!$isAkademikHonorarium)
                                    <th class="text-center">Total Honor Belum Dibayar</th>
                                    <th class="text-center">Ketersediaan Dana</th>
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
                                    <td class="text-center">
                                        <span class="label label-default">Reguler: {{ $honorarium->total_reguler }}</span>
                                        <div style="margin-top: 5px;">
                                            <span class="label label-primary">Eksekutif: {{ $honorarium->total_eksekutif }}</span>
                                        </div>
                                    </td>
                                    @if (!$isAkademikHonorarium)
                                        <td class="text-right"><strong>{{ helper::formatRupiah($honorarium->total_honor) }}</strong></td>
                                        @php
                                            $seluruhDanaTersedia = (int) $honorarium->belum_tersedia === 0;
                                            $ketersediaanTerkunci = (int) $honorarium->terkunci_pembayaran > 0;
                                        @endphp
                                        <td class="text-center honorarium-availability-cell">
                                            <input type="checkbox"
                                                class="honorarium-date-availability-toggle"
                                                data-toggle="toggle"
                                                data-size="small"
                                                data-width="118"
                                                data-on="Tersedia"
                                                data-off="Belum tersedia"
                                                data-onstyle="success"
                                                data-offstyle="warning"
                                                data-date="{{ $honorarium->date }}"
                                                data-url="{{ route('honorarium_update_date_availability', $honorarium->date) }}"
                                                data-needs-type="{{ (int) $honorarium->perlu_penetapan }}"
                                                data-current-state="{{ $seluruhDanaTersedia ? 1 : 0 }}"
                                                {{ $seluruhDanaTersedia ? 'checked' : '' }}
                                                {{ $ketersediaanTerkunci ? 'disabled' : '' }}>
                                            <span class="honorarium-availability-state {{ $seluruhDanaTersedia ? 'is-available' : '' }} {{ $ketersediaanTerkunci ? 'is-locked' : '' }}">
                                                @if ($ketersediaanTerkunci)
                                                    Terkunci karena sebagian honor telah dibayar
                                                @elseif ($seluruhDanaTersedia)
                                                    Seluruh data tersedia
                                                @else
                                                    {{ $honorarium->belum_tersedia }} data belum tersedia
                                                @endif
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
                                    <td colspan="{{ $isAkademikHonorarium ? 6 : 9 }}" class="text-center">Tidak ada honorarium yang masih perlu dikelola.</td>
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
                    { orderable: false, targets: {!! $isAkademikHonorarium ? '[5]' : '[0, 8]' !!} }
                ]
            });

            function syncAvailabilityToggle(toggle, checked) {
                if (toggle.prop('checked') === checked) {
                    return;
                }
                toggle.bootstrapToggle(checked ? 'on' : 'off', true);
            }

            $('.honorarium-date-availability-toggle').bootstrapToggle();
            $('#selected-dates-availability-toggle').bootstrapToggle();
            $(document).off('change.honorariumDateAvailability', '.honorarium-date-availability-toggle')
                .on('change.honorariumDateAvailability', '.honorarium-date-availability-toggle', function() {
                    var toggle = $(this);
                    var previousState = parseInt(toggle.attr('data-current-state'), 10) === 1;
                    var requestedState = toggle.prop('checked');
                    var date = toggle.data('date');
                    var needsType = parseInt(toggle.data('needs-type'), 10) || 0;

                    if (requestedState && needsType > 0) {
                        syncAvailabilityToggle(toggle, previousState);
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tipe honor belum lengkap',
                            text: 'Tetapkan seluruh tipe honorarium tanggal ini sebelum dana dibuat tersedia.'
                        });
                        return;
                    }

                    syncAvailabilityToggle(toggle, previousState);
                    Swal.fire({
                        icon: 'question',
                        title: 'Ubah ketersediaan dana?',
                        text: 'Seluruh data honorarium tanggal ' + date + ' akan menjadi ' +
                            (requestedState ? 'Tersedia.' : 'Belum tersedia.'),
                        showCancelButton: true,
                        confirmButtonColor: requestedState ? '#16a34a' : '#d97706',
                        confirmButtonText: 'Ya, ubah',
                        cancelButtonText: 'Batal'
                    }).then(function(result) {
                        if (!result.value) {
                            return;
                        }

                        toggle.bootstrapToggle('disable');
                        $.ajax({
                            url: toggle.data('url'),
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                available: requestedState ? 1 : 0
                            },
                            success: function(response) {
                                toggle.attr('data-current-state', response.available ? '1' : '0');
                                var state = toggle.closest('td').find('.honorarium-availability-state');
                                state.toggleClass('is-available', !!response.available)
                                    .text(response.available ? 'Seluruh data tersedia' : 'Seluruh data belum tersedia');
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                            },
                            error: function(xhr) {
                                var response = xhr.responseJSON || {};
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Tidak dapat diubah',
                                    text: response.message || 'Ketersediaan dana gagal diubah.'
                                });
                            },
                            complete: function() {
                                toggle.bootstrapToggle('enable');
                                syncAvailabilityToggle(
                                    toggle,
                                    parseInt(toggle.attr('data-current-state'), 10) === 1
                                );
                            }
                        });
                    });
                });

            function updateSelectedDates() {
                var selected = $('.honorarium-date-checkbox:checked').length;
                var total = $('.honorarium-date-checkbox').length;
                var bulkToggle = $('#selected-dates-availability-toggle');

                $('#honorarium-selected-count').text(selected + ' tanggal dipilih');
                $('#download-honorarium-pdf').prop('disabled', selected === 0);
                $('#download-honorarium-daily-recap').prop('disabled', selected === 0);
                $('#mark-honorarium-paid').prop('disabled', selected === 0);
                bulkToggle.bootstrapToggle('enable');
                if (selected === 0) {
                    bulkToggle.attr('data-current-state', '0');
                    syncAvailabilityToggle(bulkToggle, false);
                    bulkToggle.bootstrapToggle('disable');
                } else {
                    var seluruhTanggalTersedia = true;
                    $('.honorarium-date-checkbox:checked').each(function() {
                        var rowToggle = $(this).closest('tr').find('.honorarium-date-availability-toggle');
                        if (parseInt(rowToggle.attr('data-current-state'), 10) !== 1) {
                            seluruhTanggalTersedia = false;
                        }
                    });
                    bulkToggle.attr('data-current-state', seluruhTanggalTersedia ? '1' : '0');
                    syncAvailabilityToggle(bulkToggle, seluruhTanggalTersedia);
                }
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
                $('#selected-dates-availability-toggle').off('change.honorariumSelectedAvailability')
                    .on('change.honorariumSelectedAvailability', function() {
                        var bulkToggle = $(this);
                        var previousState = parseInt(bulkToggle.attr('data-current-state'), 10) === 1;
                        var requestedState = bulkToggle.prop('checked');
                        var selectedRows = $('.honorarium-date-checkbox:checked').closest('tr');
                        var selectedDates = $('.honorarium-date-checkbox:checked').map(function() {
                            return $(this).val();
                        }).get();

                        syncAvailabilityToggle(bulkToggle, previousState);
                        if (selectedDates.length === 0) {
                            updateSelectedDates();
                            return;
                        }

                        if (requestedState && selectedRows.find('.honorarium-date-availability-toggle').filter(function() {
                            return (parseInt($(this).data('needs-type'), 10) || 0) > 0;
                        }).length > 0) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Tipe honor belum lengkap',
                                text: 'Tetapkan seluruh tipe honorarium pada tanggal yang dipilih sebelum dana dibuat tersedia.'
                            });
                            return;
                        }

                        Swal.fire({
                            icon: 'question',
                            title: 'Ubah seluruh dana terpilih?',
                            text: selectedDates.length + ' tanggal yang dipilih akan menjadi ' +
                                (requestedState ? 'Tersedia.' : 'Belum tersedia.'),
                            showCancelButton: true,
                            confirmButtonColor: requestedState ? '#16a34a' : '#d97706',
                            confirmButtonText: 'Ya, ubah',
                            cancelButtonText: 'Batal'
                        }).then(function(result) {
                            if (!result.value) {
                                return;
                            }

                            bulkToggle.bootstrapToggle('disable');
                            $.ajax({
                                url: "{{ route('honorarium_update_selected_availability') }}",
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    available: requestedState ? 1 : 0,
                                    tanggal: selectedDates
                                },
                                success: function(response) {
                                    selectedRows.each(function() {
                                        var rowToggle = $(this).find('.honorarium-date-availability-toggle');
                                        rowToggle.attr('data-current-state', response.available ? '1' : '0');
                                        syncAvailabilityToggle(rowToggle, !!response.available);
                                        $(this).find('.honorarium-availability-state')
                                            .toggleClass('is-available', !!response.available)
                                            .text(response.available ? 'Seluruh data tersedia' : 'Seluruh data belum tersedia');
                                    });
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil',
                                        text: response.message
                                    });
                                },
                                error: function(xhr) {
                                    var response = xhr.responseJSON || {};
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Tidak dapat diubah',
                                        text: response.message || 'Ketersediaan dana terpilih gagal diubah.'
                                    });
                                },
                                complete: function() {
                                    updateSelectedDates();
                                }
                            });
                        });
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
