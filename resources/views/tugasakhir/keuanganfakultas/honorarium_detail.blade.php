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

        .honorarium-nim-column {
            width: 105px;
            max-width: 105px;
            white-space: normal;
            word-break: break-word;
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

        .honorarium-advisor-presence {
            min-width: 90px;
            line-height: 1.7;
            white-space: nowrap;
        }

        .honorarium-advisor-presence .fa-check {
            color: #16a34a;
        }

        .honorarium-advisor-presence .fa-times {
            color: #dc2626;
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

        .honorarium-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin: 10px 0 16px;
        }

        .honorarium-page-title {
            margin: 0;
            font-size: 21px;
            line-height: 1.3;
        }

        .honorarium-page-date {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
        }

        .honorarium-setup-panel {
            margin-bottom: 16px;
            padding: 2px 0 14px;
            border-bottom: 1px solid #e2e8f0;
        }

        .honorarium-setup-main {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .honorarium-setup-overview {
            min-width: 0;
        }

        .honorarium-setup-title {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 8px;
        }

        .honorarium-setup-title h4 {
            margin: 0;
            font-size: 15px;
            line-height: 1.35;
        }

        .automatic-setup-help-toggle {
            padding: 1px 5px;
            color: #2563eb;
            text-decoration: none;
        }

        .automatic-setup-help-toggle:hover,
        .automatic-setup-help-toggle:focus {
            text-decoration: none;
        }

        .setup-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .setup-summary-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-height: 25px;
            padding: 3px 8px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            background: #fff;
            color: #475569;
            font-size: 11px;
            white-space: nowrap;
        }

        .setup-summary-item strong {
            color: #0f172a;
            font-size: 13px;
        }

        .setup-summary-item.is-ready {
            border-color: #86efac;
            background: #f0fdf4;
            color: #166534;
        }

        .setup-summary-item.is-blocked {
            border-color: #fecaca;
            background: #fef2f2;
            color: #b91c1c;
        }

        .honorarium-setup-actions,
        .honorarium-page-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            gap: 7px;
        }

        .honorarium-setup-actions form,
        .honorarium-page-actions form {
            margin: 0;
        }

        .automatic-setup-rules {
            margin-top: 12px;
            padding: 10px 12px;
            border-left: 3px solid #60a5fa;
            background: #f8fafc;
            color: #334155;
            font-size: 12px;
        }

        .automatic-setup-rules ul {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 18px;
            margin: 0 0 7px;
            padding-left: 18px;
        }

        .automatic-setup-rules p {
            margin: 0;
        }

        .automatic-setup-status {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 5px;
            font-size: 11px;
            line-height: 1.35;
        }

        .automatic-setup-target,
        .automatic-setup-state {
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .automatic-setup-state {
            padding: 2px 6px;
            border-radius: 3px;
            background: #f1f5f9;
        }

        .automatic-setup-status.is-ready {
            color: #15803d;
        }

        .automatic-setup-status.is-blocked {
            color: #b91c1c;
        }

        .automatic-setup-status.is-skipped {
            color: #64748b;
        }

        .automatic-setup-block-reason {
            display: block;
            margin-top: 4px;
            color: #b91c1c;
            font-size: 11px;
            line-height: 1.35;
        }

        .honorarium-role-legend {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 11px;
        }

        .honorarium-role-legend .btn-link {
            padding: 0;
            font-size: 11px;
            text-decoration: none;
        }

        .honorarium-role-legend-content {
            margin-top: 7px;
            line-height: 1.7;
        }

        #honorarium-detail-table th,
        #honorarium-detail-table td {
            vertical-align: middle;
        }

        #honorarium-detail-table_wrapper .dataTables_filter {
            display: flex;
            justify-content: flex-end;
            width: 100%;
            float: none !important;
            clear: both;
            margin: 0 0 12px;
            text-align: left;
        }

        #honorarium-detail-table_wrapper .dataTables_filter label {
            display: block;
            width: 260px;
            margin: 0;
        }

        #honorarium-detail-table_wrapper .dataTables_filter input {
            width: 260px;
            max-width: 100%;
            height: 34px;
            margin-left: 0;
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
        }

        @media (max-width: 767px) {
            .honorarium-page-header,
            .honorarium-setup-main {
                align-items: stretch;
                flex-direction: column;
            }

            .honorarium-page-header .btn,
            .honorarium-setup-actions,
            .honorarium-setup-actions form,
            .honorarium-setup-actions .btn {
                width: 100%;
            }

            .honorarium-setup-actions {
                align-items: stretch;
            }

            #honorarium-detail-table_wrapper .dataTables_filter input {
                width: 100%;
            }

            #honorarium-detail-table_wrapper .dataTables_filter label {
                width: 100%;
            }
        }
    </style>
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
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
            <div class="honorarium-page-header">
                <div>
                    <h3 class="honorarium-page-title">{{ $isAkademikHonorarium ? 'Penetapan Honorarium' : 'Honorarium' }}</h3>
                    <span class="honorarium-page-date"><i class="fa fa-calendar"></i> {{ date('d/m/Y', strtotime($date)) }}</span>
                </div>
                <a href="{{ route($homeRoute) }}" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> Daftar Tanggal
                </a>
            </div>
            <div class="the-box">
                @if ($isAkademikHonorarium && $automaticTypeSetupPlan)
                    @php
                        $setupCanApply = $automaticTypeSetupPlan['can_apply'];
                    @endphp
                    <div class="honorarium-setup-panel">
                        <div class="honorarium-setup-main">
                            <div class="honorarium-setup-overview">
                                <div class="honorarium-setup-title">
                                    <h4><i class="fa fa-magic"></i> Setup Otomatis</h4>
                                    <button type="button" class="btn btn-link btn-xs automatic-setup-help-toggle collapsed"
                                        data-toggle="collapse" data-target="#automatic-setup-help"
                                        aria-expanded="false" aria-controls="automatic-setup-help" title="Lihat aturan setup">
                                        <i class="fa fa-info-circle"></i> Aturan
                                    </button>
                                </div>
                                <div class="setup-summary" aria-label="Ringkasan pemeriksaan setup">
                                    <span class="setup-summary-item is-ready">
                                        <i class="fa fa-check-circle"></i><strong>{{ $automaticTypeSetupPlan['ready_count'] }}</strong> Siap
                                    </span>
                                    <span class="setup-summary-item">
                                        <i class="fa fa-lock"></i><strong>{{ $automaticTypeSetupPlan['skipped_count'] }}</strong> Sudah diatur
                                    </span>
                                    <span class="setup-summary-item {{ $automaticTypeSetupPlan['blocking_count'] > 0 ? 'is-blocked' : '' }}">
                                        <i class="fa {{ $automaticTypeSetupPlan['blocking_count'] > 0 ? 'fa-exclamation-circle' : 'fa-check-circle' }}"></i>
                                        <strong>{{ $automaticTypeSetupPlan['blocking_count'] }}</strong> Perlu cek
                                    </span>
                                </div>
                            </div>
                            <div class="honorarium-setup-actions">
                                <form action="{{ route('honorarium_penetapan_setup_type_ujian_otomatis', $date) }}" method="POST"
                                    onsubmit="return confirm('Terapkan tipe dan nominal honorarium untuk {{ $automaticTypeSetupPlan['ready_count'] ?? 0 }} data? Proses hanya berjalan jika seluruh data tanggal ini lolos pemeriksaan.');">
                                    @csrf
                                    <button type="submit" class="btn btn-warning btn-sm"
                                        {{ empty($setupCanApply) ? 'disabled' : '' }}
                                        title="{{ empty($setupCanApply) ? 'Perbaiki data penghambat atau tidak ada data yang perlu disetup.' : 'Terapkan hasil pemeriksaan otomatis.' }}">
                                        <i class="fa fa-magic"></i> Terapkan Otomatis
                                    </button>
                                </form>
                                <form action="{{ route('honorarium_penetapan_reset_type', $date) }}" method="POST"
                                    onsubmit="return confirm('Reset semua tipe dan nominal honorarium pada tanggal ini? Data yang sudah lunas tidak akan diubah.');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" title="Reset tipe honorarium">
                                        <i class="fa fa-undo"></i> Reset
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div id="automatic-setup-help" class="collapse automatic-setup-rules">
                            <ul>
                                <li><strong>Proposal</strong> menjadi Proposal.</li>
                                <li><strong>Ujian Akhir + SK Proposal</strong> menjadi Ujian Meja.</li>
                                <li><strong>Ujian Akhir tanpa SK Proposal</strong> menjadi Proposal + Ujian Meja.</li>
                            </ul>
                            <p><i class="fa fa-shield"></i> Setup berhenti bila ada data bermasalah atau risiko Proposal ganda; data yang sudah diatur atau dibayar tidak diubah.</p>
                        </div>
                    </div>
                @endif
                @if (!$isAkademikHonorarium)
                    <div class="honorarium-page-actions" style="margin-bottom: 20px;">
                        <form action="{{ route('honorarium_available_all', $date) }}" method="POST"
                            onsubmit="return confirm('Set semua honorarium pada tanggal ini menjadi Available? Data tanpa tipe atau yang sudah lunas akan dilewati.');">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fa fa-check-circle"></i> Available Semua
                            </button>
                        </form>
                        <form action="{{ route('honorarium_unavailable_all', $date) }}" method="POST"
                            onsubmit="return confirm('Set semua honorarium pada tanggal ini menjadi Unavailable? Data yang sudah lunas tidak akan diubah.');">
                            @csrf
                            <button type="submit" class="btn btn-default">
                                <i class="fa fa-ban"></i> Unavailable Semua
                            </button>
                        </form>
                        <a href="{{ route('honorarium_history') }}" type="button" class="btn btn-primary">
                            <i class="fa fa-history"></i> History
                        </a>
                    </div>
                @endif

                @if (!$isAkademikHonorarium)
                    @php
                        $totalHonorariumTanggal = $data->sum(function ($honorarium) {
                            return isset($honorarium->total_honor_tersesuaikan)
                                ? (float) $honorarium->total_honor_tersesuaikan
                                : (float) $honorarium->total_honor;
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
                                    <th class="honorarium-nim-column">NIM</th>
                                    <th>Nama Mahasiswa</th>
                                    <th>Jenis TA</th>
                                    @if ($isAkademikHonorarium)
                                        <th>Kehadiran Pembimbing</th>
                                    @endif
                                    @if (!$isAkademikHonorarium)
                                        <th>Kehadiran Pembimbing</th>
                                    @endif
                                    @if (!$isAkademikHonorarium)
                                        <th>Available</th>
                                    @endif
                                    <th class="honorarium-type-column">Tipe Honor</th>
                                    @if ($isAkademikHonorarium)
                                        <th>Nomor SK Ujian</th>
                                    @else
                                        <th>Total Honor</th>
                                    @endif
                                    <th>Detail</th>
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
                                        $rowSetupPlan = $isAkademikHonorarium && $automaticTypeSetupPlan
                                            ? $automaticTypeSetupPlan['rows']->get((int) $honorarium->id)
                                            : null;
                                        if ((int) $honorarium->exam_type === 0) {
                                            $cakupanPembayaranDiharapkan = 'proposal';
                                        } elseif ((int) $honorarium->exam_type === 2) {
                                            $cakupanPembayaranDiharapkan = $honorarium->memiliki_sk_proposal
                                                ? 'ujian_meja'
                                                : 'gabungan';
                                        } else {
                                            $cakupanPembayaranDiharapkan = null;
                                        }
                                        $masterPembayaranBerlaku = function ($masterHonorarium) use ($honorarium, $cakupanPembayaranDiharapkan) {
                                            return !empty($honorarium->jenis_tugas_akhir_id)
                                                && in_array((int) $honorarium->jenis_tugas_akhir_id, $masterHonorarium->jenis_tugas_akhir_ids, true)
                                                && (int) $masterHonorarium->untuk_mahasiswa_eksekutif === ($honorarium->mahasiswa_eksekutif ? 1 : 0)
                                                && $masterHonorarium->cakupan_ujian === $cakupanPembayaranDiharapkan;
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="honorarium-nim-column">
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
                                            <td class="honorarium-advisor-presence">
                                                <div>
                                                    <strong>PU :</strong>
                                                    <i class="fa {{ $honorarium->pembimbing_utama_hadir ? 'fa-check' : 'fa-times' }}"></i>
                                                </div>
                                                <div>
                                                    <strong>PP :</strong>
                                                    <i class="fa {{ $honorarium->pembimbing_pendamping_hadir ? 'fa-check' : 'fa-times' }}"></i>
                                                </div>
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
                                                        <option value="unset" data-total-honor="0">Belum ditetapkan</option>
                                                        @foreach ($dataMasterHonorarium as $masterHonorarium)
                                                            @if ($masterPembayaranBerlaku($masterHonorarium))
                                                                <option value="{{ $masterHonorarium->id_honorarium }}" data-total-honor="{{ $hitungTotalMaster($masterHonorarium) }}">
                                                                    {{ $masterHonorarium->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        @php
                                                            $masterPembayaranTersimpan = $dataMasterHonorarium->first(function ($masterHonorarium) use ($honorarium, $masterPembayaranBerlaku) {
                                                                return $masterHonorarium->name === $honorarium->tipe_ujian
                                                                    && $masterPembayaranBerlaku($masterHonorarium);
                                                            });
                                                        @endphp
                                                        <option value="{{ $masterPembayaranTersimpan ? $masterPembayaranTersimpan->id_honorarium : 'unset' }}" data-total-honor="{{ $honorarium->total_honor }}" selected>
                                                            {{ $honorarium->tipe_ujian }}{{ $masterPembayaranTersimpan ? '' : ' (tersimpan)' }}</option>
                                                        <option disabled>-----</option>
                                                        @foreach ($dataMasterHonorarium as $masterHonorarium)
                                                            @if ($masterPembayaranBerlaku($masterHonorarium))
                                                                <option value="{{ $masterHonorarium->id_honorarium }}" data-total-honor="{{ $hitungTotalMaster($masterHonorarium) }}">
                                                                    {{ $masterHonorarium->name }}</option>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @if ($rowSetupPlan)
                                                    @php
                                                        $rowSetupStatusClass = $rowSetupPlan['status'] === 'ready'
                                                            ? 'is-ready'
                                                            : (in_array($rowSetupPlan['status'], ['configured', 'protected'], true) ? 'is-skipped' : 'is-blocked');
                                                    @endphp
                                                    @php
                                                        $rowSetupStatusLabel = $rowSetupPlan['status'] === 'ready'
                                                            ? 'Siap'
                                                            : ($rowSetupPlan['status'] === 'protected'
                                                                ? 'Dilindungi'
                                                                : ($rowSetupPlan['status'] === 'configured' ? 'Sudah diatur' : 'Perlu cek'));
                                                        $rowSetupStatusIcon = $rowSetupPlan['status'] === 'ready'
                                                            ? 'fa-check-circle'
                                                            : ($rowSetupPlan['status'] === 'protected'
                                                                ? 'fa-lock'
                                                                : ($rowSetupPlan['status'] === 'configured' ? 'fa-check-circle' : 'fa-exclamation-circle'));
                                                    @endphp
                                                    <div class="automatic-setup-status {{ $rowSetupStatusClass }}" title="{{ $rowSetupPlan['message'] }}">
                                                        @if ($rowSetupPlan['expected_payment_name'])
                                                            <span class="automatic-setup-target"><i class="fa fa-magic"></i> {{ $rowSetupPlan['expected_payment_name'] }}</span>
                                                        @endif
                                                        <span class="automatic-setup-state"><i class="fa {{ $rowSetupStatusIcon }}"></i> {{ $rowSetupStatusLabel }}</span>
                                                    </div>
                                                    @if ($rowSetupStatusClass === 'is-blocked')
                                                        <small class="automatic-setup-block-reason">{{ $rowSetupPlan['message'] }}</small>
                                                    @endif
                                                @endif
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
                                            @php
                                                $totalHonorBaris = isset($honorarium->total_honor_tersesuaikan)
                                                    ? $honorarium->total_honor_tersesuaikan
                                                    : $honorarium->total_honor;
                                            @endphp
                                            <td class="text-right"><strong class="honorarium-row-total" data-total-honor="{{ $totalHonorBaris }}">{{ helper::formatRupiah($totalHonorBaris) }}</strong></td>
                                        @endif
                                        <td>
                                            @php
                                                $honorTersesuaikan = isset($honorarium->honor_tersesuaikan) ? $honorarium->honor_tersesuaikan : [];
                                                $honorAwal = [
                                                    'KS' => (float) $honorarium->KS_H,
                                                    'PU' => (float) $honorarium->PU_H,
                                                    'PP' => (float) $honorarium->PP_H,
                                                    'P1' => (float) $honorarium->P1_H,
                                                    'P2' => (float) $honorarium->P2_H,
                                                    'P3' => (float) $honorarium->P3_H,
                                                ];
                                                $formatPenyesuaianHonor = function ($nilai) {
                                                    $nilai = (float) $nilai;
                                                    if ($nilai > 0) {
                                                        return '+' . helper::formatRupiah($nilai);
                                                    }
                                                    if ($nilai < 0) {
                                                        return '-' . helper::formatRupiah(abs($nilai));
                                                    }
                                                    return helper::formatRupiah(0);
                                                };
                                            @endphp
                                            <button type="button" class="btn btn-primary btn-sm view-honorarium-btn"
                                                data-toggle="modal" data-target="#statusModal"
                                                title="Lihat rincian honorarium" aria-label="Lihat rincian honorarium"
                                                data-honorarium-id="{{ $honorarium->id }}"
                                                data-ks="{{ helper::getDeskripsi($honorarium->KS) }}"
                                                data-pu="{{ helper::getDeskripsi($honorarium->PU) }}"
                                                data-pp="{{ helper::getDeskripsi($honorarium->PP) }}"
                                                data-p1="{{ helper::getDeskripsi($honorarium->P1) }}"
                                                data-p2="{{ helper::getDeskripsi($honorarium->P2) }}"
                                                data-p3="{{ helper::getDeskripsi($honorarium->P3) }}"
                                                data-ks-base="{{ helper::formatRupiah($honorAwal['KS']) }}"
                                                data-pu-base="{{ helper::formatRupiah($honorAwal['PU']) }}"
                                                data-pp-base="{{ helper::formatRupiah($honorAwal['PP']) }}"
                                                data-p1-base="{{ helper::formatRupiah($honorAwal['P1']) }}"
                                                data-p2-base="{{ helper::formatRupiah($honorAwal['P2']) }}"
                                                data-p3-base="{{ helper::formatRupiah($honorAwal['P3']) }}"
                                                data-ks-adj="{{ $formatPenyesuaianHonor((isset($honorTersesuaikan['KS']) ? $honorTersesuaikan['KS'] : $honorAwal['KS']) - $honorAwal['KS']) }}"
                                                data-pu-adj="{{ $formatPenyesuaianHonor((isset($honorTersesuaikan['PU']) ? $honorTersesuaikan['PU'] : $honorAwal['PU']) - $honorAwal['PU']) }}"
                                                data-pp-adj="{{ $formatPenyesuaianHonor((isset($honorTersesuaikan['PP']) ? $honorTersesuaikan['PP'] : $honorAwal['PP']) - $honorAwal['PP']) }}"
                                                data-p1-adj="{{ $formatPenyesuaianHonor((isset($honorTersesuaikan['P1']) ? $honorTersesuaikan['P1'] : $honorAwal['P1']) - $honorAwal['P1']) }}"
                                                data-p2-adj="{{ $formatPenyesuaianHonor((isset($honorTersesuaikan['P2']) ? $honorTersesuaikan['P2'] : $honorAwal['P2']) - $honorAwal['P2']) }}"
                                                data-p3-adj="{{ $formatPenyesuaianHonor((isset($honorTersesuaikan['P3']) ? $honorTersesuaikan['P3'] : $honorAwal['P3']) - $honorAwal['P3']) }}"
                                                data-ks-h="{{ helper::formatRupiah(isset($honorTersesuaikan['KS']) ? $honorTersesuaikan['KS'] : $honorarium->KS_H) }}"
                                                data-pu-h="{{ helper::formatRupiah(isset($honorTersesuaikan['PU']) ? $honorTersesuaikan['PU'] : $honorarium->PU_H) }}"
                                                data-pp-h="{{ helper::formatRupiah(isset($honorTersesuaikan['PP']) ? $honorTersesuaikan['PP'] : $honorarium->PP_H) }}"
                                                data-p1-h="{{ helper::formatRupiah(isset($honorTersesuaikan['P1']) ? $honorTersesuaikan['P1'] : $honorarium->P1_H) }}"
                                                data-p2-h="{{ helper::formatRupiah(isset($honorTersesuaikan['P2']) ? $honorTersesuaikan['P2'] : $honorarium->P2_H) }}"
                                                data-p3-h="{{ helper::formatRupiah(isset($honorTersesuaikan['P3']) ? $honorTersesuaikan['P3'] : $honorarium->P3_H) }}">
                                                <i class="fa fa-info-circle"></i><span class="sr-only">Lihat rincian</span>
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
                            <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Simpan Tipe</button>
                        </div>
                    @endif
                </form>
                <div class="honorarium-role-legend">
                    <button type="button" class="btn btn-link btn-xs collapsed" data-toggle="collapse"
                        data-target="#honorarium-role-help" aria-expanded="false" aria-controls="honorarium-role-help">
                        <i class="fa fa-info-circle"></i> Singkatan peran
                    </button>
                    <div id="honorarium-role-help" class="collapse honorarium-role-legend-content">
                        <strong>KS</strong> Ketua Sidang &nbsp;·&nbsp;
                        <strong>PU</strong> Pembimbing Utama &nbsp;·&nbsp;
                        <strong>PP</strong> Pembimbing Pendamping &nbsp;·&nbsp;
                        <strong>P1/P2/P3</strong> Penguji I/II/III
                    </div>
                </div>
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div><!-- /.page-content -->

    <!-- Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Rincian Honorarium</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Peran</th>
                                    <th>Nama</th>
                                    <th>Honor Awal</th>
                                    <th>Perubahan</th>
                                    <th>Honor Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>KS</td>
                                    <td id="modal-ks"></td>
                                    <td id="modal-ks-base"></td>
                                    <td id="modal-ks-adj"></td>
                                    <td id="modal-ks-h"></td>
                                </tr>
                                <tr>
                                    <td>PU</td>
                                    <td id="modal-pu"></td>
                                    <td id="modal-pu-base"></td>
                                    <td id="modal-pu-adj"></td>
                                    <td id="modal-pu-h"></td>
                                </tr>
                                <tr>
                                    <td>PP</td>
                                    <td id="modal-pp"></td>
                                    <td id="modal-pp-base"></td>
                                    <td id="modal-pp-adj"></td>
                                    <td id="modal-pp-h"></td>
                                </tr>
                                <tr>
                                    <td>P1</td>
                                    <td id="modal-p1"></td>
                                    <td id="modal-p1-base"></td>
                                    <td id="modal-p1-adj"></td>
                                    <td id="modal-p1-h"></td>
                                </tr>
                                <tr>
                                    <td>P2</td>
                                    <td id="modal-p2"></td>
                                    <td id="modal-p2-base"></td>
                                    <td id="modal-p2-adj"></td>
                                    <td id="modal-p2-h"></td>
                                </tr>
                                <tr>
                                    <td>P3</td>
                                    <td id="modal-p3"></td>
                                    <td id="modal-p3-base"></td>
                                    <td id="modal-p3-adj"></td>
                                    <td id="modal-p3-h"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info square" style="margin-bottom: 0;">
                        Honor awal ditambah atau dikurangi penyesuaian kehadiran untuk menghasilkan honor akhir.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
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
                lengthChange: false,
                language: {
                    search: '',
                    searchPlaceholder: 'Cari NIM atau nama'
                }
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
                    var ksBase = $(this).data('ks-base');
                    var puBase = $(this).data('pu-base');
                    var ppBase = $(this).data('pp-base');
                    var p1Base = $(this).data('p1-base');
                    var p2Base = $(this).data('p2-base');
                    var p3Base = $(this).data('p3-base');
                    var ksAdj = $(this).data('ks-adj');
                    var puAdj = $(this).data('pu-adj');
                    var ppAdj = $(this).data('pp-adj');
                    var p1Adj = $(this).data('p1-adj');
                    var p2Adj = $(this).data('p2-adj');
                    var p3Adj = $(this).data('p3-adj');

                    $('#modal-ks').text(ks);
                    $('#modal-pu').text(pu);
                    $('#modal-pp').text(pp);
                    $('#modal-p1').text(p1);
                    $('#modal-p2').text(p2);
                    $('#modal-p3').text(p3);
                    $('#modal-ks-base').text(ksBase);
                    $('#modal-pu-base').text(puBase);
                    $('#modal-pp-base').text(ppBase);
                    $('#modal-p1-base').text(p1Base);
                    $('#modal-p2-base').text(p2Base);
                    $('#modal-p3-base').text(p3Base);
                    $('#modal-ks-adj').text(ksAdj);
                    $('#modal-pu-adj').text(puAdj);
                    $('#modal-pp-adj').text(ppAdj);
                    $('#modal-p1-adj').text(p1Adj);
                    $('#modal-p2-adj').text(p2Adj);
                    $('#modal-p3-adj').text(p3Adj);
                    $('#modal-ks-h').text(ksHonor);
                    $('#modal-pu-h').text(puHonor);
                    $('#modal-pp-h').text(ppHonor);
                    $('#modal-p1-h').text(p1Honor);
                    $('#modal-p2-h').text(p2Honor);
                    $('#modal-p3-h').text(p3Honor);
                });
            }

            bindToggleEvents();

            datatable.on('draw', function() {
                bindToggleEvents();
            });
        });
    </script>
@endsection
