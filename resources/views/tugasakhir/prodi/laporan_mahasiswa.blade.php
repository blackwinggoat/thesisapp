@extends('tugasakhir.index')
@section('isi')

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading">Laporan Mahasiswa <small>Koordinasi Dosen</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li class="active">Laporan Mahasiswa</li>
        </ol>

        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-laporan-prodi">
                    <thead class="the-box dark full">
                    <tr>
                        <th>No</th>
                        <th>Mahasiswa</th>
                        <th>Dosen Pelapor</th>
                        <th>Kategori</th>
                        <th>Perihal</th>
                        <th>Status</th>
                        <th>Diperbarui</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($laporan as $key => $item)
                        @php
                            $statusClass = $item->status === 'selesai' ? 'success' : ($item->status === 'ditinjau' ? 'info' : 'warning');
                            $statusLabel = $item->status === 'selesai' ? 'Selesai' : ($item->status === 'ditinjau' ? 'Ditinjau' : 'Baru');
                        @endphp
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->NAMA_MAHASISWA ?? '-' }}<br><small class="text-muted">{{ $item->C_NPM }}</small></td>
                            <td>{{ $item->NAMA_DOSEN ?? $item->C_KODE_DOSEN }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $item->kategori)) }}</td>
                            <td>{{ $item->perihal }}</td>
                            <td><span class="label label-{{ $statusClass }}">{{ $statusLabel }}</span></td>
                            <td>{{ $item->updated_at }}</td>
                            <td><a href="{{ url('prodi/laporan_mahasiswa/' . $item->laporan_mahasiswa_id) }}" class="btn btn-primary btn-xs" title="Buka tindak lanjut"><i class="fa fa-comments"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center">Belum ada laporan dari dosen pembimbing.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable && $('#datatable-laporan-prodi').length > 0) {
            $('#datatable-laporan-prodi').DataTable({ paging: false, info: false, lengthChange: false });
        }
    });
</script>
@endsection
