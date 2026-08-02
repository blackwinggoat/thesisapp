@extends('tugasakhir.index')
@section('isi')

<div class="page-content">
    <div class="container-fluid">
        @php
            $statusClass = $laporan->status === 'selesai' ? 'success' : ($laporan->status === 'ditinjau' ? 'info' : 'warning');
            $statusLabel = $laporan->status === 'selesai' ? 'Selesai' : ($laporan->status === 'ditinjau' ? 'Ditinjau' : 'Baru');
        @endphp
        <h1 class="page-heading">Diskusi Laporan <small>Mahasiswa Bimbingan</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('dsn/laporan_mahasiswa') }}">Laporan ke Prodi</a></li>
            <li class="active">Diskusi</li>
        </ol>

        @if (session('success'))
            <div class="alert alert-success"><strong>Berhasil! </strong>{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger"><strong>Perhatian! </strong>{{ session('error') }}</div>
        @endif

        <div class="the-box">
            <table class="table table-th-block">
                <tbody>
                <tr><th>Mahasiswa</th><td>{{ $laporan->NAMA_MAHASISWA ?? '-' }} ({{ $laporan->C_NPM }})</td></tr>
                <tr><th>Program Studi</th><td>{{ $laporan->nama_prodi ?? $laporan->C_KODE_PRODI }}</td></tr>
                <tr><th>Kategori</th><td>{{ ucfirst(str_replace('_', ' ', $laporan->kategori)) }}</td></tr>
                <tr><th>Perihal</th><td>{{ $laporan->perihal }}</td></tr>
                <tr><th>Status</th><td><span class="label label-{{ $statusClass }}">{{ $statusLabel }}</span></td></tr>
                <tr><th>Laporan Awal</th><td>{!! nl2br(e($laporan->uraian)) !!}</td></tr>
                </tbody>
            </table>
            @if (!empty($laporan->tindakan_terakhir))
                <div class="alert alert-info"><strong>Tindakan Prodi:</strong> {!! nl2br(e($laporan->tindakan_terakhir)) !!}</div>
            @endif
        </div>

        <div class="the-box">
            <h4>Riwayat Diskusi</h4>
            @forelse ($pesan as $item)
                <div class="media" style="border-bottom: 1px solid #e5e5e5; padding: 12px 0;">
                    <div class="media-left"><i class="fa {{ $item->pengirim_peran === 'prodi' ? 'fa-university text-primary' : 'fa-user text-success' }} fa-lg"></i></div>
                    <div class="media-body">
                        <strong>{{ $item->pengirim_peran === 'prodi' ? 'Program Studi' : 'Dosen Pembimbing' }}</strong>
                        <small class="text-muted">{{ $item->created_at }}</small>
                        <div>{!! nl2br(e($item->isi_pesan)) !!}</div>
                    </div>
                </div>
            @empty
                <p class="text-muted">Belum ada respons dari Program Studi.</p>
            @endforelse
        </div>

        @if ($laporan->status !== 'selesai')
            <div class="the-box">
                <form method="post" action="{{ url('dsn/laporan_mahasiswa/' . $laporan->laporan_mahasiswa_id . '/pesan') }}">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label>Pesan untuk Program Studi</label>
                        <textarea class="form-control" name="pesan" rows="4" required>{{ old('pesan') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Kirim Pesan</button>
                </form>
            </div>
        @else
            <div class="alert alert-success">Laporan ini telah selesai. Riwayat diskusi tetap dapat dilihat.</div>
        @endif
    </div>
</div>

@endsection
