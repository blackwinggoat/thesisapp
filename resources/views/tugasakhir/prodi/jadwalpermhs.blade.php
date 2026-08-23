@extends('tugasakhir.index')
@section('isi')
    @php
        $isRiwayat = isset($isRiwayat) ? $isRiwayat : false;
        $tipeUjian = isset($tipe_ujian) ? $tipe_ujian : 'ujianmeja';
        $namaTipeUjian = $tipeUjian == 'proposal' ? 'Proposal' : ($tipeUjian == 'seminarhasil' ? 'Seminar Hasil' : 'Ujian Meja');
        $pageTitle = $isRiwayat
            ? 'Riwayat Jadwal ' . $namaTipeUjian . ' Per Mahasiswa'
            : 'Daftar Jadwal ' . $namaTipeUjian . ' Per Mahasiswa';
    @endphp
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
                <li class="active">{{$pageTitle}}</li>
            </ol>
            <!-- End breadcrumb -->

            <!-- BEGIN DATA TABLE -->
            <div class="clearfix">
                <h3 class="page-heading pull-left">{{$pageTitle}}</h3>
                <div class="pull-right" style="margin-top: 15px;">
                    @if($isRiwayat)
                        <a href="{{ url('prodi/jadwalpermhs/'.$tipeUjian) }}" class="btn btn-primary btn-perspective">
                            <i class="fa fa-calendar"></i> Jadwal Berjalan
                        </a>
                    @else
                        <a href="{{ url('prodi/jadwalpermhs/'.$tipeUjian.'/riwayat') }}" class="btn btn-default btn-perspective">
                            <i class="fa fa-history"></i> Lihat Riwayat
                        </a>
                    @endif
                </div>
            </div>
            <div class="the-box">
                <form method="post" action="{{ url('prodi/jadwalpermhs/'.$tipeUjian.'/rekap') }}" id="rekap-jadwal-form">
                    {{ csrf_field() }}
                    <div class="clearfix" style="margin-bottom: 12px;">
                        <div class="pull-left">
                            <button type="button" class="btn btn-default btn-perspective" id="check-all-jadwal">
                                <i class="fa fa-check-square-o"></i> Checklist Semua
                            </button>
                        </div>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-info btn-perspective" formaction="{{ url('prodi/jadwalpermhs/'.$tipeUjian.'/notif-dosen') }}">
                                <i class="fa fa-whatsapp"></i> Notif Dosen
                            </button>
                            <button type="submit" class="btn btn-success btn-perspective">
                                <i class="fa fa-file-excel-o"></i> Rekap Jadwal
                            </button>
                        </div>
                    </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                        <tr>
                            <th width="1%">
                                <input type="checkbox" id="jadwal-check-master" aria-label="Pilih semua jadwal">
                            </th>
                            <th>No</th>
                            <th>Tanggal Ujian</th>
                            <th>Nama Periode</th>
                            <th>Kuota</th>
                            <th>Jumlah Peserta</th>
                            {{-- <th>Status Ujian</th> --}}
                            <th>Detail Peserta</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($data as $key => $value)
                            <tr class="odd gradeX">
                                <td align="center">
                                    <input type="checkbox" class="jadwal-check" name="jadwal_ujian_ids[]" value="{{$value->jadwal_ujian_id}}" aria-label="Pilih jadwal {{$value->nama_periode}}">
                                </td>
                                <td width="1%" align="center">{{++$key}}</td>
                                <td>{{$value->tgl_ujian}}</td>
                                <td>{{$value->nama_periode}}</td>
                                <td>{{$value->kuota}}</td>
                                <td>{{$value->jml_peserta}}</td>
                                {{-- <<td>{{$value->status == 0 ? "td>{{$value->status == 0 ? "<td>{{$d->status == 0 ? "Belum terlaksana" : "Terlaksana"}}</td>" : "Terlaksana"}}</td>" : "Terlaksana"}}</td> --}}
                                <td><a href="{{ url('prodi/detail_jadwalpermhs/'.$value->pendaftaran_id)}}"><i class="fa fa-copy icon-square icon-xs icon-primary"></i></a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    @if($isRiwayat)
                                        Belum ada riwayat jadwal ujian yang tanggalnya sudah lewat.
                                    @else
                                        Belum ada jadwal ujian hari ini atau yang akan datang.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div><!-- /.table-responsive -->
                </form>
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div>
@endsection

@section('script')
    <script>
        (function () {
            var master = document.getElementById('jadwal-check-master');
            var button = document.getElementById('check-all-jadwal');
            var form = document.getElementById('rekap-jadwal-form');

            function boxes() {
                return Array.prototype.slice.call(document.querySelectorAll('.jadwal-check'));
            }

            function setAll(checked) {
                boxes().forEach(function (box) {
                    box.checked = checked;
                });
                if (master) {
                    master.checked = checked;
                }
            }

            if (master) {
                master.addEventListener('change', function () {
                    setAll(master.checked);
                });
            }

            if (button) {
                button.addEventListener('click', function () {
                    var shouldCheck = boxes().some(function (box) {
                        return !box.checked;
                    });
                    setAll(shouldCheck);
                });
            }

            if (form) {
                form.addEventListener('submit', function (event) {
                    var hasChecked = boxes().some(function (box) {
                        return box.checked;
                    });
                    if (!hasChecked) {
                        event.preventDefault();
                        alert('Pilih minimal satu jadwal terlebih dahulu.');
                    }
                });
            }
        })();
    </script>
@endsection
