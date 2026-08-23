@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading thesis-page-heading">Thesis App <small>FIKOM UMI</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="{{ url('/') }}"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">Sanksi Pembayaran</li>
            </ol>

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <h3 class="page-heading">Master Sanksi Pembayaran</h3>
            <div class="the-box">
                <div style="margin-bottom: 20px; text-align: right;">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addSanksiModal">
                        <i class="fa fa-plus"></i> Tambah Sanksi
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Jumlah Sanksi</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $sanksi)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><strong>{{ helper::formatRupiah($sanksi->jumlah_sanksi) }}</strong></td>
                                    <td>{{ $sanksi->tanggal_mulai }}</td>
                                    <td>{{ $sanksi->tanggal_selesai }}</td>
                                    <td style="text-align: center">
                                        <button type="button" class="btn btn-primary btn-sm edit-sanksi-button"
                                            title="Edit" data-toggle="modal" data-target="#editSanksiModal"
                                            data-id="{{ $sanksi->id_sanksi_pembayaran }}"
                                            data-jumlah="{{ $sanksi->jumlah_sanksi }}"
                                            data-tanggal-mulai="{{ $sanksi->tanggal_mulai }}"
                                            data-tanggal-selesai="{{ $sanksi->tanggal_selesai }}">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <a href="{{ route('sanksi_pembayaran_delete', $sanksi->id_sanksi_pembayaran) }}"
                                            class="btn btn-danger btn-sm" title="Delete"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus sanksi pembayaran ini?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addSanksiModal" tabindex="-1" role="dialog" aria-labelledby="addSanksiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSanksiModalLabel">Tambah Sanksi Pembayaran</h5>
                </div>
                <form action="{{ route('sanksi_pembayaran_store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="jumlah_sanksi">Jumlah Uang Sanksi</label>
                            <input type="number" min="0" step="1000" class="form-control" id="jumlah_sanksi"
                                name="jumlah_sanksi" placeholder="100000" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_mulai">Tanggal Mulai Berlaku</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal_selesai">Tanggal Selesai Berlaku</label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSanksiModal" tabindex="-1" role="dialog" aria-labelledby="editSanksiModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSanksiModalLabel">Edit Sanksi Pembayaran</h5>
                </div>
                <form action="{{ route('sanksi_pembayaran_update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_sanksi_pembayaran" id="edit_id_sanksi_pembayaran">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_jumlah_sanksi">Jumlah Uang Sanksi</label>
                            <input type="number" min="0" step="1000" class="form-control" id="edit_jumlah_sanksi"
                                name="jumlah_sanksi" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_tanggal_mulai">Tanggal Mulai Berlaku</label>
                            <input type="date" class="form-control" id="edit_tanggal_mulai" name="tanggal_mulai" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_tanggal_selesai">Tanggal Selesai Berlaku</label>
                            <input type="date" class="form-control" id="edit_tanggal_selesai" name="tanggal_selesai" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('.edit-sanksi-button').on('click', function() {
                var button = $(this);
                $('#edit_id_sanksi_pembayaran').val(button.data('id'));
                $('#edit_jumlah_sanksi').val(button.data('jumlah'));
                $('#edit_tanggal_mulai').val(button.data('tanggal-mulai'));
                $('#edit_tanggal_selesai').val(button.data('tanggal-selesai'));
            });
        });
    </script>
@endsection
