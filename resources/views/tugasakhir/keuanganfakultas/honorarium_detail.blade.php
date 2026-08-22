@extends('tugasakhir.index')
@section('isi')
    @php
        $honorariumMode = $honorariumMode ?? 'keuangan';
        $isAkademikHonorarium = $honorariumMode === 'akademik';
        $homeRoute = $isAkademikHonorarium ? 'honorarium_penetapan_home' : 'honorarium_home';
        $saveRoute = $isAkademikHonorarium ? 'honorarium_penetapan_save_all' : null;
    @endphp
    <style>
        .modal-table td {
            vertical-align: middle;
        }

        .modal-table .label {
            display: inline-block;
            width: 100%;
            text-align: center;
            padding: 0.3em;
        }

        .honorarium-total-summary {
            background: #166534;
            border-color: #14532d;
            color: #fff;
        }

        .honorarium-sk-list {
            max-width: 160px;
            line-height: 1.55;
            white-space: normal;
            word-break: break-word;
        }

        .honorarium-sk-label {
            display: inline-block;
            font-weight: 600;
        }

        .honorarium-student-class {
            display: block;
            margin-top: 5px;
        }

        .honorarium-advisor-attendance {
            max-width: 190px;
            white-space: normal;
        }

        .honorarium-advisor-attendance label {
            display: block;
            margin-bottom: 7px;
            font-weight: 400;
            line-height: 1.35;
        }

        .honorarium-advisor-attendance input[type="checkbox"] {
            margin-right: 6px;
        }

        .honorarium-attendance-status {
            display: block;
            min-height: 18px;
            color: #64748b;
            font-size: 11px;
            margin-top: 3px;
        }

        .honorarium-type-column {
            max-width: 250px;
            width: 250px;
            white-space: normal;
        }

        .honorarium-type-column select.form-control {
            max-width: 250px;
            width: 100%;
            font-size: 12px;
            padding-left: 6px;
            padding-right: 6px;
        }
    </style>
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
                <li><a href="{{ route($homeRoute) }}">{{ $isAkademikHonorarium ? 'Penetapan Honorarium' : 'Honorarium' }}</a></li>
                <li class="active">{{ $date }}</li>
            </ol>
            <!-- End breadcrumb -->

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <!-- BEGIN DATA TABLE -->
            <div class="clearfix">
                <h3 class="page-heading pull-left">{{ $isAkademikHonorarium ? 'Penetapan Honorarium' : 'Honorarium' }} Tanggal {{ $date }}</h3>
                <a href="{{ route($homeRoute) }}" class="btn btn-default pull-right" style="margin-top: 20px;">
                    <i class="fa fa-arrow-left"></i> Kembali ke Daftar Tanggal
                </a>
            </div>
            <div class="the-box">
                <div style="margin-bottom: 20px; text-align: right;">
                    @if ($isAkademikHonorarium)
                        <form action="{{ route('honorarium_penetapan_setup_type_ujian_otomatis', $date) }}" method="POST" style="display: inline;"
                            onsubmit="return confirm('Terapkan tipe dan nominal honorarium otomatis untuk data yang belum diatur pada tanggal ini? Data yang sudah diatur atau sudah lunas tidak akan diubah.');">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fa fa-magic"></i> Setup Tipe Ujian Otomatis
                            </button>
                        </form>
                        <form action="{{ route('honorarium_penetapan_reset_type', $date) }}" method="POST" style="display: inline;"
                            onsubmit="return confirm('Reset semua tipe dan nominal honorarium pada tanggal ini? Data yang sudah lunas tidak akan diubah.');">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-undo"></i> Reset Type
                            </button>
                        </form>
                    @else
                        <form action="{{ route('honorarium_available_all', $date) }}" method="POST" style="display: inline;"
                            onsubmit="return confirm('Set semua honorarium pada tanggal ini menjadi Available? Data tanpa tipe atau yang sudah lunas akan dilewati.');">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-check-circle"></i> Available Semua
                            </button>
                        </form>
                        <form action="{{ route('honorarium_unavailable_all', $date) }}" method="POST" style="display: inline;"
                            onsubmit="return confirm('Set semua honorarium pada tanggal ini menjadi Unavailable? Data yang sudah lunas tidak akan diubah.');">
                            @csrf
                            <button type="submit" class="btn btn-default">
                                <i class="fa fa-ban"></i> Unavailable Semua
                            </button>
                        </form>
                        <a href="{{ route('honorarium_history') }}" type="button" class="btn btn-primary">
                            <i class="fa fa-history"></i> History
                        </a>
                    @endif
                </div>

                @if (!$isAkademikHonorarium)
                    @php
                        $totalHonorariumTanggal = $data->sum(function ($honorarium) {
                            return (float) $honorarium->total_honor;
                        });
                    @endphp
                    <div class="alert alert-success square honorarium-total-summary" style="margin-bottom: 20px; font-size: 18px;">
                        <strong>Total Honor Seluruh Mahasiswa:</strong>
                        <strong id="total-honorarium-tanggal" style="font-size: 24px; margin-left: 8px;">{{ helper::formatRupiah($totalHonorariumTanggal) }}</strong>
                    </div>
                @endif

                <form action="{{ $isAkademikHonorarium ? route($saveRoute) : route($homeRoute) }}" method="{{ $isAkademikHonorarium ? 'POST' : 'GET' }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table" id="honorarium-detail-table">
                            <thead class="the-box dark full">
                                <tr>
                                    <th>No</th>
                                    <th>Nim</th>
                                    <th>Student Name</th>
                                    <th>Jenis TA</th>
                                    @if ($isAkademikHonorarium)
                                        <th>Kehadiran Pembimbing</th>
                                    @endif
                                    @if (!$isAkademikHonorarium)
                                        <th>Available</th>
                                    @endif
                                    <th class="honorarium-type-column">Type</th>
                                    @if ($isAkademikHonorarium)
                                        <th>Nomor SK Ujian</th>
                                    @else
                                        <th>Total Honor</th>
                                    @endif
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $honorarium)
                                    @php
                                        $tipeBelumDitetapkan = empty($honorarium->tipe_ujian) || in_array((string) $honorarium->tipe_ujian, ['0', '2'], true);
                                        $sudahAdaPembayaran = ($honorarium->KS && $honorarium->KS_Stat == 3)
                                            || ($honorarium->PU && $honorarium->PU_Stat == 3)
                                            || ($honorarium->PP && $honorarium->PP_Stat == 3)
                                            || ($honorarium->P1 && $honorarium->P1_Stat == 3)
                                            || ($honorarium->P2 && $honorarium->P2_Stat == 3)
                                            || ($honorarium->P3 && $honorarium->P3_Stat == 3);
                                        $tersediaUntukSemuaPeran = (!$honorarium->KS || $honorarium->KS_Stat != 0)
                                            && (!$honorarium->PU || $honorarium->PU_Stat != 0)
                                            && (!$honorarium->PP || $honorarium->PP_Stat != 0)
                                            && (!$honorarium->P1 || $honorarium->P1_Stat != 0)
                                            && (!$honorarium->P2 || $honorarium->P2_Stat != 0)
                                            && (!$honorarium->P3 || $honorarium->P3_Stat != 0);
                                        $hitungTotalMaster = function ($masterHonorarium) use ($honorarium) {
                                            $total = 0;
                                            $peranMaster = [
                                                'KS' => 'ketua_sidang',
                                                'PU' => 'pembimbing_utama',
                                                'PP' => 'pembimbing_pendamping',
                                                'P1' => 'penguji_1',
                                                'P2' => 'penguji_2',
                                                'P3' => 'penguji_3',
                                            ];
                                            foreach ($peranMaster as $peran => $kolomMaster) {
                                                if (trim((string) $honorarium->{$peran}) !== '') {
                                                    $total += (float) $masterHonorarium->{$kolomMaster};
                                                }
                                            }

                                            return $total;
                                        };
                                        $adaPembimbingUtama = trim((string) $honorarium->PU) !== '';
                                        $adaPembimbingPendamping = trim((string) $honorarium->PP) !== '';
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="honorarium-type-column">
                                            <strong>{{ $honorarium->C_NPM }}</strong>
                                            <span class="honorarium-student-class label {{ $honorarium->mahasiswa_eksekutif ? 'label-primary' : 'label-default' }}">
                                                {{ $honorarium->mahasiswa_eksekutif ? 'Eksekutif' : 'Reguler' }}
                                            </span>
                                        </td>
                                        <td>{{ helper::getNamaMhs($honorarium->C_NPM) }}</td>
                                        <td>
                                            @if ($honorarium->kode_jenis_tugas_akhir)
                                                <span class="label label-info">{{ $honorarium->kode_jenis_tugas_akhir }}</span>
                                            @else
                                                <span class="label label-warning">Belum ditetapkan</span>
                                            @endif
                                        </td>
                                        @if ($isAkademikHonorarium)
                                            <td class="honorarium-advisor-attendance">
                                                <input type="hidden" name="honorariums[{{ $loop->index }}][pembimbing_utama_hadir]" value="{{ $sudahAdaPembayaran || !$adaPembimbingUtama ? ($honorarium->pembimbing_utama_hadir ? 1 : 0) : 0 }}">
                                                <label>
                                                    <input type="checkbox"
                                                        class="pembimbing-attendance-checkbox"
                                                        name="honorariums[{{ $loop->index }}][pembimbing_utama_hadir]"
                                                        value="1"
                                                        data-honorarium-id="{{ $honorarium->id }}"
                                                        data-role="pembimbing_utama_hadir"
                                                        {{ $honorarium->pembimbing_utama_hadir ? 'checked' : '' }}
                                                        {{ !$adaPembimbingUtama || $sudahAdaPembayaran ? 'disabled' : '' }}>
                                                    {{ $adaPembimbingUtama ? helper::getDeskripsi($honorarium->PU) : '---' }}
                                                </label>

                                                <input type="hidden" name="honorariums[{{ $loop->index }}][pembimbing_pendamping_hadir]" value="{{ $sudahAdaPembayaran || !$adaPembimbingPendamping ? ($honorarium->pembimbing_pendamping_hadir ? 1 : 0) : 0 }}">
                                                <label>
                                                    <input type="checkbox"
                                                        class="pembimbing-attendance-checkbox"
                                                        name="honorariums[{{ $loop->index }}][pembimbing_pendamping_hadir]"
                                                        value="1"
                                                        data-honorarium-id="{{ $honorarium->id }}"
                                                        data-role="pembimbing_pendamping_hadir"
                                                        {{ $honorarium->pembimbing_pendamping_hadir ? 'checked' : '' }}
                                                        {{ !$adaPembimbingPendamping || $sudahAdaPembayaran ? 'disabled' : '' }}>
                                                    {{ $adaPembimbingPendamping ? helper::getDeskripsi($honorarium->PP) : '---' }}
                                                </label>
                                                <span class="honorarium-attendance-status"></span>
                                            </td>
                                        @endif
                                        @if (!$isAkademikHonorarium)
                                            <td>
                                                <input type="checkbox" name="honorariums[{{ $loop->index }}][KS_Stat]"
                                                    data-toggle="toggle" data-on="Yes" data-off="No"
                                                    data-honorarium-id="{{ $honorarium->id }}"
                                                    {{ $tersediaUntukSemuaPeran ? 'checked' : '' }}
                                                    {{ $tipeBelumDitetapkan || $sudahAdaPembayaran ? 'disabled' : '' }}>
                                            </td>
                                        @endif
                                        <td>
                                            @if ($isAkademikHonorarium)
                                                <select class="form-control"
                                                    name="honorariums[{{ $loop->index }}][id_pembayaran]"
                                                    {{ $sudahAdaPembayaran ? 'disabled' : '' }}>

                                                    @if ($honorarium->tipe_ujian == '0' || $honorarium->tipe_ujian == '2')
                                                        <option value="unset" data-total-honor="0">Unset</option>
                                                        @foreach ($dataMasterHonorarium as $masterHonorarium)
                                                            @if (empty($masterHonorarium->jenis_tugas_akhir_ids)
                                                                || empty($honorarium->jenis_tugas_akhir_id)
                                                                || in_array((int) $honorarium->jenis_tugas_akhir_id, $masterHonorarium->jenis_tugas_akhir_ids))
                                                                @if ((int) $masterHonorarium->untuk_mahasiswa_eksekutif === ($honorarium->mahasiswa_eksekutif ? 1 : 0))
                                                                <option value="{{ $masterHonorarium->id_honorarium }}" data-total-honor="{{ $hitungTotalMaster($masterHonorarium) }}">
                                                                    {{ $masterHonorarium->name }}</option>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @php
                                                            $masterPembayaranTersimpan = $dataMasterHonorarium->first(function ($masterHonorarium) use ($honorarium) {
                                                                return $masterHonorarium->name === $honorarium->tipe_ujian
                                                                    && (empty($masterHonorarium->jenis_tugas_akhir_ids)
                                                                        || empty($honorarium->jenis_tugas_akhir_id)
                                                                        || in_array((int) $honorarium->jenis_tugas_akhir_id, $masterHonorarium->jenis_tugas_akhir_ids))
                                                                    && (int) $masterHonorarium->untuk_mahasiswa_eksekutif === ($honorarium->mahasiswa_eksekutif ? 1 : 0);
                                                            });
                                                        @endphp
                                                        <option value="{{ $masterPembayaranTersimpan ? $masterPembayaranTersimpan->id_honorarium : 'unset' }}" data-total-honor="{{ $honorarium->total_honor }}" selected>
                                                            {{ $honorarium->tipe_ujian }}{{ $masterPembayaranTersimpan ? '' : ' (tersimpan)' }}</option>
                                                        <option disabled>-----</option>
                                                        @foreach ($dataMasterHonorarium as $masterHonorarium)
                                                            @if (empty($masterHonorarium->jenis_tugas_akhir_ids)
                                                                || empty($honorarium->jenis_tugas_akhir_id)
                                                                || in_array((int) $honorarium->jenis_tugas_akhir_id, $masterHonorarium->jenis_tugas_akhir_ids))
                                                                @if ((int) $masterHonorarium->untuk_mahasiswa_eksekutif === ($honorarium->mahasiswa_eksekutif ? 1 : 0))
                                                                <option value="{{ $masterHonorarium->id_honorarium }}" data-total-honor="{{ $hitungTotalMaster($masterHonorarium) }}">
                                                                    {{ $masterHonorarium->name }}</option>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                            @else
                                                @if ($tipeBelumDitetapkan)
                                                    <span class="label label-warning">Belum ditetapkan</span>
                                                @else
                                                    <strong>{{ $honorarium->tipe_ujian }}</strong>
                                                @endif
                                            @endif
                                        </td>
                                        @if ($isAkademikHonorarium)
                                            <td class="honorarium-sk-list">
                                                <span class="honorarium-row-total hidden" data-total-honor="{{ $honorarium->total_honor }}"></span>
                                                <div>
                                                    <span class="honorarium-sk-label">Proposal :</span>
                                                    <span>{{ $honorarium->nomor_sk_proposal ?: '---' }}</span>
                                                </div>
                                                <div>
                                                    <span class="honorarium-sk-label">Ujian Akhir :</span>
                                                    <span>{{ $honorarium->nomor_sk_ujian_akhir ?: '---' }}</span>
                                                </div>
                                            </td>
                                        @else
                                            <td class="text-right"><strong class="honorarium-row-total" data-total-honor="{{ $honorarium->total_honor }}">{{ helper::formatRupiah($honorarium->total_honor) }}</strong></td>
                                        @endif
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm view-honorarium-btn"
                                                data-toggle="modal" data-target="#statusModal"
                                                data-honorarium-id="{{ $honorarium->id }}"
                                                data-ks="{{ helper::getDeskripsi($honorarium->KS) }}"
                                                data-pu="{{ helper::getDeskripsi($honorarium->PU) }}"
                                                data-pp="{{ helper::getDeskripsi($honorarium->PP) }}"
                                                data-p1="{{ helper::getDeskripsi($honorarium->P1) }}"
                                                data-p2="{{ helper::getDeskripsi($honorarium->P2) }}"
                                                data-p3="{{ helper::getDeskripsi($honorarium->P3) }}"
                                                data-ks-h="{{ helper::formatRupiah($honorarium->KS_H) }}"
                                                data-pu-h="{{ helper::formatRupiah($honorarium->PU_H) }}"
                                                data-pp-h="{{ helper::formatRupiah($honorarium->PP_H) }}"
                                                data-p1-h="{{ helper::formatRupiah($honorarium->P1_H) }}"
                                                data-p2-h="{{ helper::formatRupiah($honorarium->P2_H) }}"
                                                data-p3-h="{{ helper::formatRupiah($honorarium->P3_H) }}"
                                                data-ks-stat="{{ $honorarium->KS_Stat }}"
                                                data-pu-stat="{{ $honorarium->PU_Stat }}"
                                                data-pp-stat="{{ $honorarium->PP_Stat }}"
                                                data-p1-stat="{{ $honorarium->P1_Stat }}"
                                                data-p2-stat="{{ $honorarium->P2_Stat }}"
                                                data-p3-stat="{{ $honorarium->P3_Stat }}">
                                                <i class="fa fa-info-circle"></i> View
                                            </button>
                                        </td>
                                        <input type="hidden" name="honorariums[{{ $loop->index }}][id]"
                                            value="{{ $honorarium->id }}">
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                    @if ($isAkademikHonorarium)
                        <div style="text-align: right; margin-top: 20px;">
                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                        </div>
                    @endif
                </form>
                <div>
                    <small>
                        <p>KS = Ketua Sidang</p>
                        <p>PU = Pembimbing Utama</p>
                        <p>PP = Pembimbing Pendamping</p>
                        <p>P1 = Penguji I</p>
                        <p>P2 = Penguji II</p>
                        <p>P3 = Penguji III</p>
                    </small>
                </div>
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div><!-- /.page-content -->

    <!-- Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Honorarium Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Name</th>
                                <th>Honor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>KS</td>
                                <td id="modal-ks"></td>
                                <td id="modal-ks-h"></td>
                                <td id="modal-ks-status"></td>
                            </tr>
                            <tr>
                                <td>PU</td>
                                <td id="modal-pu"></td>
                                <td id="modal-pu-h"></td>
                                <td id="modal-pu-status"></td>
                            </tr>
                            <tr>
                                <td>PP</td>
                                <td id="modal-pp"></td>
                                <td id="modal-pp-h"></td>
                                <td id="modal-pp-status"></td>
                            </tr>
                            <tr>
                                <td>P1</td>
                                <td id="modal-p1"></td>
                                <td id="modal-p1-h"></td>
                                <td id="modal-p1-status"></td>
                            </tr>
                            <tr>
                                <td>P2</td>
                                <td id="modal-p2"></td>
                                <td id="modal-p2-h"></td>
                                <td id="modal-p2-status"></td>
                            </tr>
                            <tr>
                                <td>P3</td>
                                <td id="modal-p3"></td>
                                <td id="modal-p3-h"></td>
                                <td id="modal-p3-status"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var tableSelector = '#honorarium-detail-table';
            if ($.fn.dataTable.isDataTable(tableSelector)) {
                $(tableSelector).DataTable().destroy();
            }

            var datatable = $(tableSelector).DataTable({
                paging: false,
                info: false,
                lengthChange: false
            });

            function formatRupiah(value) {
                return 'Rp ' + Math.round(value || 0).toLocaleString('id-ID');
            }

            function updateTotalHonorariumTanggal() {
                var total = 0;
                $('.honorarium-row-total').each(function() {
                    total += parseFloat($(this).attr('data-total-honor')) || 0;
                });
                $('#total-honorarium-tanggal').text(formatRupiah(total));
            }

            $(document).off('change.honorariumTotal', tableSelector + ' select[name$="[id_pembayaran]"]')
                .on('change.honorariumTotal', tableSelector + ' select[name$="[id_pembayaran]"]', function() {
                    var totalBaru = parseFloat($(this).find('option:selected').attr('data-total-honor')) || 0;
                    var totalBaris = $(this).closest('tr').find('.honorarium-row-total');

                    totalBaris.attr('data-total-honor', totalBaru).text(formatRupiah(totalBaru));
                    updateTotalHonorariumTanggal();
                });

            $(document).off('change.pembimbingAttendance', '.pembimbing-attendance-checkbox')
                .on('change.pembimbingAttendance', '.pembimbing-attendance-checkbox', function() {
                    var checkbox = $(this);
                    var status = checkbox.closest('.honorarium-advisor-attendance').find('.honorarium-attendance-status');
                    var nilaiSebelumnya = !checkbox.prop('checked');

                    checkbox.prop('disabled', true);
                    status.removeClass('text-success text-danger').text('Menyimpan...');

                    $.ajax({
                        url: "{{ route('honorarium_penetapan_pembimbing_attendance') }}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: checkbox.data('honorarium-id'),
                            role: checkbox.data('role'),
                            hadir: checkbox.prop('checked') ? 1 : 0
                        },
                        success: function(response) {
                            status.addClass('text-success').text('Tersimpan');
                            setTimeout(function() {
                                status.text('').removeClass('text-success');
                            }, 1800);
                        },
                        error: function(xhr) {
                            var response = xhr.responseJSON || {};
                            checkbox.prop('checked', nilaiSebelumnya);
                            status.addClass('text-danger').text(response.message || 'Gagal disimpan');
                        },
                        complete: function() {
                            checkbox.prop('disabled', false);
                        }
                    });
                });

            function bindToggleEvents() {
                $('input[data-toggle="toggle"]').bootstrapToggle();

                $('input[data-toggle="toggle"]').off('change').on('change', function() {
                    var honorariumId = $(this).data('honorarium-id');
                    var isChecked = $(this).prop('checked');
                    var url = isChecked ? "{{ route('honorarium_available_post_yes') }}" :
                        "{{ route('honorarium_available_post_no') }}";

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id_honorarium: honorariumId
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!'
                            });
                            $(this).bootstrapToggle('toggle');
                        }
                    });
                });

                $('.view-honorarium-btn').off('click').on('click', function() {
                    var ks = $(this).data('ks');
                    var pu = $(this).data('pu');
                    var pp = $(this).data('pp');
                    var p1 = $(this).data('p1');
                    var p2 = $(this).data('p2');
                    var p3 = $(this).data('p3');
                    var ksHonor = $(this).data('ks-h');
                    var puHonor = $(this).data('pu-h');
                    var ppHonor = $(this).data('pp-h');
                    var p1Honor = $(this).data('p1-h');
                    var p2Honor = $(this).data('p2-h');
                    var p3Honor = $(this).data('p3-h');

                    $('#modal-ks').text(ks);
                    $('#modal-pu').text(pu);
                    $('#modal-pp').text(pp);
                    $('#modal-p1').text(p1);
                    $('#modal-p2').text(p2);
                    $('#modal-p3').text(p3);
                    $('#modal-ks-h').text(ksHonor);
                    $('#modal-pu-h').text(puHonor);
                    $('#modal-pp-h').text(ppHonor);
                    $('#modal-p1-h').text(p1Honor);
                    $('#modal-p2-h').text(p2Honor);
                    $('#modal-p3-h').text(p3Honor);

                    var ksStat = $(this).data('ks-stat');
                    var puStat = $(this).data('pu-stat');
                    var ppStat = $(this).data('pp-stat');
                    var p1Stat = $(this).data('p1-stat');
                    var p2Stat = $(this).data('p2-stat');
                    var p3Stat = $(this).data('p3-stat');

                    $('#modal-ks-status').html('<span class="' + (ksStat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (ksStat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-pu-status').html('<span class="' + (puStat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (puStat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-pp-status').html('<span class="' + (ppStat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (ppStat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-p1-status').html('<span class="' + (p1Stat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (p1Stat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-p2-status').html('<span class="' + (p2Stat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (p2Stat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-p3-status').html('<span class="' + (p3Stat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (p3Stat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                });
            }

            bindToggleEvents();

            datatable.on('draw', function() {
                bindToggleEvents();
            });
        });
    </script>
@endsection
