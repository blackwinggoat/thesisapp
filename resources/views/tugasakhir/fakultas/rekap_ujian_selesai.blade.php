@extends('tugasakhir.index')

@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">SK Yudisium</li>
            </ol>

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <h3 class="page-heading">SK Yudisium</h3>

            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-hover" id="completed-exam-table">
                        <thead class="the-box dark full">
                            <tr>
                                <th class="text-center" style="width: 55px;">No</th>
                                <th>Tanggal Ujian</th>
                                <th class="text-center">Jumlah Mahasiswa</th>
                                <th class="text-center">Type Mahasiswa</th>
                                <th style="min-width: 260px;">SK per Program Studi</th>
                                <th class="text-center" style="width: 180px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $rekap)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ helper::tgl_indo_lengkap($rekap->tanggal_ujian) }}</td>
                                    <td class="text-center">
                                        <span class="label label-info">{{ $rekap->jumlah_mahasiswa }} mahasiswa</span>
                                        <div class="text-muted" style="margin-top: 5px; white-space: nowrap;">
                                            TI: {{ $rekap->jumlah_teknik_informatika }} &nbsp; SI: {{ $rekap->jumlah_sistem_informasi }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="label label-default">Reguler: {{ $rekap->jumlah_reguler }}</span>
                                        <div style="margin-top: 5px;">
                                            <span class="label label-primary">Eksekutif: {{ $rekap->jumlah_eksekutif }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($rekap->jumlah_teknik_informatika > 0)
                                            <a href="{{ route('fakultas.sk_yudisium_data', ['date' => $rekap->tanggal_ujian, 'kode_prodi' => '130']) }}"
                                                class="btn btn-default btn-xs" title="Kelola SK Yudisium Teknik Informatika" data-toggle="tooltip">
                                                <i class="fa fa-pencil"></i> TI
                                            </a>
                                            <span class="text-muted">{{ $rekap->nomor_surat_ti ?: 'Nomor belum diisi' }}</span>
                                        @endif
                                        @if ($rekap->jumlah_sistem_informasi > 0)
                                            <div style="margin-top: {{ $rekap->jumlah_teknik_informatika > 0 ? '7px' : '0' }};">
                                                <a href="{{ route('fakultas.sk_yudisium_data', ['date' => $rekap->tanggal_ujian, 'kode_prodi' => '131']) }}"
                                                    class="btn btn-default btn-xs" title="Kelola SK Yudisium Sistem Informasi" data-toggle="tooltip">
                                                    <i class="fa fa-pencil"></i> SI
                                                </a>
                                                <span class="text-muted">{{ $rekap->nomor_surat_si ?: 'Nomor belum diisi' }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center" style="white-space: nowrap;">
                                        <div class="yudisium-action-buttons" role="group" aria-label="Aksi SK Yudisium">
                                            <button type="button" class="btn btn-primary btn-sm show-completed-exam-details"
                                                data-detail-url="{{ route('fakultas.rekap_ujian_selesai_peserta', $rekap->tanggal_ujian) }}"
                                                data-date-label="{{ helper::tgl_indo_lengkap($rekap->tanggal_ujian) }}"
                                                title="Lihat informasi mahasiswa" data-toggle="tooltip" aria-label="Lihat informasi mahasiswa">
                                                <i class="fa fa-info-circle"></i>
                                            </button>
                                            @if ($rekap->jumlah_teknik_informatika > 0)
                                                <a href="{{ route('fakultas.sk_yudisium_data', ['date' => $rekap->tanggal_ujian, 'kode_prodi' => '130']) }}"
                                                    class="btn btn-danger btn-sm" style="margin-left: 6px;"
                                                    title="Atur dan periksa PDF SK Yudisium Teknik Informatika"
                                                    data-toggle="tooltip" aria-label="Atur dan periksa PDF SK Yudisium Teknik Informatika">
                                                    <i class="fa fa-file-pdf-o"></i> TI
                                                </a>
                                            @endif
                                            @if ($rekap->jumlah_sistem_informasi > 0)
                                                <a href="{{ route('fakultas.sk_yudisium_data', ['date' => $rekap->tanggal_ujian, 'kode_prodi' => '131']) }}"
                                                    class="btn btn-danger btn-sm" style="margin-left: 6px;"
                                                    title="Atur dan periksa PDF SK Yudisium Sistem Informasi"
                                                    data-toggle="tooltip" aria-label="Atur dan periksa PDF SK Yudisium Sistem Informasi">
                                                    <i class="fa fa-file-pdf-o"></i> SI
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada Ujian Tugas Akhir dengan tanggal ujian yang telah lewat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="completed-exam-detail-modal" tabindex="-1" role="dialog" aria-labelledby="completed-exam-detail-title">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="completed-exam-detail-title">Informasi Mahasiswa Ujian TA</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="completed-exam-detail-table">
                            <thead>
                                <tr>
                                    <th>NIM</th>
                                    <th>Nama</th>
                                    <th class="text-center">Nilai Ujian TA</th>
                                    <th class="text-center">IPK</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();

            function escapeHtml(value) {
                return $('<div>').text(value || '-').html();
            }

            $('.show-completed-exam-details').on('click', function() {
                var button = $(this);
                var modal = $('#completed-exam-detail-modal');
                var tableBody = $('#completed-exam-detail-table tbody');

                $('#completed-exam-detail-title').text('Informasi Mahasiswa Ujian TA - ' + button.data('date-label'));
                tableBody.html('<tr><td colspan="4" class="text-center">Memuat data...</td></tr>');
                modal.modal('show');

                $.get(button.data('detail-url'))
                    .done(function(response) {
                        if (!response.data || response.data.length === 0) {
                            tableBody.html('<tr><td colspan="4" class="text-center">Tidak ada data mahasiswa.</td></tr>');
                            return;
                        }

                        var rows = $.map(response.data, function(mahasiswa) {
                            return '<tr>' +
                                '<td>' + escapeHtml(mahasiswa.nim) + '</td>' +
                                '<td>' + escapeHtml(mahasiswa.nama) + '</td>' +
                                '<td class="text-center">' + escapeHtml(mahasiswa.nilai_ujian_ta) + '</td>' +
                                '<td class="text-center">' + escapeHtml(mahasiswa.ipk) + '</td>' +
                            '</tr>';
                        }).join('');

                        tableBody.html(rows);
                    })
                    .fail(function() {
                        tableBody.html('<tr><td colspan="4" class="text-center text-danger">Data mahasiswa tidak dapat dimuat.</td></tr>');
                    });
            });

        });
    </script>
@endsection
