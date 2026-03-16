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
                <li><a href="index.html"><i class="fa fa-home"></i></a></li>
                <li><a href="#fakelink">Home</a></li>
                <li class="active">Master Pembayaran</li>
            </ol>
            <!-- End breadcrumb -->

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">Master Tipe Pembayaran</h3>
            <div class="the-box">
                <div style="margin-bottom: 20px; text-align: right;">
                    <!-- Button to Open the Modal -->
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addTypeModal">
                        <i class="fa fa-plus"></i> Add Type
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>KS</th>
                                <th>PU</th>
                                <th>PP</th>
                                <th>P1</th>
                                <th>P2</th>
                                <th>P3</th>
                                <th style="text-align: center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $p)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $p->name }}</td>
                                    <td>{{ helper::formatRupiah($p->ketua_sidang) }}</td>
                                    <td>{{ helper::formatRupiah($p->pembimbing_utama) }}</td>
                                    <td>{{ helper::formatRupiah($p->pembimbing_pendamping) }}</td>
                                    <td>{{ helper::formatRupiah($p->penguji_1) }}</td>
                                    <td>{{ helper::formatRupiah($p->penguji_2) }}</td>
                                    <td>{{ helper::formatRupiah($p->penguji_3) }}</td>
                                    <td style="text-align: center">
                                        <button type="button" class="btn btn-primary btn-sm" title="Edit"
                                            data-toggle="modal" data-target="#editTypeModal"
                                            data-id="{{ $p->id_honorarium }}" data-name="{{ $p->name }}"
                                            data-ks="{{ $p->ketua_sidang }}" data-pu="{{ $p->pembimbing_utama }}"
                                            data-pp="{{ $p->pembimbing_pendamping }}" data-p1="{{ $p->penguji_1 }}"
                                            data-p2="{{ $p->penguji_2 }}" data-p3="{{ $p->penguji_3 }}">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <a href="{{ route('master_pembayaran_delete', $p->id_honorarium) }}"
                                            class="btn btn-danger btn-sm" title="Delete"
                                            onclick="return confirm('Apakah Anda Yakin?')">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- Status atau Keterangan --}}
                    <div>
                        <small>
                            <p>KS = Ketua Sidang</p>
                            <p>PU = Pembimbing Utama</p>
                            <p>PP = Pembimbing Pendamping</p>
                            <p>P1 = Penguji I</p>
                            <p>P2 = Penguji II</p>
                            <p>P3 = Penguji III</p>
                        </small>
                    </div>
                </div><!-- /.table-responsive -->
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div><!-- /.page-content -->
    <!-- The Modal for Editing -->
    <div class="modal fade" id="editTypeModal" tabindex="-1" role="dialog" aria-labelledby="editTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTypeModalLabel">Edit Manajemen Honorarium</h5>
                </div>
                <form action="{{ route('master_pembayaran_update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_honorarium" id="edit_id_honorarium">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_name">Nama Tipe Pembayaran</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_ks">Ketua Sidang</label>
                            <input type="text" class="form-control" id="edit_ks" name="ketua_sidang" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_pu">Pembimbing Utama</label>
                            <input type="text" class="form-control" id="edit_pu" name="pembimbing_utama" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_pp">Pembimbing Pendamping</label>
                            <input type="text" class="form-control" id="edit_pp" name="pembimbing_pendamping" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_p1">Penguji I</label>
                            <input type="text" class="form-control" id="edit_p1" name="penguji_1" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_p2">Penguji II</label>
                            <input type="text" class="form-control" id="edit_p2" name="penguji_2" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_p3">Penguji III</label>
                            <input type="text" class="form-control" id="edit_p3" name="penguji_3" required>
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

    <!-- The Modal -->
    <div class="modal fade" id="addTypeModal" tabindex="-1" role="dialog" aria-labelledby="addTypeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addTypeModalLabel">Manajemen Honorarium</h5>
                </div>
                <form action="{{ route('master_pembayaran_store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Nama Tipe Pembayaran</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Proposal" required>
                        </div>
                        <div class="form-group">
                            <label for="ketua_sidang">Ketua Sidang</label>
                            <input type="text" class="form-control" id="ketua_sidang" name="ketua_sidang"
                                placeholder="100000" required>
                        </div>
                        <div class="form-group">
                            <label for="pembimbing_utama">Pembimbing Utama</label>
                            <input type="text" class="form-control" id="pembimbing_utama" name="pembimbing_utama"
                                placeholder="100000" required>
                        </div>
                        <div class="form-group">
                            <label for="pembimbing_pendamping">Pembimbing Pendamping</label>
                            <input type="text" class="form-control" id="pembimbing_pendamping"
                                name="pembimbing_pendamping" placeholder="100000" required>
                        </div>
                        <div class="form-group">
                            <label for="penguji_1">Penguji I</label>
                            <input type="text" class="form-control" id="penguji_1" name="penguji_1"
                                placeholder="100000" required>
                        </div>
                        <div class="form-group">
                            <label for="penguji_2">Penguji II</label>
                            <input type="text" class="form-control" id="penguji_2" name="penguji_2"
                                placeholder="100000" required>
                        </div>
                        <div class="form-group">
                            <label for="penguji_3">Penguji III</label>
                            <input type="text" class="form-control" id="penguji_3" name="penguji_3"
                                placeholder="100000" required>
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
        $('#editTypeModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id')
            var name = button.data('name')
            var ks = button.data('ks')
            var pu = button.data('pu')
            var pp = button.data('pp')
            var p1 = button.data('p1')
            var p2 = button.data('p2')
            var p3 = button.data('p3')

            var modal = $(this)
            modal.find('.modal-title').text('Edit Manajemen Honorarium: ' + name)
            modal.find('#edit_id_honorarium').val(id)
            modal.find('#edit_name').val(name)
            modal.find('#edit_ks').val(ks)
            modal.find('#edit_pu').val(pu)
            modal.find('#edit_pp').val(pp)
            modal.find('#edit_p1').val(p1)
            modal.find('#edit_p2').val(p2)
            modal.find('#edit_p3').val(p3)
        })
    </script>
@endsection
