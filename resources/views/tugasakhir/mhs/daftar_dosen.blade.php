@extends('tugasakhir.index')
@section('isi')

<div class="page-content">
    <div class="container-fluid">
        <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>
        <ol class="breadcrumb default square rsaquo sm">
            <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li class="active">Daftar Dosen</li>
        </ol>
        <h3 class="page-heading">Daftar Dosen</h3>
        <div class="the-box">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="datatable-dosen-mahasiswa">
                    <thead class="the-box dark full">
                    <tr>
                        <th>No</th>
                        <th>NIDN</th>
                        <th>Nama Dosen</th>
                        <th>Kontak</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($data as $key => $value)
                        <tr class="odd gradeX">
                            <td width="1%" align="center">{{ $key + 1 }}</td>
                            <td>{{ $value->nidn ?? '-' }}</td>
                            <td>{{ $value->nama_dosen ?? '-' }}</td>
                            <td>
                                @if (!empty($value->nomor_whatsapp))
                                    <a class="btn btn-success btn-xs" href="https://wa.me/{{ $value->nomor_whatsapp }}" target="_blank" rel="noopener noreferrer" title="Chat melalui WhatsApp" aria-label="Chat {{ $value->nama_dosen ?? 'dosen' }} melalui WhatsApp">
                                        <i class="fa fa-whatsapp"></i>
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data dosen.</td>
                        </tr>
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
        if ($.fn.DataTable && $('#datatable-dosen-mahasiswa').length > 0) {
            $('#datatable-dosen-mahasiswa').DataTable({
                paging: false,
                info: false,
                lengthChange: false
            });
        }
    });
</script>
@endsection
