@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading">Sistem Informasi Program Studi <small> TUGAS AKHIR</small></h1>
            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('prodi/jadwalpermhs/'.$tipe_ujian) }}">Jadwal Per Mahasiswa</a></li>
                <li class="active">Notif Dosen</li>
            </ol>

            <h3 class="page-heading">Notif Dosen Jadwal {{ $namaTipeUjian }}</h3>
            <div class="the-box">
                <div class="clearfix" style="margin-bottom: 12px;">
                    <a href="{{ url('prodi/jadwalpermhs/'.$tipe_ujian) }}" class="btn btn-default btn-perspective">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="the-box dark full">
                        <tr>
                            <th>No</th>
                            <th>Dosen</th>
                            <th>Jumlah Jadwal</th>
                            <th>WhatsApp</th>
                            <th>Link Rekap</th>
                            <th>Pesan</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($notifications as $index => $notification)
                            <tr>
                                <td width="1%" align="center">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $notification['nama_dosen'] }}</strong><br>
                                    <small>{{ $notification['kode_dosen'] }}</small>
                                </td>
                                <td align="center">{{ count($notification['rows']) }}</td>
                                <td>
                                    @if($notification['wa_url'] !== '')
                                        <a href="{{ $notification['wa_url'] }}" class="btn btn-success btn-sm" target="_blank" rel="noopener noreferrer">
                                            <i class="fa fa-whatsapp"></i> Kirim WA
                                        </a>
                                    @else
                                        <span class="label label-warning">Nomor WA belum tersedia</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ $notification['schedule_url'] }}" class="btn btn-info btn-sm" target="_blank" rel="noopener noreferrer">
                                        <i class="fa fa-link"></i> Buka Link
                                    </a>
                                </td>
                                <td>
                                    <textarea class="form-control" rows="6" readonly>{{ $notification['message'] }}</textarea>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada dosen pada jadwal yang dipilih.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
