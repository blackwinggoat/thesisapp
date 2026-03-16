@extends('tugasakhir.index')
@section('isi')
    <div class="page-content">
        <div class="container-fluid">
            <h1 class="page-heading">Sistem Informasi Program Studi <small>Tugas Akhir</small></h1>

            <ol class="breadcrumb default square rsaquo sm">
                <li><a href="index.html"><i class="fa fa-home"></i></a></li>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li class="active">Dosen Pembimbing</li>
            </ol>

            <h3 class="page-heading">List of Lecturer</h3>
            <div class="the-box">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="datatable-example">
                        <thead class="the-box dark full">
                            <tr>
                                <th>No</th>
                                <th>NIDN</th>
                                <th>Nama</th>
                                <th>Custom Report</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->C_KODE_DOSEN }}</td>
                                    <td>{{ $item->NAMA_DOSEN }}</td>
                                    <td>
                                        <!-- Tombol Custom Report -->
                                        <button class="btn btn-primary"
                                                data-toggle="modal"
                                                data-target="#customReportModal"
                                                data-id="{{ $item->C_KODE_DOSEN }}">
                                            Custom Report
                                        </button>
                                    </td>
                                    <td>
                                        <a href="{{ route('report_dosen_detail', $item->C_KODE_DOSEN) }}" class="btn btn-primary">Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Custom Report -->
    <div class="modal fade" id="customReportModal" tabindex="-1" role="dialog" aria-labelledby="customReportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customReportModalLabel">Custom Report</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form untuk memasukkan Start Date dan End Date -->
                    <form id="customReportForm">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <input type="hidden" id="dosen_id" name="dosen_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="processReport">Process</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Set nilai dosen ID ketika tombol Custom Report diklik
        $('#customReportModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button yang diklik
            var dosenId = button.data('id'); // Ambil data-id dari button

            // Set nilai dosen ID di input hidden
            var modal = $(this);
            modal.find('#dosen_id').val(dosenId);
        });

        // Ketika tombol Process diklik
        $('#processReport').on('click', function () {
            var dosenId = $('#dosen_id').val();
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();

            if (startDate && endDate) {
                // Redirect ke route dengan parameter NIDN, Start Date, dan End Date
                var url = '/report/dosen/' + dosenId + '/' + startDate + '/' + endDate;
                window.location.href = url;
            } else {
                alert('Please select both start date and end date.');
            }
        });
    });
</script>
@endsection
