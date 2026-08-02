@extends('tugasakhir.index')
@section('isi')
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
                <li><a href="{{ url('prodi/topik') }}">Topik Usulan</a></li>
                <li class="active">Riwayat Topik Usulan</li>
            </ol>
            <!-- End breadcrumb -->

            <h3 class="page-heading">Daftar Riwayat Usulan</h3>
            <div class="the-box">
                <form method="get" action="{{ url('prodi/topik/riwayat') }}" class="form-horizontal" style="margin-bottom: 18px;">
                    <div class="row">
                        <div class="col-md-5">
                            <label style="display:block;">Cari NIM / Nama / Topik</label>
                            <input type="text" name="q" class="form-control" value="{{ $q ?? '' }}" placeholder="Masukkan kata kunci">
                        </div>
                        <div class="col-md-2">
                            <label style="display:block;">Per Halaman</label>
                            <select name="per_page" class="form-control">
                                @foreach ([25, 50, 100, 200] as $size)
                                    <option value="{{ $size }}" {{ (int) ($perPage ?? 50) === $size ? 'selected' : '' }}>{{ $size }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label style="display:block;">Aksi</label>
                            <div>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filter</button>
                                <a href="{{ url('prodi/topik/riwayat') }}" class="btn btn-default">Reset</a>
                                <a href="{{ url('prodi/topik') }}" class="btn btn-info">Kembali ke Topik</a>
                            </div>
                        </div>
                    </div>
                </form>

                <div style="margin-bottom: 10px;">
                    Menampilkan <strong>{{ $data_riwayat_usulan->count() }}</strong> dari <strong>{{ $data_riwayat_usulan->total() }}</strong> riwayat usulan
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Topik</th>
                                <th>Jenis</th>
                                <th>Kerangka Pikir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($data_riwayat_usulan->count() === 0)
                                <tr>
                                    <td colspan="7" class="text-center">Data riwayat usulan tidak ditemukan.</td>
                                </tr>
                            @endif
                            @foreach ($data_riwayat_usulan as $key => $value)
                                <tr class="odd gradeX">
                                    <td width="1%" align="center">{{ $data_riwayat_usulan->firstItem() + $key }}</td>
                                    <td>{{ $value->C_NPM }}</td>
                                    <td>{{ $value->NAMA_MAHASISWA }}</td>
                                    <td>{{ $value->topik }}</td>
                                    <td>{{ $value->kode_jenis_tugas_akhir ?: '-' }}</td>
                                    <td>
                                        @if ($value->kerangka)
                                            <button class="btn btn-primary" onclick="showModal(this)"
                                                data-href="{{ asset('dokumen/' . $value->kerangka) }}"
                                                data-target="#modalPrimary" data-toggle="modal"><i class="fa fa-paperclip"></i>
                                            </button>
                                        @else
                                            <span class="badge badge-danger">Mahasiswa tidak mengupload kerangka pikir</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($value->status == 0)
                                            Belum dikonfirmasi
                                        @elseif($value->status == 1)
                                            Diterima
                                        @elseif($value->status == 2)
                                            Ditolak
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
                <div>
                    {{ $data_riwayat_usulan->links() }}
                </div>
            </div><!-- /.the-box .default -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

{{-- ModalSetUser --}}
@section('modalPrimaryTitle')
    Download
@endsection
@section('modalPrimaryBody')
    Download kerangka pikir?
@endsection
@section('modalPrimaryFooter')
    <button onclick="goOnNewTab(this)" class="btn btn-default">Download</button>
@endsection

@section('script')
    <script>
        let modal, modalId, link;
        const showModal = e => {
            link = e.getAttribute("data-href");
            modalId = e.getAttribute("data-target");
            modal = document.querySelector(modalId);
        };

        const goOnNewTab = () => {
            modal.querySelector(".modal-backdrop").click();
            window.open(link);
        };
    </script>
@endsection
