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
                <div class="col-md-8">
                    <h3 class="page-heading">Data SK Yudisium {{ $programStudi }}</h3>
                    <p class="text-muted" style="margin-top: -8px;">
                        Ujian Tugas Akhir tanggal {{ helper::tgl_indo_lengkap($date) }}
                    </p>
                </div>
                <div class="col-md-4 text-right" style="padding-top: 18px;">
                    <a href="{{ route('fakultas.rekap_ujian_selesai') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
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
                            placeholder="Contoh: 827/A.10/SI-FIK/UMI/VIII/2026">
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
                                            <input type="text" name="mahasiswa[{{ $index }}][nomor_alumni]" maxlength="30"
                                                class="form-control input-sm"
                                                value="{{ old('mahasiswa.' . $index . '.nomor_alumni', $mahasiswa->nomor_alumni) }}"
                                                placeholder="Nomor alumni">
                                        </td>
                                        <td>
                                            <input type="number" name="mahasiswa[{{ $index }}][ipk]" min="0" max="4" step="0.01"
                                                class="form-control input-sm yudisium-ipk"
                                                value="{{ $ipkValue }}" aria-label="IPK {{ $mahasiswa->nim }}">
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
@endsection

@section('script')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();

            function kategoriYudisium(ipk) {
                if (ipk >= 3.51) return 'I';
                if (ipk >= 3.01) return 'II';
                if (ipk >= 2.76) return 'III';
                if (ipk >= 2.00) return 'IV';
                return '-';
            }

            $('.yudisium-ipk').on('input', function() {
                var value = parseFloat($(this).val());
                $(this).closest('tr').find('.yudisium-category').text(isNaN(value) ? '-' : kategoriYudisium(value));
            });
        });
    </script>
@endsection
