@extends('tugasakhir.index')
@section('isi')
    <!-- BEGIN PAGE CONTENT -->
    <div class="page-content">
        <div class="container-fluid">
            <!-- Begin page heading -->
            <h1 class="page-heading">Sistem Informasi Program Studi <small>Tugas Akhir</small></h1>
            <!-- End page heading -->

            <!-- Begin breadcrumb -->
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/')}}">Home</a></li>
                <li><a href="{{ url('prodi/dosen_pembimbing')}}">Dosen Pembimbing</a></li>
                <li class="active">Detail Dosen Pembimbing</li>
            </ol>


            <h3 class="page-heading">Detail Dosen Pembimbing</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">

                <fieldset>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">NIDN</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="nidn" value="{{$data->C_KODE_DOSEN}}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Nama</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="nama" value="{{$data->NAMA_DOSEN}}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">No Handphone</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="noHp" value="{{ !empty($data->NO_HP) ? $data->NO_HP : (!empty($data->NO_TELP) ? $data->NO_TELP : '-') }}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Alamat</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="alamat" value="{{$data->ALAMAT}}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Jumlah Bimbingan</label>
                        <div class="col-lg-5">
                            <input type="text" class="form-control bold-border" name="jmlBimbingan" value="{{$total}}" disabled/>
                        </div>
                    </div>
                    <br><br>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Detail Bimbingan</label>
                        <div class="table-responsive col-lg-10">
                            <table class="table table-th-block">
                                <thead>
                                <tr>
                                    <th>Tahapan Mahasiswa Bimbingan</th>
                                    <th>Pembimbing Utama : Pembimbing Utama</th>
                                    <th>Pembimbing Pendamping : Pembimbing Pendamping</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Persiapan Proposal</td>
                                    <td>{{count($ppropI)}}</td>
                                    <td>{{count($ppropII)}}</td>
                                </tr>
                                <tr>
                                    <td>Persiapan Ujian Meja</td>
                                    <td>{{count($pmejaI)}}</td>
                                    <td>{{count($pmejaII)}}</td>
                                </tr>
                                <tr>
                                    <td>Lulusan</td>
                                    <td>{{count($alumniI)}}</td>
                                    <td>{{count($alumniII)}}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div><!-- /.table-responsive -->
                    </div>
                </fieldset>
            </div><!-- /.the-box -->
            <!-- End breadcrumb -->
            <h3 class="page-heading">Daftar Mahasiswa Bimbingan Aktif / Belum Lulus</h3>
            <!-- BEGIN DATA TABLE -->
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-bimbingan-aktif">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Peran Pembimbing</th>
                            <th>No SK Bimbingan</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Koordinasi Mahasiswa</th>
                            <th>Tahapan Bimbingan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($data_bimbingan_aktif as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{ $key + 1 }}</td>
                                <td>{{ $value->peran_pembimbing ?? '-' }}</td>
                                <td>
                                    @if (!empty($value->nomor_sk))
                                        <form action="{{ url('dsn/cetak_sk_pembimbing/') }}" method="post" target="_blank">
                                            @csrf
                                            <input type="hidden" name="nomor" value="{{ $value->nomor_sk }}">
                                            <button type="submit" class="btn btn-primary">{{ $value->nomor_sk }}</button>
                                        </form>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $value->C_NPM }}</td>
                                <td>{{ $value->NAMA_MAHASISWA ?? '-' }}</td>
                                <td>
                                    @php
                                        $waRaw = trim((string) ($value->no_wa ?? ''));
                                        $waDigits = preg_replace('/\D+/', '', $waRaw);
                                        if ($waDigits !== '' && strpos($waDigits, '0') === 0) {
                                            $waDigits = '62' . substr($waDigits, 1);
                                        }
                                        $waLink = $waDigits !== '' ? 'https://wa.me/' . $waDigits : '';

                                        $telegramRaw = trim((string) ($value->id_telegram ?? ''));
                                        $telegramUsername = ltrim($telegramRaw, '@');
                                        $telegramLink = $telegramUsername !== '' ? 'https://t.me/' . $telegramUsername : '';
                                        $laporanAktif = $laporanAktifByNim->get($value->C_NPM);
                                    @endphp

                                    <div style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center;">
                                        @if ($waLink !== '')
                                            <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm" title="Hubungi melalui WhatsApp" aria-label="Hubungi {{ $value->NAMA_MAHASISWA ?? 'mahasiswa' }} melalui WhatsApp">
                                                <i class="fa fa-whatsapp"></i>
                                            </a>
                                        @endif
                                        @if ($telegramLink !== '')
                                            <a href="{{ $telegramLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-info btn-sm" title="Hubungi melalui Telegram" aria-label="Hubungi {{ $value->NAMA_MAHASISWA ?? 'mahasiswa' }} melalui Telegram">
                                                <i class="fa fa-telegram"></i>
                                            </a>
                                        @endif
                                        @if ($canLaporKeProdi)
                                            @if ($laporanAktif)
                                                <a href="{{ url('dsn/laporan_mahasiswa/' . $laporanAktif->laporan_mahasiswa_id) }}" class="btn btn-primary btn-sm" title="Lanjutkan laporan ke Prodi">
                                                    <i class="fa fa-comments"></i> Lihat Laporan
                                                </a>
                                            @else
                                                <button type="button" class="btn btn-warning btn-sm btn-lapor-prodi" data-toggle="modal" data-target="#modalLaporProdi" data-nim="{{ $value->C_NPM }}" data-nama="{{ $value->NAMA_MAHASISWA ?? '-' }}" title="Laporkan kepada Program Studi">
                                                    <i class="fa fa-flag"></i> Lapor Prodi
                                                </button>
                                            @endif
                                        @endif
                                        @if ($waLink === '' && $telegramLink === '' && !$canLaporKeProdi)
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $value->label_status_bimbingan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada mahasiswa bimbingan aktif / belum lulus.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->

            @if ($canLaporKeProdi)
                <div class="modal fade" id="modalLaporProdi" tabindex="-1" role="dialog" aria-labelledby="modalLaporProdiLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="post" action="{{ url('dsn/laporan_mahasiswa') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="C_NPM" id="laporProdiNim" value="{{ old('C_NPM') }}">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="modalLaporProdiLabel">Lapor ke Program Studi</h4>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted" id="laporProdiMahasiswa"></p>
                                    <div class="form-group">
                                        <label>Kategori</label>
                                        <select class="form-control" name="kategori" required>
                                            <option value="">Pilih kategori</option>
                                            @foreach ($kategoriLaporanMahasiswa as $kodeKategori => $labelKategori)
                                                <option value="{{ $kodeKategori }}" {{ old('kategori') === $kodeKategori ? 'selected' : '' }}>{{ $labelKategori }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Perihal</label>
                                        <input type="text" class="form-control" name="perihal" value="{{ old('perihal') }}" maxlength="255" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Uraian Laporan</label>
                                        <textarea class="form-control" name="uraian" rows="5" required>{{ old('uraian') }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning"><i class="fa fa-paper-plane"></i> Kirim ke Prodi</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <h3 class="page-heading">Daftar Mahasiswa Lulusan</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-bimbingan-lulusan">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Peran Pembimbing</th>
                            <th>No SK Bimbingan</th>
                            <th>NIM</th>
                            <th>Nama</th>
                            <th>Kontak Mahasiswa</th>
                            <th>Tahapan Bimbingan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($data_bimbingan_lulusan as $key => $value)
                            <tr class="odd gradeX">
                                <td width="1%" align="center">{{ $key + 1 }}</td>
                                <td>{{ $value->peran_pembimbing ?? '-' }}</td>
                                <td>
                                    @if (!empty($value->nomor_sk))
                                        <form action="{{ url('dsn/cetak_sk_pembimbing/') }}" method="post" target="_blank">
                                            @csrf
                                            <input type="hidden" name="nomor" value="{{ $value->nomor_sk }}">
                                            <button type="submit" class="btn btn-primary">{{ $value->nomor_sk }}</button>
                                        </form>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $value->C_NPM }}</td>
                                <td>{{ $value->NAMA_MAHASISWA ?? '-' }}</td>
                                <td>
                                    @php
                                        $waRaw = trim((string) ($value->no_wa ?? ''));
                                        $waDigits = preg_replace('/\D+/', '', $waRaw);
                                        if ($waDigits !== '' && strpos($waDigits, '0') === 0) {
                                            $waDigits = '62' . substr($waDigits, 1);
                                        }
                                        $waLink = $waDigits !== '' ? 'https://wa.me/' . $waDigits : '';

                                        $telegramRaw = trim((string) ($value->id_telegram ?? ''));
                                        $telegramUsername = ltrim($telegramRaw, '@');
                                        $telegramLink = $telegramUsername !== '' ? 'https://t.me/' . $telegramUsername : '';
                                    @endphp

                                    @if ($waLink !== '' || $telegramLink !== '')
                                        <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-start;">
                                            @if ($waLink !== '')
                                                <a href="{{ $waLink }}" target="_blank" class="btn btn-success btn-sm" style="min-width: 125px; text-align: left;">
                                                    <i class="fa fa-whatsapp" style="font-size: 16px; margin-right: 6px;"></i> WhatsApp
                                                </a>
                                            @endif
                                            @if ($telegramLink !== '')
                                                <a href="{{ $telegramLink }}" target="_blank" class="btn btn-info btn-sm" style="min-width: 125px; text-align: left;">
                                                    <i class="fa fa-telegram" style="font-size: 16px; margin-right: 6px;"></i> Telegram
                                                </a>
                                            @endif
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $value->label_status_bimbingan ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada mahasiswa lulusan.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div>

@endsection

@section('script')
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable && $('#datatable-bimbingan-aktif').length > 0) {
                $('#datatable-bimbingan-aktif').DataTable({
                    paging: false,
                    info: false,
                    lengthChange: false
                });
            }

            if ($.fn.DataTable && $('#datatable-bimbingan-lulusan').length > 0) {
                $('#datatable-bimbingan-lulusan').DataTable({
                    pageLength: 10
                });
            }

            $('.btn-lapor-prodi').on('click', function() {
                $('#laporProdiNim').val($(this).data('nim'));
                $('#laporProdiMahasiswa').text($(this).data('nama') + ' (' + $(this).data('nim') + ')');
            });

            @if (old('C_NPM'))
                $('#modalLaporProdi').modal('show');
            @endif
        });
    </script>
@endsection
