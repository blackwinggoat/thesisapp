@extends('tugasakhir.index')
@section('isi')
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
                <li class="active">Honorarium</li>
            </ol>
            <!-- End breadcrumb -->

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Honorarium Ujian</h3>
            <div class="the-box">
                @php
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
                <div class="honorarium-toolbar">
                    <p class="text-muted">Pilih tanggal ujian untuk melihat rincian mahasiswa dan honorarium.</p>
                    <a href="{{route('history_honorarium')}}" type="button" class="btn btn-primary">
                        <i class="fa fa-history"></i> Riwayat Pembayaran
                    </a>
                </div>

                @if ($orphanAssignments->isNotEmpty())
                    <div class="alert alert-warning alert-block square">
                        <strong>{{ $orphanAssignments->count() }} penugasan belum terhubung ke jadwal ujian.</strong>
                        Data ini tidak dimasukkan ke kelompok tanggal agar tidak tercatat pada tanggal ujian yang keliru. Silakan koordinasikan dengan Prodi.
                    </div>
                @endif

                <form action="{{ route('honorarium_save_all_dosen') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table" id="datatable-example">
                            <thead class="the-box dark full">
                                <tr>
                                    <th>No</th>
                                    <th class="text-center">Konfirmasi Terima</th>
                                    <th>Tanggal Ujian</th>
                                    <th>Mahasiswa</th>
                                    <th>Penugasan Dosen</th>
                                    <th>Total Honorarium Siap Diterima</th>
                                    <th class="text-center">Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalAvailable = $honorariumByDate->sum('available_total');
                                    $waitingAssignments = $honorariumByDate->sum(function ($group) {
                                        return $group->assignment_count - $group->available_count;
                                    });
                                @endphp
                                @foreach ($honorariumByDate as $dateKey => $group)
                                    @php $modalId = 'honorarium-date-' . str_replace('-', '', $dateKey); @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="text-center">
                                            @if ($group->available_count > 0)
                                                <label class="honorarium-date-confirmation" title="Konfirmasi semua honorarium tersedia pada tanggal ini">
                                                    <input type="checkbox"
                                                        name="honorarium_dates[]"
                                                        value="{{ $dateKey }}"
                                                        data-amount="{{ $group->available_total }}"
                                                        data-count="{{ $group->available_count }}">
                                                    <span>{{ $group->available_count }} item</span>
                                                </label>
                                            @else
                                                <span class="text-muted">Belum tersedia</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($group->date)->format('d/m/Y') }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-default">{{ $group->student_count }} mahasiswa</span>
                                        </td>
                                        <td>{{ $group->assignment_count }} penugasan</td>
                                        <td>
                                            @if ($group->available_count > 0)
                                                <span class="badge badge-success amount-display">Rp {{ number_format($group->available_total, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">Belum tersedia</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#{{ $modalId }}">
                                                <i class="fa fa-list"></i> Detail <span class="badge">{{ $group->assignment_count }}</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                    <div style="margin-top: 20px; text-align: right;">
                        <table style="display: inline-table; border-collapse: separate;border: 1px solid gray;">
                            <tr>
                                <td style="border: 1px solid gray; padding: 10px"><strong>Honorarium Siap Diterima:</strong></td>
                                <td style="border: 1px solid gray; padding: 10px">Rp <span
                                        id="totalAvailable">{{ number_format($totalAvailable, 0, ',', '.') }}</span>,-</td>
                            </tr>
                            <tr>
                                <td style="border: 1px solid gray; padding: 10px"><strong>Penugasan Menunggu Ketersediaan:</strong></td>
                                <td style="border: 1px solid gray; padding: 10px">{{ $waitingAssignments }} penugasan</td>
                            </tr>
                        </table>
                    </div>
                    <div class="honorarium-save">
                        <button type="submit" class="btn btn-success" id="confirmHonorariumButton" disabled>
                            <i class="fa fa-check"></i> Konfirmasi Telah Terima
                        </button>
                    </div>

                @foreach ($honorariumByDate as $dateKey => $group)
                    @php $modalId = 'honorarium-date-' . str_replace('-', '', $dateKey); @endphp
                    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="{{ $modalId }}-title">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="{{ $modalId }}-title">Honorarium Ujian - {{ \Carbon\Carbon::parse($group->date)->format('d/m/Y') }}</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped honorarium-detail-table">
                                            <thead>
                                                <tr>
                                                    <th>Mahasiswa</th>
                                                    <th>Peran Dosen</th>
                                                    <th>Jenis Ujian</th>
                                                    <th>Honor Dasar</th>
                                                    <th>Perubahan</th>
                                                    <th>Honor Diterima</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($group->items as $honorarium)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ helper::getNamaMhs($honorarium->C_NPM) }}</strong>
                                                            <small class="text-muted display-block">{{ $honorarium->C_NPM }}</small>
                                                        </td>
                                                        <td>{{ $honorarium->role }}</td>
                                                        <td>{{ $honorarium->tipe_ujian == '0' || $honorarium->tipe_ujian == '2' ? 'Belum ditetapkan' : $honorarium->tipe_ujian }}</td>
                                                        <td>
                                                            @if ($honorarium->status == 1)
                                                                {{ helper::formatRupiah($honorarium->base_amount) }}
                                                            @else
                                                                <span class="text-muted">Belum tersedia</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($honorarium->status == 1)
                                                                @if ($honorarium->adjustment_amount > 0)
                                                                    <span class="badge badge-info">{{ $formatPenyesuaianHonor($honorarium->adjustment_amount) }}</span>
                                                                @elseif ($honorarium->adjustment_amount < 0)
                                                                    <span class="badge badge-danger">{{ $formatPenyesuaianHonor($honorarium->adjustment_amount) }}</span>
                                                                @else
                                                                    <span class="text-muted">{{ helper::formatRupiah(0) }}</span>
                                                                @endif
                                                                @if (!empty($honorarium->adjustment_note))
                                                                    <small class="text-muted display-block">{{ $honorarium->adjustment_note }}</small>
                                                                @endif
                                                            @else
                                                                <span class="text-muted">Belum tersedia</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($honorarium->status == 1)
                                                                <span class="badge badge-success">Rp {{ number_format($honorarium->amount, 0, ',', '.') }}</span>
                                                            @else
                                                                <span class="text-muted">Belum tersedia</span>
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
                </form>

            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div><!-- /.page-content -->
@endsection

@section('script')
    <style>
        .honorarium-toolbar {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .honorarium-toolbar p {
            margin: 0;
        }

        .honorarium-save {
            margin-top: 20px;
            text-align: right;
        }

        .display-block {
            display: block;
            margin-top: 3px;
        }

        .honorarium-detail-table th {
            white-space: nowrap;
        }

        .honorarium-date-confirmation {
            color: #3c763d;
            font-weight: 600;
            margin: 0;
            white-space: nowrap;
        }

        .honorarium-date-confirmation input {
            margin-right: 5px;
        }

        .badge-info {
            background: #31708f;
        }

        .badge-danger {
            background: #a94442;
        }

        @media (max-width: 767px) {
            .honorarium-toolbar {
                align-items: flex-start;
                flex-direction: column;
                gap: 12px;
            }

            .honorarium-save {
                text-align: left;
            }
        }
    </style>
    <script>
        $(function() {
            $('input[name="honorarium_dates[]"]').change(function() {
                var totalAvailable = parseFloat('{{ $totalAvailable }}');
                var selectedCount = 0;

                $('input[name="honorarium_dates[]"]:checked').each(function() {
                    totalAvailable -= parseFloat($(this).data('amount'));
                    selectedCount += parseInt($(this).data('count'), 10);
                });

                $('#totalAvailable').text(Math.max(totalAvailable, 0).toLocaleString('id-ID'));
                $('#confirmHonorariumButton')
                    .prop('disabled', selectedCount === 0)
                    .html('<i class="fa fa-check"></i> Konfirmasi Telah Terima' + (selectedCount > 0 ? ' (' + selectedCount + ' item)' : ''));
            });
        });
    </script>
@endsection
