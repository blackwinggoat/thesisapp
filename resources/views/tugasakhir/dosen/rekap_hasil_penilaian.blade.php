@extends('tugasakhir.index')

@section('isi')
<style>
    .assessment-recap-date { margin: 24px 0 8px; font-size: 16px; font-weight: 700; }
    .assessment-recap-table { margin-bottom: 22px; min-width: 1460px; }
    .assessment-recap-table th { background: #ffff00; color: #1f2933; text-align: center; vertical-align: middle !important; }
    .assessment-recap-table tbody tr,
    .assessment-recap-table tbody tr:nth-of-type(odd),
    .assessment-recap-table tbody tr:nth-of-type(even),
    .assessment-recap-table tbody td,
    .assessment-recap-table tbody td.marker { background: #ffffff !important; }
    .assessment-recap-table td { vertical-align: middle !important; }
    .assessment-recap-table .marker { font-weight: 700; }
    .assessment-recap-table .student-title { min-width: 240px; text-align: left; }
    @media print {
        .assessment-recap-toolbar, .main-sidebar, .top-navbar, .sidebar, .navbar { display: none !important; }
        .page-content, .container-fluid { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .assessment-recap-table { font-size: 9px; min-width: 0; }
        .assessment-recap-date { break-after: avoid; page-break-after: avoid; }
        .table-responsive { overflow: visible !important; }
    }
</style>

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url($backPath) }}">{{ $namaUjian }}</a></li>
            <li class="active">Rekap</li>
        </ol>

        <div class="clearfix assessment-recap-toolbar" style="margin-bottom: 12px;">
            <h3 class="page-heading pull-left" style="margin: 0;">Rekap Penilaian {{ $namaUjian }}</h3>
            <div class="pull-right">
                <button class="btn btn-success btn-sm" type="button" onclick="window.print()"><i class="fa fa-print"></i> Cetak / PDF</button>
                <a href="{{ url($backPath) }}" class="btn btn-default btn-sm"><i class="fa fa-arrow-left"></i> Kembali</a>
            </div>
        </div>

        @forelse($rekapPerTanggal as $tanggal => $jadwalHariIni)
            <div class="assessment-recap-date">Jadwal Ujian: {{ $tanggal === 'tanpa-jadwal' ? 'Belum tersedia' : helper::tgl_indo_lengkap($tanggal) }}</div>
            <div class="table-responsive">
                <table class="table table-bordered assessment-recap-table">
                    <thead>
                        <tr>
                            <th>Ruangan</th>
                            <th>Jam</th>
                            <th>NIM</th>
                            <th>Mahasiswa</th>
                            <th>Judul</th>
                            <th>Pembimbing Utama</th>
                            <th>Pembimbing Pendamping</th>
                            <th>Penguji I</th>
                            <th>Penguji II</th>
                            <th>Penguji III</th>
                            <th>Ketua Sidang</th>
                            <th>Jenis TA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jadwalHariIni as $d)
                            <tr>
                                <td>{{ $d->nama_ruangan ?: '-' }}</td>
                                <td>{{ $d->jam_ujian_label ?: '-' }}</td>
                                <td>{{ $d->C_NPM }}</td>
                                <td>{{ $d->NAMA_MAHASISWA }}</td>
                                <td class="student-title">{!! helper::jenisTugasAkhirBadge($d->jenis_tugas_akhir_id ?? null) !!} {{ $d->judul ?: '-' }}</td>
                                @foreach(['pembimbing_I_id', 'pembimbing_II_id', 'penguji_I_id', 'penguji_II_id', 'penguji_III_id', 'ketua_sidang_id'] as $peran)
                                    <td class="{{ isset($d->highlight_roles[$peran]) ? 'marker' : '' }}">{{ $d->tim_ujian_by_peran[$peran] ?? '-' }}</td>
                                @endforeach
                                <td>{!! helper::jenisTugasAkhirBadge($d->jenis_tugas_akhir_id ?? null) !!}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="alert alert-info">Tidak ada mahasiswa yang muncul pada daftar penilaian {{ strtolower($namaUjian) }}.</div>
        @endforelse
    </div>
</div>
@endsection
