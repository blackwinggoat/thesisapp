@extends('tugasakhir.index')

@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ route('fakultas.rekap_ujian_selesai') }}">SK Yudisium</a></li>
                <li class="active">{{ $programStudi }}</li>
            </ol>

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger square">
                    <strong>Data belum dapat disimpan.</strong>
                    <ul style="margin: 7px 0 0 18px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-7">
                    <h3 class="page-heading">Data SK Yudisium {{ $programStudi }}</h3>
                    <p class="text-muted" style="margin-top: -8px;">
                        Ujian Tugas Akhir tanggal {{ helper::tgl_indo_lengkap($date) }}
                    </p>
                    <p class="text-muted" style="margin-top: -4px;">
                        Nilai TA dihitung dari penilaian Thesis Apps. IPK ditarik dari nilai akhir aktif SIAKAD.
                    </p>
                </div>
                <div class="col-md-5 text-right" style="padding-top: 18px;">
                    <a href="{{ route('fakultas.rekap_ujian_selesai') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <button type="button" id="sync-all-yudisium-ipk" class="btn btn-info"
                        title="Tarik IPK seluruh peserta dari SIAKAD">
                        <i class="fa fa-cloud-download"></i> <span class="yudisium-sync-all-label">Tarik IPK SIAKAD</span>
                    </button>
                    @if (empty($kekurangan))
                        <a href="{{ route('fakultas.cetak_sk_yudisium', ['date' => $date, 'kode_prodi' => $kodeProdi]) }}"
                            target="_blank" class="btn btn-danger" title="Buka PDF SK Yudisium">
                            <i class="fa fa-file-pdf-o"></i> PDF
                        </a>
                    @else
                        <span data-toggle="tooltip" title="Lengkapi data yudisium sebelum membuat PDF.">
                            <button type="button" class="btn btn-danger" disabled>
                                <i class="fa fa-file-pdf-o"></i> PDF
                            </button>
                        </span>
                    @endif
                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#reset-yudisium-modal"
                        title="Reset data SK Yudisium">
                        <i class="fa fa-undo"></i> Reset
                    </button>
                </div>
            </div>

            @if (!empty($kekurangan))
                <div class="alert alert-warning square">
                    <strong>PDF belum siap diterbitkan.</strong>
                    <ul style="margin: 7px 0 0 18px;">
                        @foreach ($kekurangan as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('fakultas.simpan_data_sk_yudisium') }}" method="POST">
                @csrf
                <input type="hidden" name="tanggal_ujian" value="{{ $date }}">
                <input type="hidden" name="kode_prodi" value="{{ $kodeProdi }}">

                <div class="the-box">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="nomor_surat">Nomor Surat Keputusan</label>
                        <input id="nomor_surat" type="text" name="nomor_surat" maxlength="150" class="form-control"
                            value="{{ old('nomor_surat', $dokumen->nomor_surat ?? '') }}"
                            placeholder="Masukkan nomor SK Yudisium">
                        <span class="help-block" style="margin-bottom: 0;">
                            <span class="label label-info" style="font-size: 12px;">Contoh</span>
                            <strong style="margin-left: 5px;">{{ $contohNomorSuratYudisium }}</strong>
                            <span class="text-muted" style="margin-left: 5px;">
                                Nomor terakhir tersimpan: {{ $nomorSuratYudisiumTerakhir === null ? 'belum ada' : $nomorSuratYudisiumTerakhir }}.
                            </span>
                        </span>
                    </div>
                </div>

                @php
                    $pesertaSudahMemilikiNomorAlumni = $peserta->contains(function ($mahasiswa) {
                        return trim((string) $mahasiswa->nomor_alumni) !== '';
                    });
                @endphp
                <div class="the-box" style="border-left: 4px solid #3c8dbc;">
                    <div class="row">
                        <div class="col-sm-7">
                            <strong>Nomor alumni terakhir terpakai:</strong>
                            <span class="label label-primary" style="font-size: 14px; margin-left: 5px;">
                                {{ $nomorAlumniTerakhir === null ? 'Belum ada' : $nomorAlumniTerakhir }}
                            </span>
                            <p class="text-muted" style="margin: 7px 0 0;">
                                Nomor yang disarankan untuk peserta pertama adalah
                                <strong>{{ $nomorAlumniBerikutnya }}</strong>. Ubah kolom pertama bila diperlukan.
                            </p>
                        </div>
                        <div class="col-sm-5 text-right" style="padding-top: 4px;">
                            <button type="button" id="continue-alumni-numbers" class="btn btn-primary">
                                <i class="fa fa-sort-numeric-asc"></i> Lanjutkan Nomor Berikutnya
                            </button>
                            <div id="alumni-number-status" class="text-muted" style="margin-top: 7px;"></div>
                        </div>
                    </div>
                </div>

                <div class="the-box">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="yudisium-student-table">
                            <thead class="the-box dark full">
                                <tr>
                                    <th class="text-center" style="width: 52px;">No</th>
                                    <th style="min-width: 125px;">NIM</th>
                                    <th style="min-width: 200px;">Nama</th>
                                    <th class="text-center" style="min-width: 95px;">Nilai TA</th>
                                    <th class="text-center" style="min-width: 85px;">Huruf</th>
                                    <th style="min-width: 130px;">Nomor Alumni</th>
                                    <th style="min-width: 100px;">IPK</th>
                                    <th class="text-center" style="min-width: 110px;">Yudisium</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($peserta as $index => $mahasiswa)
                                    @php
                                        $ipkValue = old(
                                            'mahasiswa.' . $index . '.ipk',
                                            $mahasiswa->ipk === null ? '' : number_format($mahasiswa->ipk, 2, '.', '')
                                        );
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $loop->iteration }}</td>
                                        <td>
                                            {{ $mahasiswa->nim }}
                                            <input type="hidden" name="mahasiswa[{{ $index }}][nim]" value="{{ $mahasiswa->nim }}">
                                        </td>
                                        <td>{{ $mahasiswa->nama }}</td>
                                        <td class="text-center">
                                            {{ $mahasiswa->nilai_ujian_ta === null ? 'Belum lengkap' : number_format($mahasiswa->nilai_ujian_ta, 2, ',', '.') }}
                                        </td>
                                        <td class="text-center">{{ $mahasiswa->nilai_huruf ?: '-' }}</td>
                                        <td>
                                            @php
                                                $nomorAlumniDefault = $mahasiswa->nomor_alumni;
                                                if (!$pesertaSudahMemilikiNomorAlumni && $index === 0) {
                                                    $nomorAlumniDefault = $nomorAlumniBerikutnya;
                                                }
                                            @endphp
                                            <input type="text" name="mahasiswa[{{ $index }}][nomor_alumni]" maxlength="9"
                                                class="form-control input-sm yudisium-alumni-number" inputmode="numeric"
                                                value="{{ old('mahasiswa.' . $index . '.nomor_alumni', $nomorAlumniDefault) }}"
                                                placeholder="Nomor alumni" aria-label="Nomor alumni {{ $mahasiswa->nim }}">
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="text" name="mahasiswa[{{ $index }}][ipk]"
                                                    class="form-control yudisium-ipk" readonly
                                                    value="{{ $ipkValue }}" aria-label="IPK {{ $mahasiswa->nim }}"
                                                    placeholder="Belum ditarik">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-info yudisium-sync-ipk"
                                                        data-nim="{{ $mahasiswa->nim }}" title="Tarik IPK dari SIAKAD">
                                                        <i class="fa fa-cloud-download"></i>
                                                    </button>
                                                </span>
                                            </div>
                                            <small class="text-muted yudisium-ipk-status">
                                                @if ($mahasiswa->ipk_sumber)
                                                    {{ $mahasiswa->ipk_sumber }}
                                                    @if ($mahasiswa->ipk_disinkronkan_pada)
                                                        | {{ date('d-m-Y H:i', strtotime($mahasiswa->ipk_disinkronkan_pada)) }}
                                                    @endif
                                                @else
                                                    Belum ditarik dari SIAKAD
                                                @endif
                                            </small>
                                        </td>
                                        <td class="text-center yudisium-category">
                                            {{ $mahasiswa->kategori_yudisium ?: '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-right" style="margin-bottom: 24px;">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Simpan Data Yudisium
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="reset-yudisium-modal" tabindex="-1" role="dialog" aria-labelledby="reset-yudisium-title">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="reset-yudisium-title">
                        <i class="fa fa-exclamation-triangle text-warning"></i> Reset Data SK Yudisium
                    </h4>
                </div>
                <div class="modal-body">
                    <p>
                        Data SK Yudisium <strong>{{ $programStudi }}</strong> untuk tanggal
                        <strong>{{ helper::tgl_indo_lengkap($date) }}</strong> akan direset.
                    </p>
                    <ul style="margin-bottom: 0; padding-left: 20px;">
                        <li>Nomor surat dan token verifikasi PDF dihapus.</li>
                        <li>Nomor alumni dan IPK seluruh peserta dihapus.</li>
                        <li>Nilai ujian dan jadwal tetap tersimpan.</li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    <form action="{{ route('fakultas.reset_data_sk_yudisium') }}" method="POST" style="display: inline-block;">
                        @csrf
                        <input type="hidden" name="tanggal_ujian" value="{{ $date }}">
                        <input type="hidden" name="kode_prodi" value="{{ $kodeProdi }}">
                        <input type="hidden" name="konfirmasi" value="RESET">
                        <button type="submit" class="btn btn-danger">
                            <i class="fa fa-undo"></i> Ya, Reset Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();

            var syncIpkUrl = {!! json_encode(route('fakultas.sinkronkan_ipk_sk_yudisium')) !!};
            var csrfToken = {!! json_encode(csrf_token()) !!};

            function kategoriYudisium(ipk) {
                if (ipk >= 3.51) return 'I';
                if (ipk >= 3.01) return 'II';
                if (ipk >= 2.76) return 'III';
                if (ipk >= 2.00) return 'IV';
                return '-';
            }

            function syncIpk($button) {
                var $row = $button.closest('tr');
                var $status = $row.find('.yudisium-ipk-status');

                $button.prop('disabled', true).find('i').removeClass('fa-cloud-download').addClass('fa-spinner fa-spin');
                $status.text('Menghubungi SIAKAD...');

                return $.ajax({
                    url: syncIpkUrl,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrfToken,
                        tanggal_ujian: '{{ $date }}',
                        kode_prodi: '{{ $kodeProdi }}',
                        nim: $button.data('nim')
                    }
                }).done(function(response) {
                    $row.find('.yudisium-ipk').val(response.ipk);
                    $row.find('.yudisium-category').text(response.kategori_yudisium || '-');
                    $status.removeClass('text-danger').addClass('text-muted')
                        .text(response.source + ' | ' + response.synced_at + ' | ' + response.total_sks + ' SKS');
                }).fail(function(xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'IPK tidak dapat ditarik dari SIAKAD.';
                    $status.text(message).removeClass('text-muted').addClass('text-danger');
                }).always(function() {
                    $button.prop('disabled', false).find('i').removeClass('fa-spinner fa-spin').addClass('fa-cloud-download');
                });
            }

            $('.yudisium-sync-ipk').on('click', function() {
                syncIpk($(this));
            });

            $('#continue-alumni-numbers').on('click', function() {
                var $inputs = $('.yudisium-alumni-number');
                var $status = $('#alumni-number-status');
                var firstNumber = $.trim($inputs.first().val());

                if (!/^[1-9][0-9]{0,8}$/.test(firstNumber)) {
                    $status.removeClass('text-success').addClass('text-danger')
                        .text('Isi nomor alumni peserta pertama dengan angka yang valid.');
                    $inputs.first().focus();
                    return;
                }

                var startNumber = parseInt(firstNumber, 10);
                $inputs.each(function(index) {
                    $(this).val(startNumber + index);
                });
                $status.removeClass('text-danger').addClass('text-success')
                    .text($inputs.length + ' nomor telah disusun berurutan dan masih dapat dikoreksi.');
            });

            $('#sync-all-yudisium-ipk').on('click', function() {
                var $allButton = $(this);
                var $allLabel = $allButton.find('.yudisium-sync-all-label');
                var $buttons = $('.yudisium-sync-ipk');
                var current = 0;
                var succeeded = 0;
                var failed = 0;

                if ($buttons.length === 0 || $allButton.prop('disabled')) {
                    return;
                }

                $allButton.prop('disabled', true).find('i').removeClass('fa-cloud-download').addClass('fa-spinner fa-spin');
                function next() {
                    if (current >= $buttons.length) {
                        $allButton.prop('disabled', false).find('i').removeClass('fa-spinner fa-spin').addClass('fa-cloud-download');
                        $allLabel.text('Tarik IPK SIAKAD (' + succeeded + ' berhasil, ' + failed + ' gagal)');
                        return;
                    }

                    var $button = $buttons.eq(current++);
                    syncIpk($button).done(function() {
                        succeeded++;
                    }).fail(function() {
                        failed++;
                    }).always(next);
                }

                next();
            });
        });
    </script>
@endsection
