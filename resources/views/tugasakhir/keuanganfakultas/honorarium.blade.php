@extends('tugasakhir.index')
@section('isi')
    <style>
        .modal-table td {
            vertical-align: middle;
        }

        .modal-table .label {
            display: inline-block;
            width: 100%;
            text-align: center;
            padding: 0.3em;
        }
    </style>
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
                <li class="active">Honorarium</li>
            </ol>
            <!-- End breadcrumb -->

            @if (session('status'))
                <div class="alert alert-{{ session('status') }} alert-block square fade in alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('message') }}
                </div>
            @endif

            <!-- BEGIN DATA TABLE -->
            <h3 class="page-heading">List of Honorarium Availability</h3>
            <div class="the-box">
                <div style="margin-bottom: 20px; text-align: right;">
                    <a href="{{ route('honorarium_history') }}" type="button" class="btn btn-primary">
                        <i class="fa fa-history"></i> History
                    </a>
                </div>

                <form action="{{ route('honorarium_save_all') }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table" id="datatable-example">
                            <thead class="the-box dark full">
                                <tr>
                                    <th>No</th>
                                    <th>Date</th>
                                    <th>Nim</th>
                                    <th>Student Name</th>
                                    <th>Available</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $honorarium)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $honorarium->date }}</td>
                                        <td>{{ $honorarium->C_NPM }}</td>
                                        <td>{{ helper::getNamaMhs($honorarium->C_NPM) }}</td>
                                        <td>
                                            <input type="checkbox" name="honorariums[{{ $loop->index }}][KS_Stat]"
                                                data-toggle="toggle" data-on="Yes" data-off="No"
                                                data-honorarium-id="{{ $honorarium->id }}"
                                                {{ $honorarium->KS_Stat != 0 ? 'checked' : '' }}
                                                {{ $honorarium->KS_Stat == 3 || $honorarium->PU_Stat == 3 || $honorarium->PP_Stat == 3 || $honorarium->P1_Stat == 3 || $honorarium->P2_Stat == 3 || $honorarium->P3_Stat == 3 ? 'disabled' : '' }}>
                                        </td>
                                        <td>
                                            <select class="form-control"
                                                name="honorariums[{{ $loop->index }}][tipe_ujian]"
                                                {{ ($honorarium->KS_Stat == 3 || $honorarium->PU_Stat == 3 || $honorarium->PP_Stat == 3 || $honorarium->P1_Stat == 3 || $honorarium->P2_Stat == 3 || $honorarium->P3_Stat == 3) && $honorarium->KS_Stat != 0 ? 'disabled' : '' }}>

                                                @if ($honorarium->tipe_ujian == '0' || $honorarium->tipe_ujian == '2')
                                                    <option value="unset">Unset</option>
                                                    @foreach ($dataMasterHonorarium as $masterHonorarium)
                                                        <option value="{{ $masterHonorarium->name }}">
                                                            {{ $masterHonorarium->name }}</option>
                                                    @endforeach
                                                @else
                                                    <option value="{{ $honorarium->tipe_ujian }}" selected>
                                                        {{ $honorarium->tipe_ujian }}</option>
                                                    <option disabled>-----</option>
                                                    @foreach ($dataMasterHonorarium as $masterHonorarium)
                                                        <option value="{{ $masterHonorarium->name }}">
                                                            {{ $masterHonorarium->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-primary btn-sm view-honorarium-btn"
                                                data-toggle="modal" data-target="#statusModal"
                                                data-honorarium-id="{{ $honorarium->id }}"
                                                data-ks="{{ helper::getDeskripsi($honorarium->KS) }}"
                                                data-pu="{{ helper::getDeskripsi($honorarium->PU) }}"
                                                data-pp="{{ helper::getDeskripsi($honorarium->PP) }}"
                                                data-p1="{{ helper::getDeskripsi($honorarium->P1) }}"
                                                data-p2="{{ helper::getDeskripsi($honorarium->P2) }}"
                                                data-p3="{{ helper::getDeskripsi($honorarium->P3) }}"
                                                data-ks-stat="{{ $honorarium->KS_Stat }}"
                                                data-pu-stat="{{ $honorarium->PU_Stat }}"
                                                data-pp-stat="{{ $honorarium->PP_Stat }}"
                                                data-p1-stat="{{ $honorarium->P1_Stat }}"
                                                data-p2-stat="{{ $honorarium->P2_Stat }}"
                                                data-p3-stat="{{ $honorarium->P3_Stat }}">
                                                <i class="fa fa-info-circle"></i> View
                                            </button>
                                        </td>
                                        <input type="hidden" name="honorariums[{{ $loop->index }}][id]"
                                            value="{{ $honorarium->id }}">
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div><!-- /.table-responsive -->
                    <div style="text-align: right; margin-top: 20px;">
                        <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Save</button>
                    </div>
                </form>
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
            </div><!-- /.the-box .default -->
            <!-- END DATA TABLE -->
        </div><!-- /.container-fluid -->
    </div><!-- /.page-content -->

    <!-- Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="statusModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Honorarium Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Name</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>KS</td>
                                <td id="modal-ks"></td>
                                <td id="modal-ks-status"></td>
                            </tr>
                            <tr>
                                <td>PU</td>
                                <td id="modal-pu"></td>
                                <td id="modal-pu-status"></td>
                            </tr>
                            <tr>
                                <td>PP</td>
                                <td id="modal-pp"></td>
                                <td id="modal-pp-status"></td>
                            </tr>
                            <tr>
                                <td>P1</td>
                                <td id="modal-p1"></td>
                                <td id="modal-p1-status"></td>
                            </tr>
                            <tr>
                                <td>P2</td>
                                <td id="modal-p2"></td>
                                <td id="modal-p2-status"></td>
                            </tr>
                            <tr>
                                <td>P3</td>
                                <td id="modal-p3"></td>
                                <td id="modal-p3-status"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            var datatable = $('#datatable-example').DataTable();

            function bindToggleEvents() {
                $('input[data-toggle="toggle"]').bootstrapToggle();

                $('input[data-toggle="toggle"]').off('change').on('change', function() {
                    var honorariumId = $(this).data('honorarium-id');
                    var isChecked = $(this).prop('checked');
                    var url = isChecked ? "{{ route('honorarium_available_post_yes') }}" :
                        "{{ route('honorarium_available_post_no') }}";

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id_honorarium: honorariumId
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Something went wrong!'
                            });
                            $(this).bootstrapToggle('toggle');
                        }
                    });
                });

                $('.view-honorarium-btn').off('click').on('click', function() {
                    var ks = $(this).data('ks');
                    var pu = $(this).data('pu');
                    var pp = $(this).data('pp');
                    var p1 = $(this).data('p1');
                    var p2 = $(this).data('p2');
                    var p3 = $(this).data('p3');

                    $('#modal-ks').text(ks);
                    $('#modal-pu').text(pu);
                    $('#modal-pp').text(pp);
                    $('#modal-p1').text(p1);
                    $('#modal-p2').text(p2);
                    $('#modal-p3').text(p3);

                    var ksStat = $(this).data('ks-stat');
                    var puStat = $(this).data('pu-stat');
                    var ppStat = $(this).data('pp-stat');
                    var p1Stat = $(this).data('p1-stat');
                    var p2Stat = $(this).data('p2-stat');
                    var p3Stat = $(this).data('p3-stat');

                    $('#modal-ks-status').html('<span class="' + (ksStat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (ksStat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-pu-status').html('<span class="' + (puStat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (puStat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-pp-status').html('<span class="' + (ppStat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (ppStat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-p1-status').html('<span class="' + (p1Stat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (p1Stat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-p2-status').html('<span class="' + (p2Stat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (p2Stat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                    $('#modal-p3-status').html('<span class="' + (p3Stat === 3 ? 'label label-success' :
                            'label label-danger') + '">' + (p3Stat === 3 ? 'Paid' : 'Unpaid') +
                        '</span>');
                });
            }

            bindToggleEvents();

            datatable.on('draw', function() {
                bindToggleEvents();
            });
        });
    </script>
@endsection
